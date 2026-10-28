<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JadwalMcuController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\PdfMergerController;
use App\Http\Controllers\McuPdfController;
use Livewire\Livewire;
// Tambahkan komponen Livewire yang baru di sini
use App\Livewire\Admin\TambahAdmin;
use App\Livewire\Admin\TambahDokter;
use App\Livewire\QrScanner;
use App\Livewire\PaketPoli;
use App\Livewire\PemantauanLingkunganIndex;
use App\Livewire\PemantauanLingkunganForm;
use App\Livewire\NotificationDashboard;
use App\Livewire\NotificationHistory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\PoliGigiResult;
use App\Models\KebugaranResult;
use App\Models\FisikResult;
use App\Livewire\Admin\JadwalDokterManager;
use App\Http\Controllers\ValidationController; // Tambahkan ini di bagian atas file web.php


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Rute untuk Otentikasi (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Grup Rute untuk Admin (membutuhkan otentikasi guard 'admin_users')
Route::middleware(['auth:admin_users', 'verified'])->prefix('admin')->group(function () {
    
     // KRITIS: Rute Manajemen Profil Admin
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    // Rute untuk memproses pembaruan data (membutuhkan method PUT)
    // Pastikan baris ini ada, menggunakan Route::put
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');  
    // Tambahkan rute ini untuk dipanggil oleh AJAX
    Route::get('/admin/dashboard/lingkungan-data', [DashboardController::class, 'getLingkunganDataJson'])->name('dashboard.data_lingkungan');
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Manajemen Karyawan
    Route::get('/karyawan/download', [KaryawanController::class, 'downloadExcel'])->name('karyawan.download');
    Route::get('/peserta-mcu/download', [KaryawanController::class, 'pesertaMcuExcel'])->name('peserta.mcu.download');
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/{karyawan_id}/keluarga/{tipe}', [KaryawanController::class, 'showKeluarga'])->name('data.keluarga.show');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::get('/karyawan/{karyawan}', [KaryawanController::class, 'show'])->name('karyawan.show');
    Route::get('/keluarga/{pesertaMcu}', [KeluargaController::class, 'show'])->name('pasien.show');
    Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::get('/keluarga/{keluarga}/edit', [KeluargaController::class, 'edit'])->name('keluarga.edit');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
    Route::delete('/keluarga/{keluarga}', [KeluargaController::class, 'destroy'])->name('keluarga.destroy');
    Route::post('/karyawan/import', [KaryawanController::class, 'importExcel'])->name('karyawan.import');
    Route::get('/karyawan/{karyawan_id}/add-keluarga', [KaryawanController::class, 'addKeluarga'])->name('karyawan.add.keluarga');
    Route::post('/karyawan/{karyawan_id}/store-keluarga', [KaryawanController::class, 'storeKeluarga'])->name('karyawan.store.keluarga');
    Route::get('/add-pasien-non-karyawan', [KaryawanController::class, 'addKeluarga'])->name('pasien.add.nonkaryawan');
    Route::post('/store-pasien-non-karyawan', [KaryawanController::class, 'storeKeluarga'])->name('pasien.store.nonkaryawan');
    Route::post('/peserta-mcu/import', [KaryawanController::class, 'pesertaMcuImport'])->name('peserta-mcu.import');

    // Manajemen Jadwal MCU
    //Beritahu Laravel untuk membuat semua rute otomatis, KECUALI 'create'
    Route::resource('jadwal', JadwalMcuController::class)->except(['create']);
    // Route::get('/jadwal-mcu', [JadwalMcuController::class, 'index'])->name('jadwal.list');
    Route::get('/jadwal-mcu/create', [JadwalMcuController::class, 'create'])->name('jadwal-mcu.create');
    // Route::delete('/jadwal/{jadwal}', [JadwalMcuController::class, 'destroy'])->name('jadwal.destroy');
    Route::get('/jadwal/{jadwal}/detail', \App\Livewire\QrPatientDetail::class)->name('qr-patient-detail');
    // Route::get('/jadwal/{jadwal}/edit', [JadwalMcuController::class, 'edit'])->name('jadwal.edit');
    // Route::put('/jadwal/{jadwal}', [JadwalMcuController::class, 'update'])->name('jadwal.update');
    Route::get('/jadwal-dokter', JadwalDokterManager::class)->name('admin.jadwal-dokter');
    // Rute API untuk Jadwal Dokter (digunakan oleh FullCalendar)
    Route::get('/get-jadwal-dokter', [\App\Http\Controllers\Api\JadwalDokterApiController::class, 'getEvents'])->name('api.jadwal-dokter'); 
    
    Route::post('jadwal/{jadwal}/update-status', [JadwalMcuController::class, 'updateStatus'])->name('jadwal.update-status');
    Route::get('/scan-qr', QrScanner::class)->name('scan.qr');

    Route::get('/notifications/dashboard', NotificationDashboard::class)->name('notifications.dashboard');
    Route::get('/notifications/history', NotificationHistory::class)->name('notifications.history');

    // Manajemen Pengguna (Tambah Admin & Dokter)
    Route::get('/tambah-admin', TambahAdmin::class)->name('admin.create');
    Route::get('/tambah-dokter', TambahDokter::class)->name('admin.tambah-dokter');
    Route::get('/kelola-paket-poli', PaketPoli::class)->name('paket-poli');
    Route::get('/admin/laporan-pemeriksaan', \App\Livewire\Admin\ExportPemeriksaan::class)->name('admin.laporan.pemeriksaan');

    Route::prefix('lingkungan')->group(function () {
        Route::get('pemantauan', PemantauanLingkunganIndex::class)->name('pemantauan.index');
        Route::get('pemantauan/tambah', PemantauanLingkunganForm::class)->name('pemantauan.create');
    });

    // Rute untuk mengunduh PDF gabungan
    Route::get('/download-mcu-summary/{jadwalId}', [McuPdfController::class, 'downloadMcuSummary'])->name('download.mcu.summary');
    Route::get('/download/mcu-resume/{jadwalId}', [McuPdfController::class, 'downloadResume'])->name('download.resume.pdf');
    
    Route::get('/pdf/view-summary/{id}', [McuPdfController::class, 'viewPdf'])->name('pdf.view');
    Route::get('/pdf/gigi/{id}', [McuPdfController::class, 'viewPdfGigi'])->name('pdf.view.gigi');
    Route::get('/pdf/kebugaran/{id}', [McuPdfController::class, 'viewPdfKebugaran'])->name('pdf.kebugaran.view');
    Route::get('/pdf/fisik/{id}', [McuPdfController::class, 'viewPdfFisik'])->name('pdf.fisik.view');

    Route::get('/download/{filePath}', function ($filePath) {
    
        // 1. Tentukan path relatif di dalam disk 'public'
        $relativePath = 'pdf_reports/' . $filePath; 

        // 2. Periksa apakah file ada di disk 'public'
        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404, 'File ' . $filePath . ' tidak ditemukan di storage/app/public/pdf_reports/.');
        }

        // 3. Kembalikan respons untuk menampilkan file (inline)
        return Storage::disk('public')->response($relativePath, basename($filePath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);

    })->where('filePath', '.*')->name('download');

    Route::get('/pengaturan-sistem', \App\Livewire\Admin\SystemSettings::class)->name('admin.settings');
    Route::get('/admin/audit-trail', \App\Livewire\Admin\AuditTrail::class)->name('audit.trail');
});

// Grup Rute untuk Karyawan (jika ada guard terpisah)
Route::middleware(['auth:employee_logins'])->prefix('karyawan')->group(function () {
    // Dashboard Karyawan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('karyawan.dashboard');
    
    // Pendaftaran MCU (dari sisi karyawan)
    Route::post('/daftar-mcu', [JadwalMcuController::class, 'simpanPendaftaran'])->name('karyawan.daftar-mcu');
});

// Rute Publik untuk Validasi Keaslian PDF via Scan QR Code
Route::get('/validasi-dokumen/{uuid}', [ValidationController::class, 'verifyPdf'])->name('validasi.pdf');
