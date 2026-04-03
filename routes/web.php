<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\StandarNormalController;
use App\Http\Controllers\Kader\KaderDashboardController;
use App\Http\Controllers\Kader\KaderPatientController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminKaderController;
use App\Http\Controllers\Admin\AiKnowledgeController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// Landing
// ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    return inertia('Landing');
});

// ─────────────────────────────────────────────────────────────
// Auth routes (admin / kader login)  — guests only
// ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

// Tamu Google OAuth (open — no guest middleware, any visitor can use)
Route::get('/auth/google/tamu', [AuthController::class, 'redirectToGoogleTamu'])->name('auth.google.tamu');

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─────────────────────────────────────────────────────────────
// Patient lookup (no login required)
// ─────────────────────────────────────────────────────────────
Route::get('/masuk', [PatientController::class, 'showLookup'])->name('patient.lookup');
Route::post('/masuk', [PatientController::class, 'lookup'])->name('patient.login');
Route::post('/keluar', [PatientController::class, 'logout'])->name('patient.logout');

// ─────────────────────────────────────────────────────────────
// Patient session routes (NIK session required)
// ─────────────────────────────────────────────────────────────
Route::middleware('patient.session')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [HealthRecordController::class, 'index'])->name('history');
    Route::get('/ai-analysis', [AiAnalysisController::class, 'index'])->name('ai-analysis');
    Route::post('/ai-analysis', [AiAnalysisController::class, 'analyze'])->name('ai-analysis.run');
    Route::delete('/ai-analysis/{aiAnalysis}', [AiAnalysisController::class, 'destroy'])->name('ai-analysis.destroy');
    Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai-chat');
    Route::post('/ai-chat/message', [AiChatController::class, 'message'])->name('ai-chat.message');
});

// Public signed report link for WhatsApp sharing (no auth, signature-protected)
Route::get('/share/analysis/{aiAnalysis}', [AiAnalysisController::class, 'sharedReport'])
    ->middleware('signed')
    ->name('analysis.share');

// Short share URL for WhatsApp (token cached server-side)
Route::get('/s/{token}', [AiAnalysisController::class, 'sharedReportByToken'])
    ->name('analysis.share.short');

// ─────────────────────────────────────────────────────────────
// Shared info routes (patient session OR kader/admin — no guard needed)
// ─────────────────────────────────────────────────────────────
Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi');
Route::get('/standar-normal', [StandarNormalController::class, 'index'])->name('standar-normal');

// ─────────────────────────────────────────────────────────────
// Kader routes
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'kader'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', [KaderDashboardController::class, 'index'])->name('dashboard');

    // Patients management
    Route::get('/pasien', [KaderPatientController::class, 'index'])->name('patients');
    Route::post('/pasien', [KaderPatientController::class, 'store'])->name('patients.store');
    Route::get('/pasien/cari', [KaderPatientController::class, 'search'])->name('patients.search');
    Route::get('/pasien/export-all', [KaderPatientController::class, 'exportAll'])->name('patients.export_all');
    Route::get('/pasien/{patient}', [KaderPatientController::class, 'show'])->name('patients.show');
    Route::get('/pasien/{patient}/export', [KaderPatientController::class, 'export'])->name('patients.export');
    Route::put('/pasien/{patient}', [KaderPatientController::class, 'update'])->name('patients.update');
    Route::delete('/pasien/{patient}', [KaderPatientController::class, 'destroy'])->name('patients.destroy');

    // Input health data for a patient
    Route::get('/pasien/{patient}/input', [KaderPatientController::class, 'showInput'])->name('patients.input');
    Route::post('/pasien/{patient}/input', [KaderPatientController::class, 'storeInput'])->name('patients.input.store');

    // AI Analysis management for patient
    Route::post('/pasien/{patient}/analyze', [KaderPatientController::class, 'analyze'])->name('patients.analyze');
    Route::delete('/pasien/{patient}/analyze/{aiAnalysis}', [KaderPatientController::class, 'destroyAnalysis'])->name('patients.analyze.destroy');
});

// ─────────────────────────────────────────────────────────────
// Admin routes
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Patients
    Route::get('/patients', [AdminUserController::class, 'index'])->name('patients');
    Route::get('/patients/export', [AdminUserController::class, 'export'])->name('patients.export');
    Route::get('/patients/{patient}/edit', [AdminUserController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [AdminUserController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}/records/{healthRecord}', [AdminUserController::class, 'destroyRecord'])->name('patients.records.destroy');
    Route::get('/patients/{patient}', [AdminUserController::class, 'show'])->name('patients.show');
    Route::delete('/patients/{patient}', [AdminUserController::class, 'destroy'])->name('patients.destroy');

    // Kaders
    Route::get('/kaders', [AdminKaderController::class, 'index'])->name('kaders');
    Route::post('/kaders', [AdminKaderController::class, 'store'])->name('kaders.store');
    Route::delete('/kaders/{user}', [AdminKaderController::class, 'destroy'])->name('kaders.destroy');

    // AI Knowledge Base
    Route::get('/knowledge', [AiKnowledgeController::class, 'index'])->name('knowledge.index');
    Route::post('/knowledge', [AiKnowledgeController::class, 'store'])->name('knowledge.store');
    Route::put('/knowledge/{knowledge}', [AiKnowledgeController::class, 'update'])->name('knowledge.update');
    Route::delete('/knowledge/{knowledge}', [AiKnowledgeController::class, 'destroy'])->name('knowledge.destroy');
});
