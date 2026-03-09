<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('role', 'user')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('healthRecords', 'aiAnalyses')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'users'   => $users,
            'filters' => ['search' => $search],
        ]);
    }

    public function show(User $user)
    {
        $user->load([
            'healthRecords' => fn($q) => $q->latest('recorded_at')->take(50),
            'aiAnalyses'    => fn($q) => $q->latest()->take(10),
        ]);

        return Inertia::render('Admin/UserDetail', [
            'user'    => $user,
            'records' => $user->healthRecords,
            'analyses'=> $user->aiAnalyses,
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun admin.']);
        }

        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('role', 'user')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('healthRecords', 'aiAnalyses')
            ->latest()
            ->get();

        $filename = 'users_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM for Excel
            fputcsv($file, ['ID', 'Nama', 'Email', 'Gender', 'Umur', 'Total Data', 'Total Analisis', 'Bergabung']);
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->gender ?? '-',
                    $user->age    ?? '-',
                    $user->health_records_count,
                    $user->ai_analyses_count,
                    $user->created_at->format('d/m/Y'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
