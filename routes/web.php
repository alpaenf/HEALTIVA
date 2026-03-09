<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\StandarNormalController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AiKnowledgeController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return inertia('Landing');
});

// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Main user routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/input-data', [HealthRecordController::class, 'create'])->name('input-data');
    Route::post('/input-data', [HealthRecordController::class, 'store']);
    Route::get('/history', [HealthRecordController::class, 'index'])->name('history');
    Route::delete('/history/{healthRecord}', [HealthRecordController::class, 'destroy'])->name('history.destroy');
    Route::get('/ai-analysis', [AiAnalysisController::class, 'index'])->name('ai-analysis');
    Route::post('/ai-analysis', [AiAnalysisController::class, 'analyze'])->name('ai-analysis.run');
    Route::delete('/ai-analysis/{aiAnalysis}', [AiAnalysisController::class, 'destroy'])->name('ai-analysis.destroy');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Edukasi
    Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi');

    // Standar Normal
    Route::get('/standar-normal', [StandarNormalController::class, 'index'])->name('standar-normal');

    // AI Chat
    Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai-chat');
    Route::post('/ai-chat/message', [AiChatController::class, 'message'])->name('ai-chat.message');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // AI Knowledge Base
        Route::get('/knowledge', [AiKnowledgeController::class, 'index'])->name('knowledge.index');
        Route::post('/knowledge', [AiKnowledgeController::class, 'store'])->name('knowledge.store');
        Route::put('/knowledge/{knowledge}', [AiKnowledgeController::class, 'update'])->name('knowledge.update');
        Route::delete('/knowledge/{knowledge}', [AiKnowledgeController::class, 'destroy'])->name('knowledge.destroy');
    });
});
