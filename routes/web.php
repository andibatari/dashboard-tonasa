<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Busdev\BpmIcofrController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Busdev\InnovationController;
use App\Http\Controllers\Busdev\SystemDevelopmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

//Rute Umum (Bisa diakses oleh semua user yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. RUTE PORTAL INOVASI UNTUK KARYAWAN UMUM
    Route::get('/inovasi', [InnovationController::class, 'portal'])->name('inovasi.portal');
    Route::post('/inovasi/daftar', [InnovationController::class, 'store'])->name('inovasi.store');
    // --- TAMBAHKAN RUTE INI ---
    Route::get('/inovasi/template/{kategori}', [InnovationController::class, 'downloadTemplate'])->name('inovasi.download-template');
});

// Ruang Kerja Khusus Busdev
Route::middleware(['auth', 'role:Developer|Super Admin|Admin'])->prefix('busdev')->name('busdev.')->group(function () {
    
    // ---- Halaman BPM ICT ICoFR & Ekspor ----
    Route::get('/bpm-icofr', [BpmIcofrController::class, 'index'])->name('bpm-icofr.index');
    Route::get('/bpm-icofr/export', [BpmIcofrController::class, 'exportExcel'])->name('bpm-icofr.export');
    
    // ---- Siklus ----
    Route::post('bpm-icofr/cycle', [BpmIcofrController::class, 'storeCycle'])->name('bpm-icofr.store-cycle');
    
    // ---- Proses Utama ----
    Route::post('/bpm-icofr/proses', [BpmIcofrController::class, 'storeProcess'])->name('bpm-icofr.store-process');
    Route::put('/bpm-icofr/proses/{id}', [BpmIcofrController::class, 'updateProcess'])->name('bpm-icofr.update-process'); // Rute Edit Proses Utama
    Route::delete('/bpm-icofr/proses/{id}', [BpmIcofrController::class, 'destroyProcess'])->name('bpm-icofr.destroy-process'); // Rute Hapus Proses Utama
    Route::put('bpm-icofr/process/{id}/status', [BpmIcofrController::class, 'updateProcessStatus'])->name('bpm-icofr.update-process-status');

    // ---- Sub Proses ---- (Hapus awalan 'busdev.' di sini agar tidak double)
    Route::post('bpm-icofr/sub-process', [BpmIcofrController::class, 'storeSubProcess'])->name('bpm-icofr.store-sub-process');
    Route::put('bpm-icofr/sub-process/{id}', [BpmIcofrController::class, 'updateSubProcess'])->name('bpm-icofr.update-sub-process');
    Route::delete('bpm-icofr/sub-process/{id}', [BpmIcofrController::class, 'destroySubProcess'])->name('bpm-icofr.destroy-sub-process');

    // ---- Halaman Detail Siklus ----
    Route::get('/bpm-icofr/{cycle}', [BpmIcofrController::class, 'show'])->name('bpm-icofr.show');
    
    // ---- Matriks RCM (Risiko & Kontrol) ----
    Route::post('/bpm-icofr/kontrol/simpan', [BpmIcofrController::class, 'storeControl'])->name('bpm-icofr.store-control');
    Route::put('/bpm-icofr/kontrol/{id}', [BpmIcofrController::class, 'updateControl'])->name('bpm-icofr.update-control');
    Route::delete('/bpm-icofr/kontrol/{id}', [BpmIcofrController::class, 'destroyControl'])->name('bpm-icofr.destroy-control');

    // ---- Import RCM (Excel) & Dokumen BPM ----
    Route::post('/bpm-icofr/import-rcm', [BpmIcofrController::class, 'importRcm'])->name('bpm-icofr.import-rcm');
    Route::post('/bpm-icofr/upload-bpm', [BpmIcofrController::class, 'uploadBpm'])->name('bpm-icofr.upload-bpm');

    Route::post('/bpm-icofr/sync-sheets', [BpmIcofrController::class, 'syncFromGoogleSheets'])->name('bpm-icofr.sync-sheets');
    Route::post('/bpm-icofr/push-sheets', [BpmIcofrController::class, 'pushToGoogleSheets'])->name('bpm-icofr.push-sheets');

    Route::put('/bpm-icofr/cycle/{id}', [BpmIcofrController::class, 'updateCycle'])->name('bpm-icofr.update-cycle');
    Route::delete('/bpm-icofr/cycle/{id}', [BpmIcofrController::class, 'destroyCycle'])->name('bpm-icofr.destroy-cycle');

    Route::post('/bpm-icofr/download-cert', [BpmIcofrController::class, 'downloadCertificate'])->name('bpm-icofr.download-cert');
    Route::post('/bpm-icofr/update-officials', [BpmIcofrController::class, 'updateOfficials'])->name('bpm-icofr.update-officials');

    // ---- Kelola PIC Pelaksana Kontrol ----
    Route::get('/bpm-icofr/pics', [BpmIcofrController::class, 'getPics'])->name('bpm-icofr.pics.index');
    Route::post('/bpm-icofr/pics', [BpmIcofrController::class, 'storePic'])->name('bpm-icofr.pics.store');
    Route::put('/bpm-icofr/pics/{id}', [BpmIcofrController::class, 'updatePic'])->name('bpm-icofr.pics.update');
    Route::delete('/bpm-icofr/pics/{id}', [BpmIcofrController::class, 'destroyPic'])->name('bpm-icofr.pics.destroy');
    Route::post('/bpm-icofr/pics/push-sheets', [BpmIcofrController::class, 'pushPicsToGoogleSheets'])->name('bpm-icofr.pics.push-sheets');

    // ---- Sub Menu System Development (Monitoring Sertifikat & Temuan Audit) ----
    Route::get('/system-development', [SystemDevelopmentController::class, 'index'])->name('system-development.index');
    Route::post('/system-development/sync-sheets', [SystemDevelopmentController::class, 'syncFromGoogleSheets'])->name('system-development.sync-sheets');
    Route::post('/system-development/sync-audit-sheets', [SystemDevelopmentController::class, 'syncAuditSheets'])->name('system-development.sync-audit-sheets');
    Route::post('/system-development/import-excel', [SystemDevelopmentController::class, 'importExcel'])->name('system-development.import-excel');
    Route::get('/system-development/export-excel', [SystemDevelopmentController::class, 'exportExcel'])->name('system-development.export-excel');
    Route::post('/system-development/certificate', [SystemDevelopmentController::class, 'store'])->name('system-development.store');
    Route::put('/system-development/certificate/{id}', [SystemDevelopmentController::class, 'update'])->name('system-development.update');
    Route::delete('/system-development/certificate/{id}', [SystemDevelopmentController::class, 'destroy'])->name('system-development.destroy');
    Route::post('/system-development/audit-finding', [SystemDevelopmentController::class, 'storeAuditFinding'])->name('system-development.store-audit-finding');
    Route::put('/system-development/audit-finding/{id}', [SystemDevelopmentController::class, 'updateAuditFinding'])->name('system-development.update-audit-finding');
    Route::delete('/system-development/audit-finding/{id}', [SystemDevelopmentController::class, 'destroyAuditFinding'])->name('system-development.destroy-audit-finding');

    // 3. RUTE DASBOR MONITORING INOVASI (HANYA UNTUK ADMIN/BUSDEV)
    Route::get('/innovation-management', [InnovationController::class, 'adminIndex'])->name('innovation.index');
    Route::get('/innovation-management/export', [InnovationController::class, 'exportExcel'])->name('innovation.export');
    Route::get('/innovation-management/makalah/{id}', [InnovationController::class, 'previewMakalah'])->name('innovation.makalah');
    Route::get('/innovation-management/makalah/{id}/download', [InnovationController::class, 'downloadMakalah'])->name('innovation.makalah.download');
});

// Rute Khusus Pengelola Sistem (Developer & Super Admin)
Route::middleware(['auth', 'role:Developer|Super Admin'])->group(function () {
    // Manajemen Akun
    Route::get('/manajemen-akun', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/manajemen-akun', [AccountController::class, 'store'])->name('accounts.store');
    Route::put('/manajemen-akun/{user}', [AccountController::class, 'update'])->name('accounts.update'); 
    Route::delete('/manajemen-akun/{user}', [AccountController::class, 'destroy'])->name('accounts.destroy');

    // Log Aktivitas (Audit Trail)
    Route::get('/log-aktivitas', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Rute Pengaturan Sistem (Admin Busdev boleh masuk)
Route::middleware(['auth', 'role:Developer|Super Admin|Admin'])->group(function () {
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
    Route::put('/pengaturan/templates', [SettingController::class, 'updateTemplates'])->name('settings.update-templates');
});

require __DIR__.'/auth.php';