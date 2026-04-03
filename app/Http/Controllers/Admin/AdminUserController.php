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
            ['<b>LAPORAN DATA REKAM MEDIS & PASIEN (Administrator)</b>'],
            ['<b>SISTEM MANAJEMEN KESEHATAN TINGKAT PERTAMA</b>'],
            ['<b>Tanggal Cetak:</b>', now()->format('d/m/Y H:i')],
            [''],
            [
                '<b>ID</b>', '<b>NIK</b>', '<b>Nama Pasien</b>', '<b>Gender</b>', '<b>Tanggal Lahir</b>', '<b>No HP</b>', 
                '<b>Total Data</b>', '<b>Tanggal Terdaftar</b>',
                '<b>Tgl Pemeriksaan Terakhir</b>',
                '<b>Berat Badan (kg)</b>', '<b>Tinggi Badan (cm)</b>', '<b>IMT</b>', '<b>Kesimpulan IMT</b>',
                '<b>Sistolik (mmHg)</b>', '<b>Diastolik (mmHg)</b>', '<b>Kesimpulan Tensi</b>',
                '<b>Gula Darah (mg/dL)</b>',
                '<b>Suhu Tubuh (°C)</b>', '<b>Detak Jantung (bpm)</b>',
                '<b>Catatan Medis</b>'
            ]
        ];

        foreach ($patients as $p) {
            $latestRecord = $p->healthRecords->first();
            
            $data[] = [
                $p->id,
                $p->nik,
                $p->name,
                $p->gender ?? '-',
                $p->date_of_birth?->format('d/m/Y') ?? '-',
                $p->phone ?? '-',
                $p->health_records_count,
                $p->created_at->format('d/m/Y'),
                
                // Detail Pemeriksaan Terakhir
                $latestRecord ? ($latestRecord->recorded_at?->format('d/m/Y H:i') ?? '-') : 'Belum Ada Data',
                $latestRecord->weight ?? '-',
                $latestRecord->height ?? '-',
                $latestRecord ? $latestRecord->bmi : '-',
                $latestRecord ? $latestRecord->bmi_status : '-',
                $latestRecord->systolic ?? '-',
                $latestRecord->diastolic ?? '-',
                $latestRecord ? $latestRecord->blood_pressure_status : '-',
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
