<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Models\HealthRecord;
use App\Models\AiAnalysis;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalPatients  = Patient::count();
        $totalKaders    = User::where('role', 'kader')->count();
        $totalRecords   = HealthRecord::count();
        $totalAnalyses  = AiAnalysis::count();

        $newPatientsThisMonth = Patient::whereMonth('created_at', now()->month)->count();

        $recentPatients = Patient::latest()
            ->take(5)
            ->get(['id', 'nik', 'name', 'gender', 'created_at']);

        // Quantitative analysis stats
        $patientsAll = Patient::with('healthRecords')->get();
        $countHighBp = 0;
        $countHighSugar = 0;
        $countObesity = 0;
        $analyzedPatients = 0;

        foreach ($patientsAll as $p) {
            $latest = $p->healthRecords->sortByDesc('recorded_at')->first();
            if ($latest) {
                $analyzedPatients++;
                if ($latest->systolic >= 140 || $latest->diastolic >= 90) {
                    $countHighBp++;
                }
                if ($latest->blood_sugar >= 200) {
                    $countHighSugar++;
                }
                if ($latest->weight && $latest->height) {
                    $bmi = $latest->weight / pow($latest->height / 100, 2);
                    if ($bmi >= 25) {
                        $countObesity++;
                    }
                }
            }
        }

        $pctHighBp = $analyzedPatients > 0 ? round(($countHighBp / $analyzedPatients) * 100) : 0;
        $pctHighSugar = $analyzedPatients > 0 ? round(($countHighSugar / $analyzedPatients) * 100) : 0;
        $pctObesity = $analyzedPatients > 0 ? round(($countObesity / $analyzedPatients) * 100) : 0;

        $monthlyData = HealthRecord::selectRaw('MONTH(recorded_at) as month, COUNT(*) as count')
            ->whereYear('recorded_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyChart = collect(range(1, 12))->map(fn($m) => [
            'month' => $m,
            'count' => $monthlyData[$m]->count ?? 0,
        ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalPatients'       => $totalPatients,
                'totalKaders'         => $totalKaders,
                'totalRecords'        => $totalRecords,
                'totalAnalyses'       => $totalAnalyses,
                'newPatientsThisMonth'=> $newPatientsThisMonth,
                'pctHighBp'           => $pctHighBp,
                'pctHighSugar'        => $pctHighSugar,
                'pctObesity'          => $pctObesity,
                'countHighBp'         => $countHighBp,
                'countHighSugar'      => $countHighSugar,
                'countObesity'        => $countObesity,
                'analyzedPatients'    => $analyzedPatients,
            ],
            'recentPatients' => $recentPatients,
            'monthlyChart'   => $monthlyChart,
        ]);
    }
}
