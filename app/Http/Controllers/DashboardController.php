<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\AiAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $latestRecord = $user->healthRecords()
            ->latest('recorded_at')
            ->first();

        $recentRecords = $user->healthRecords()
            ->latest('recorded_at')
            ->take(7)
            ->get()
            ->reverse()
            ->values();

        $totalRecords = $user->healthRecords()->count();
        $latestAnalysis = $user->aiAnalyses()->latest()->first();

        $healthScore = $this->calculateHealthScore($latestRecord);

        $chartData = $recentRecords->map(fn($r) => [
            'date' => $r->recorded_at->format('d/m'),
            'systolic' => $r->systolic,
            'diastolic' => $r->diastolic,
            'heart_rate' => $r->heart_rate,
            'weight' => $r->weight,
        ]);

        return Inertia::render('Dashboard', [
            'latestRecord' => $latestRecord,
            'recentRecords' => $recentRecords,
            'totalRecords' => $totalRecords,
            'latestAnalysis' => $latestAnalysis,
            'healthScore' => $healthScore,
            'chartData' => $chartData,
        ]);
    }

    private function calculateHealthScore(?HealthRecord $record): int
    {
        if (!$record) return 0;

        $score = 100;

        if ($record->systolic && $record->diastolic) {
            if ($record->systolic >= 140 || $record->diastolic >= 90) $score -= 25;
            elseif ($record->systolic >= 130) $score -= 10;
        }

        if ($record->heart_rate) {
            if ($record->heart_rate < 60 || $record->heart_rate > 100) $score -= 15;
        }

        if ($record->blood_sugar) {
            if ($record->blood_sugar > 200) $score -= 25;
            elseif ($record->blood_sugar > 140) $score -= 10;
        }

        if ($record->weight && $record->height) {
            $bmi = $record->bmi;
            if ($bmi < 18.5 || $bmi >= 30) $score -= 20;
            elseif ($bmi >= 25) $score -= 10;
        }

        return max(0, $score);
    }
}
