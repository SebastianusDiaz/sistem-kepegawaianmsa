<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsensiController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/absensi/check-in', [AbsensiController::class, 'checkIn'])->name('absensi.checkIn');
    Route::post('/absensi/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.checkOut');
    Route::get('/profile', [\App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/photo', [\App\Http\Controllers\UserController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::resource('absensi', AbsensiController::class);

    // Coverage Assignments
    Route::get('/assignments/published', [\App\Http\Controllers\AssignmentController::class, 'published'])->name('assignments.published');
    Route::resource('assignments', \App\Http\Controllers\AssignmentController::class);
    Route::patch('/assignments/{assignment}/status', [\App\Http\Controllers\AssignmentController::class, 'updateStatus'])->name('assignments.updateStatus');
    Route::get('/assignments/{assignment}/export', [\App\Http\Controllers\AssignmentController::class, 'exportPdf'])->name('assignments.export_pdf');
    Route::post('/assignments/{assignment}/take', [\App\Http\Controllers\AssignmentController::class, 'take'])->name('assignments.take');
    Route::post('/assignments/{assignment}/respond', [\App\Http\Controllers\AssignmentController::class, 'respond'])->name('assignments.respond');
    Route::post('/assignments/{assignment}/evidence', [\App\Http\Controllers\AssignmentController::class, 'updateEvidence'])->name('assignments.updateEvidence');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('divisions', \App\Http\Controllers\DivisionController::class);
    Route::resource('positions', \App\Http\Controllers\PositionController::class);

    // Employee Directory
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class)->only(['index', 'show']);

    // Reports
    Route::get('/reports/performance', [\App\Http\Controllers\PerformanceReportController::class, 'index'])->name('reports.performance');

    // Archives
    Route::resource('archives', \App\Http\Controllers\ArchiveController::class)->only(['index', 'store', 'destroy']);
    Route::get('/archives/{archive}/download', [\App\Http\Controllers\ArchiveController::class, 'download'])->name('archives.download');

    // Kerjasama (MoU)
    Route::resource('kerjasama', \App\Http\Controllers\KerjasamaController::class);
    Route::post('/kerjasama/{kerjasama}/approve', [\App\Http\Controllers\KerjasamaController::class, 'approve'])->name('kerjasama.approve');
    Route::post('/kerjasama/{kerjasama}/reject', [\App\Http\Controllers\KerjasamaController::class, 'reject'])->name('kerjasama.reject');

    // Permohonan Izin (Cuti & Izin)
    Route::resource('permohonan-izin', \App\Http\Controllers\PermohonanIzinController::class);
    Route::post('/permohonan-izin/{permohonan_izin}/approve', [\App\Http\Controllers\PermohonanIzinController::class, 'approve'])->name('permohonan-izin.approve');
    Route::post('/permohonan-izin/{permohonan_izin}/reject', [\App\Http\Controllers\PermohonanIzinController::class, 'reject'])->name('permohonan-izin.reject');

    // Rekap Absensi (Director Only)
    Route::get('/rekap-absensi', [\App\Http\Controllers\RekapAbsensiController::class, 'index'])->name('rekap-absensi.index');

    // Assignment Review System
    Route::get('/reviews', [\App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{assignment}', [\App\Http\Controllers\ReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{assignment}', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Payrolls
    Route::resource('payrolls', \App\Http\Controllers\PayrollController::class);
    Route::post('/payrolls/{payroll}/sync', [\App\Http\Controllers\PayrollController::class, 'syncDeductions'])->name('payrolls.sync');
    Route::patch('/payrolls/{payroll}/pay', [\App\Http\Controllers\PayrollController::class, 'markAsPaid'])->name('payrolls.markAsPaid');
    Route::get('/payrolls/{payroll}/print', [\App\Http\Controllers\PayrollController::class, 'print'])->name('payrolls.print');
});