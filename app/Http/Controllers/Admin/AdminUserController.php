<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\HealthRecord;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            })
            ->withCount('healthRecords', 'aiAnalyses')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Patients', [
            'patients' => $patients,
            'filters'  => ['search' => $search],
        ]);
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'healthRecords' => fn($q) => $q->latest('recorded_at')->take(50),
            'aiAnalyses'    => fn($q) => $q->latest()->take(10),
        ]);

        $patient->setRelation('aiAnalyses', $patient->aiAnalyses->map(function ($analysis) {
            $analysis->share_url = $analysis->makeShareUrl(30);
            return $analysis;
        }));

        $eduVideos = Cache::get('edukasi_videos_all', []);

        return Inertia::render('Admin/UserDetail', [
            'user'     => $patient,
            'records'  => $patient->healthRecords,
            'analyses' => $patient->aiAnalyses,
            'eduVideos'=> $eduVideos,
        ]);
    }

    public function edit(Patient $patient)
    {
        return Inertia::render('Admin/PatientEdit', [
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender'        => 'nullable|in:male,female',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
        ]);

        $patient->update($validated);

        return redirect()
            ->route('admin.patients.show', $patient)
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return back()->with('success', 'Pasien berhasil dihapus.');
    }

    public function destroyRecord(Patient $patient, HealthRecord $healthRecord)
    {
        if ((int) $healthRecord->patient_id !== (int) $patient->id) {
            abort(404);
        }

        $healthRecord->delete();

        return back()->with('success', 'Riwayat kesehatan berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            })
            ->withCount('healthRecords', 'aiAnalyses')
            ->with(['healthRecords' => function ($query) {
                $query->latest('recorded_at')->take(1);
            }])
            ->latest()
            ->get();

        $excelFileName = 'pasien_pemeriksaan_terbaru_' . now()->format('Ymd_His') . '.xlsx';

        $data = [
            ['<style font-size="16"><b>INSTALASI REKAM MEDIS & INFORMASI KESEHATAN (MEDIX)</b></style>'],
            ['<style font-size="11">Faskes Tingkat Pertama Pemantauan Pasien Terpadu - Sistem Informasi Medis Digital</style>'],
            ['<style font-size="11">Email: rekam.medis@medix.id | No. Dokumen: RM-XLS/' . date('Y/m/d') . '</style>'],
            [''],
            ['<style font-size="14"><b>LAPORAN DATA PASIEN & RINGKASAN KLINIS TERAKHIR</b></style>'],
            ['<b>Tanggal Cetak:</b>', now()->format('d/m/Y H:i:s')],
            [''],
            [
                '<style bgcolor="#E2EFDA"><b>No. RM</b></style>', 
                '<style bgcolor="#E2EFDA"><b>NIK Pasien</b></style>', 
                '<style bgcolor="#E2EFDA"><b>Nama Lengkap Pasien</b></style>', 
                '<style bgcolor="#E2EFDA"><b>L/P</b></style>', 
                '<style bgcolor="#E2EFDA"><b>Tanggal Lahir</b></style>', 
                '<style bgcolor="#E2EFDA"><b>Kontak (HP)</b></style>', 
                '<style bgcolor="#E2EFDA"><b>Tgl Registrasi</b></style>',
                '<style bgcolor="#DDEBF7"><b>Tgl Visite Terakhir</b></style>',
                '<style bgcolor="#DDEBF7"><b>BB (kg)</b></style>', 
                '<style bgcolor="#DDEBF7"><b>TB (cm)</b></style>', 
                '<style bgcolor="#DDEBF7"><b>IMT</b></style>', 
                '<style bgcolor="#DDEBF7"><b>Kategori Status Gizi (IMT)</b></style>',
                '<style bgcolor="#FCE4D6"><b>Tensi (S/D) mmHg</b></style>', 
                '<style bgcolor="#FCE4D6"><b>Kategori Kardiovaskular</b></style>',
                '<style bgcolor="#FFF2CC"><b>Gula Darah (mg/dL)</b></style>',
                '<style bgcolor="#FFF2CC"><b>Suhu (°C)</b></style>', 
                '<style bgcolor="#FFF2CC"><b>Nadi (bpm)</b></style>',
                '<style bgcolor="#F2F2F2"><b>Catatan Medis (Assessment)</b></style>'
            ]
        ];

        foreach ($patients as $p) {
            $latestRecord = $p->healthRecords->first();
            
            $tensi = ($latestRecord && $latestRecord->systolic && $latestRecord->diastolic) 
                        ? $latestRecord->systolic . '/' . $latestRecord->diastolic 
                        : '-';

            $data[] = [
                'RM-' . str_pad($p->id, 5, '0', STR_PAD_LEFT),
                "'" . $p->nik, // Using quote to force string type so Excel doesn't convert long numbers to scientific notation
                $p->name,
                $p->gender === 'male' ? 'L' : ($p->gender === 'female' ? 'P' : '-'),
                $p->date_of_birth ? date('d/m/Y', strtotime($p->date_of_birth)) : '-',
                $p->phone ?? '-',
                $p->created_at->format('d/m/Y'),
                
                // Detail Pemeriksaan Terakhir
                $latestRecord && $latestRecord->recorded_at ? $latestRecord->recorded_at->format('d/m/Y H:i') : 'Belum Ada Visite',
                $latestRecord->weight ?? '-',
                $latestRecord->height ?? '-',
                $latestRecord && $latestRecord->bmi ? number_format($latestRecord->bmi, 1) : '-',
                $latestRecord->bmi_status ?? '-',
                $tensi,
                $latestRecord->blood_pressure_status ?? '-',
                $latestRecord->blood_sugar ?? '-',
                $latestRecord->temperature ?? '-',
                $latestRecord->heart_rate ?? '-',
                $latestRecord->notes ?? '-'
            ];
        }

        return response()->streamDownload(function () use ($data) {
            $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
            $xlsx->saveAs('php://output');
        }, $excelFileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
