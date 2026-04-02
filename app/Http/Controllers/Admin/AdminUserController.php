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
            ->latest()
            ->get();

        $filename = 'pasien_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($patients) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'NIK', 'Nama', 'Gender', 'Tanggal Lahir', 'No HP', 'Total Data', 'Total Analisis', 'Terdaftar']);
            foreach ($patients as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->nik,
                    $p->name,
                    $p->gender ?? '-',
                    $p->date_of_birth?->format('d/m/Y') ?? '-',
                    $p->phone ?? '-',
                    $p->health_records_count,
                    $p->ai_analyses_count,
                    $p->created_at->format('d/m/Y'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
