<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HealthRecord;
use App\Models\AiAnalysis;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalRecords = HealthRecord::count();
        $totalAnalyses = AiAnalysis::count();
        $newUsersThisMonth = User::where('role', 'user')
            ->whereMonth('created_at', now()->month)
            ->count();

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'gender', 'avatar', 'created_at']);

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
                'totalUsers' => $totalUsers,
                'totalRecords' => $totalRecords,
                'totalAnalyses' => $totalAnalyses,
                'newUsersThisMonth' => $newUsersThisMonth,
            ],
            'recentUsers' => $recentUsers,
            'monthlyChart' => $monthlyChart,
        ]);
    }
}
