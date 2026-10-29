<?php

// Memuat class dari Laravel untuk mendefinisikan URL/Rute API
use Illuminate\Support\Facades\Route;

// Memuat controller yang menangani proses autentikasi (login, logout, profil)
use App\Http\Controllers\Api\AuthController as ApiAuthController;
// Memuat controller yang menangani logika pendaftaran dan riwayat MCU
use App\Http\Controllers\Api\JadwalMcuApiController;
// Memuat controller yang menyediakan data parameter lingkungan
use App\Http\Controllers\Api\LingkunganApiController;
// Memuat controller yang menangani informasi jadwal dokter poliklinik
use App\Http\Controllers\Api\JadwalDokterApiController;
// Memuat class Request untuk menangani HTTP request dari sisi klien (mobile)
use Illuminate\Http\Request;
// Memuat model Karyawan untuk referensi data
use App\Models\Karyawan;

// Rute Publik (Akses tanpa token)
// Endpoint untuk memvalidasi kredensial pengguna dan mengembalikan token Sanctum
Route::post('/login', [ApiAuthController::class, 'login']);
// Endpoint Pendaftaran & Aktivasi Akun Mobile (Karyawan & Peserta Umum)
Route::post('/register/karyawan', [ApiAuthController::class, 'registerKaryawan']);
Route::post('/register/umum', [ApiAuthController::class, 'registerUmum']);
// Endpoint untuk mengunduh rekapitulasi laporan MCU akhir berdasarkan ID jadwal
Route::get('/jadwal-mcu/download-laporan-gabungan/{id}', [JadwalMcuApiController::class, 'downloadLaporanGabungan']);


// Rute Terlindungi (Protected Routes)
// Middleware 'auth:sanctum' memastikan hanya request dengan token otorisasi valid yang bisa mengakses rute di dalam grup ini
Route::middleware('auth:sanctum')->group(function () {

    // Endpoint untuk menghapus token akses saat ini (mengakhiri sesi pengguna)
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    
    // Endpoint khusus untuk menerima dan memperbarui token Firebase Cloud Messaging (FCM) dari aplikasi mobile
    // Token ini esensial untuk fitur push notification (seperti pengingat jadwal MCU)
    Route::post('/update-fcm-token', function (Request $request) {
        // Validasi input: fcm_token wajib diisi
        $request->validate(['fcm_token' => 'required']);
        
        // Mengambil instansiasi user yang sedang aktif mengirim request
        $user = $request->user(); 
        
        // Percabangan logika berdasarkan tipe akun yang login:
        // 1. Jika pengguna diidentifikasi sebagai entitas akun 'EmployeeLogin' (Karyawan internal)
        if ($user instanceof \App\Models\EmployeeLogin && $user->karyawan) {
            // Perbarui kolom fcm_token pada tabel karyawan
            $user->karyawan->update(['fcm_token' => $request->fcm_token]);
        } 
        // 2. Jika pengguna diidentifikasi sebagai entitas akun umum 'PesertaMcuLogin' (Pasien eksternal)
        elseif ($user instanceof \App\Models\PesertaMcuLogin && $user->pasien) {
            // Perbarui kolom fcm_token pada tabel pasien
            $user->pasien->update(['fcm_token' => $request->fcm_token]);
        }

        // Mengembalikan respons JSON standar bahwa pembaruan berhasil
        return response()->json(['status' => 'success']);
    });

    // Endpoint pengelolaan akun
    Route::post('/change-password', [ApiAuthController::class, 'changePassword']); // Mengubah kata sandi
    Route::post('/update-profile', [ApiAuthController::class, 'updateProfile']);   // Memperbarui informasi profil pengguna
    
    // Endpoint layanan pemantauan kondisi lingkungan fisik
    Route::get('/lingkungan', [LingkunganApiController::class, 'index']);          // Menampilkan daftar data lingkungan
    Route::get('/lingkungan/filters', [LingkunganApiController::class, 'getFilters']); // Menyediakan data dropdown untuk fitur filter di aplikasi
    
    // Endpoint untuk sistem antrean: Memproses validasi kehadiran pengguna di klinik (misal melalui QR Scanner)
    Route::post('/jadwal-poli/checkin', [\App\Http\Controllers\Api\JadwalMcuApiController::class, 'checkInPoli']);
    
    // Endpoint untuk menarik kalender operasional dokter
    Route::get('/jadwal-dokter/events', [JadwalDokterApiController::class, 'getEvents']);
    
    // Grup rute spesifik untuk manajemen siklus Medical Check-Up
    Route::prefix('jadwal-mcu')->group(function () {
        // Endpoint untuk mendaftarkan jadwal MCU baru ke sistem
        Route::post('/ajukan', [JadwalMcuApiController::class, 'store']);
        // Endpoint untuk melihat histori pemeriksaan MCU berdasarkan ID pengguna yang sedang login
        Route::get('/riwayat', [JadwalMcuApiController::class, 'getRiwayatByUser']);
        // Endpoint untuk mengambil referensi jenis-jenis paket MCU yang tersedia di klinik
        Route::get('/paket', [JadwalMcuApiController::class, 'getPaketMcu']);
        // Endpoint untuk memvalidasi apakah kuota jadwal pada tanggal yang dipilih masih tersedia
        Route::get('/check-ketersediaan', [\App\Http\Controllers\Api\JadwalMcuApiController::class, 'checkKetersediaan']);
    });
});