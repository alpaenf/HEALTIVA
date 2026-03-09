<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HealthRecordController extends Controller
{
    public function index()
    {
        $records = Auth::user()
            ->healthRecords()
            ->latest('recorded_at')
            ->paginate(10);

        return Inertia::render('History', [
            'records' => $records,
        ]);
    }

    public function create()
    {
        return Inertia::render('InputData');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'systolic' => 'nullable|integer|min:50|max:300',
            'diastolic' => 'nullable|integer|min:30|max:200',
            'heart_rate' => 'nullable|integer|min:20|max:300',
            'blood_sugar' => 'nullable|numeric|min:0|max:1000',
            'weight' => 'nullable|numeric|min:1|max:500',
            'height' => 'nullable|numeric|min:1|max:300',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'recorded_at' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['recorded_at'] = $validated['recorded_at'] ?? now();

        HealthRecord::create($validated);

        return redirect()->route('history')->with('success', 'Data kesehatan berhasil disimpan!');
    }

    public function show(HealthRecord $healthRecord)
    {
        $this->authorize('view', $healthRecord);

        return Inertia::render('HealthRecordDetail', [
            'record' => $healthRecord,
        ]);
    }

    public function destroy(HealthRecord $healthRecord)
    {
        $this->authorize('delete', $healthRecord);

        $healthRecord->delete();

        return redirect()->route('history')->with('success', 'Data berhasil dihapus.');
    }
}
