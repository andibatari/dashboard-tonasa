<?php

// Mendefinisikan lokasi (namespace) file controller ini di dalam struktur folder Laravel
namespace App\Http\Controllers\Api;

// Memuat berbagai library bawaan Laravel dan package eksternal yang dibutuhkan
use Illuminate\Support\Facades\Storage; // Untuk mengelola file sistem (upload/download file PDF)
use App\Http\Controllers\Controller; // Menggunakan base Controller bawaan Laravel
use Illuminate\Http\Request; // Untuk menangkap data yang dikirim dari klien (Flutter/Postman)
use Illuminate\Support\Facades\DB; // Untuk menjalankan query database secara langsung
use Illuminate\Support\Facades\Log; // Untuk mencatat error ke dalam file log Laravel
use Illuminate\Validation\ValidationException; // Untuk menangani error jika input tidak valid
use Illuminate\Support\Str; // Menyediakan fungsi manipulasi string (seperti membuat UUID dan Slug)

// Memuat Model-Model yang merepresentasikan tabel di Database
use App\Models\JadwalMcu; 
use App\Models\EmployeeLogin;
use App\Models\PesertaMcuLogin;
use App\Models\Dokter;
use App\Http\Resources\JadwalMcuResource; // Untuk memformat output data JSON API

// Memuat Library eksternal untuk pengelolaan PDF dan QR Code
use iio\libmergepdf\Merger; // Library untuk menggabungkan beberapa file PDF menjadi satu
use Barryvdh\DomPDF\Facade\Pdf; // Library untuk membuat PDF dari file view HTML/Blade
use Carbon\Carbon; // Library untuk mengelola dan memformat tanggal/waktu
use Exception; // Class bawaan PHP untuk menangkap pesan error global
use Endroid\QrCode\QrCode; // Library untuk membuat gambar QR Code
use Endroid\QrCode\Writer\PngWriter; // Untuk menulis/menyimpan QR Code dalam format PNG
use Endroid\QrCode\Encoding\Encoding; // Untuk mengatur format encoding (UTF-8) pada QR Code
use Endroid\QrCode\ErrorCorrectionLevel; // Mengatur tingkat toleransi kerusakan QR Code

class JadwalMcuApiController extends Controller
{
    // Fungsi untuk mendaftarkan jadwal MCU baru dari aplikasi Mobile
    public function store(Request $request)
    {
        // Mengambil data user yang sedang login berdasarkan token Sanctum yang dikirim
        $loginUser = auth('sanctum')->user(); 

        // Jika tidak ada user yang login (token tidak valid), kembalikan error 401
        if (!$loginUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Memvalidasi inputan dari form aplikasi mobile
            $request->validate([
                'tanggal_mcu' => 'required|date|after_or_equal:today', // Tanggal wajib diisi dan tidak boleh masa lalu
                'paket_mcu'   => 'required|exists:paket_mcus,id', // ID paket harus ada di tabel paket_mcus
            ]);

            // Menentukan profil user yang sedang login apakah Karyawan Internal atau Peserta Eksternal
            if ($loginUser instanceof EmployeeLogin) {
                $user = $loginUser->karyawan; // Ambil data detail karyawan
                $column = 'karyawan_id'; // Set kolom relasi ke karyawan_id
            } elseif ($loginUser instanceof PesertaMcuLogin) {
                $user = $loginUser->pasien; // Ambil data detail pasien umum
                $column = 'peserta_mcus_id'; // Set kolom relasi ke peserta_mcus_id
            } 

            // Cek apakah user sudah punya jadwal MCU yang sedang aktif atau belum selesai
            if (JadwalMcu::where($column, $user->id)->whereIn('status', ['Scheduled', 'Present'])->exists()) {
                // Jika sudah punya, tolak pendaftaran baru
                return response()->json(['success' => false, 'message' => 'Anda sudah memiliki jadwal MCU aktif.'], 409);
            }

            // Ubah format tanggal inputan menjadi format standar database (YYYY-MM-DD)
            $tanggal = Carbon::parse($request->tanggal_mcu)->toDateString();

            // Menghitung jumlah pasien yang sudah mendaftar pada tanggal tersebut (kecuali yang batal)
            $kuotaTerisi = JadwalMcu::whereDate('tanggal_mcu', $tanggal)
                                    ->where('status', '!=', 'Canceled') 
                                    ->count();

            // Jika kuota sudah mencapai 30 orang, tolak pendaftaran
            if ($kuotaTerisi >= 30) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Mohon maaf, kuota jadwal untuk tanggal tersebut sudah penuh. Silakan pilih hari yang lain.'
                ], 422); 
            }

            // Mengambil nomor antrean terakhir pada tanggal yang dipilih
            $lastAntrean = JadwalMcu::whereDate('tanggal_mcu', $tanggal)
                ->orderBy('id', 'desc') // Urutkan dari yang terbaru
                ->first();

            // Jika hari itu sudah ada yang mendaftar
            if ($lastAntrean && $lastAntrean->no_antrean) {
                // Ambil angkanya saja (misal dari "C001" ambil angka "1")
                $lastNumber = (int) substr($lastAntrean->no_antrean, 1); 
                $nextNumber = $lastNumber + 1; // Tambahkan 1 untuk nomor antrean selanjutnya
            } else {
                // Jika hari itu kosong, mulai nomor antrean dari angka 1
                $nextNumber = 1;
            }

            // Cek jadwal dokter poli yang bertugas pada tanggal MCU yang dipilih
            $jadwalDokter = \App\Models\JadwalDokter::whereDate('tanggal', $tanggal)->first();
            // Jika ada dokter, ambil ID dokternya. Jika tidak ada, isi dengan null
            $dokterId = $jadwalDokter ? $jadwalDokter->dokter_id : null;

            // Menyimpan data pendaftaran baru ke tabel JadwalMcu
            $jadwal = JadwalMcu::create([
                'qr_code_id'          => (string) Str::uuid(), // Generate ID acak unik (UUID) untuk QR Code Validasi
                'tanggal_mcu'         => $tanggal, // Tanggal pelaksanaan MCU
                'tanggal_pendaftaran' => now()->toDateString(), // Tanggal pendaftaran (hari ini)
                'paket_mcus_id'       => $request->paket_mcu, // ID paket MCU yang dipilih
                'dokter_id'           => $dokterId, // ID Dokter jaga
                'no_antrean'          => 'C' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT), // Format antrean (contoh: C001, C002)
                'status'              => 'Scheduled', // Status awal: Terjadwal
                'nama_pasien'         => $user->nama ?? $user->nama_lengkap, // Menarik nama pasien otomatis
                'nik_pasien'          => $user->nik ?? $user->nik_pasien, // Menarik NIK pasien otomatis
                'no_sap'              => $user->no_sap ?? null, // Menarik Nomor SAP (khusus Karyawan)
                'perusahaan_asal'     => $user->perusahaan ?? 'Pribadi', // Menarik data nama perusahaan
                'karyawan_id'         => ($column == 'karyawan_id') ? $user->id : null, // Simpan ID Relasi Karyawan jika user adalah karyawan
                'peserta_mcus_id'     => ($column == 'peserta_mcus_id') ? $user->id : null, // Simpan ID Relasi Peserta jika user umum
            ]);

            // Mencari data detail paket MCU yang dipilih
            $paket = \App\Models\PaketMcu::find($request->paket_mcu);
            
            // Jika paket tersebut memiliki relasi dengan jenis-jenis poli tertentu
            if ($paket && $paket->poli()->exists()) {
                // Looping/mengulang semua poli yang termasuk dalam paket tersebut
                foreach ($paket->poli as $itemPoli) {
                    // Buat jadwal spesifik untuk setiap poli (misal: antrean poli mata, antrean poli gigi)
                    \App\Models\JadwalPoli::create([
                        'jadwal_mcus_id' => $jadwal->id, // Hubungkan dengan ID Jadwal MCU utamanya
                        'poli_id'        => $itemPoli->id, // Hubungkan dengan ID Poli
                        'status'         => 'Pending', // Status poli: Pending (belum dikunjungi)
                    ]);
                }
            }

            // Kembalikan respons berhasil ke aplikasi mobile
            return response()->json(['success' => true, 'qr_code_id' => $jadwal->qr_code_id, 'message' => 'Jadwal berhasil dibuat'], 201);

        } catch (ValidationException $e) {
            // Jika validasi gagal (misal tidak pilih paket), kirim pesan error
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            // Jika server error (masalah database dll), kirim pesan error internal
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Fungsi untuk menampilkan daftar riwayat MCU milik user yang sedang login
    public function getRiwayatByUser()
    {
        try {
            // Mengambil data user yang sedang login
            $loginUser = auth('sanctum')->user();
            if (!$loginUser) return response()->json(['message' => 'Unauthenticated'], 401);

            // Mengecek tipe user untuk menentukan kolom relasi yang akan di-query
            if ($loginUser instanceof EmployeeLogin) {
                $column = 'karyawan_id';
                $userId = $loginUser->karyawan_id;
            } elseif ($loginUser instanceof PesertaMcuLogin) {
                $column = 'peserta_mcus_id';
                $userId = $loginUser->peserta_mcu_id;
            } else {
                return response()->json(['message' => 'User tidak dikenali'], 403);
            }

            // Tarik data jadwalMCU yang cocok dengan user tersebut beserta relasi Dokter, Paket, dan Polinya
            $riwayat = JadwalMcu::where($column, $userId)
                ->with(['dokter', 'paketMcu', 'jadwalPoli.poli'])
                ->orderBy('id', 'asc') // Urutkan dari data terlama ke terbaru
                ->get();

            // Mengembalikan data JSON yang sudah diformat rapi melalui class Resource
            return JadwalMcuResource::collection($riwayat);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // Fungsi untuk menggabungkan dan mengunduh seluruh file PDF hasil MCU (Fit to Work)
    public function downloadLaporanGabungan($id)
    {
        // Menarik token dari parameter URL (karena fungsi download browser tidak bisa mengirim Bearer Header)
        $token = request()->query('token');
        if (!$token) {
            return response()->json(['message' => 'Token autentikasi diperlukan'], 401);
        }

        try {
            // Tarik data jadwal MCU beserta semua data relasinya (pasien, unit kerja, dokter, file poli)
            $jadwal = JadwalMcu::with(['jadwalPoli', 'karyawan.departemen', 'karyawan.unitKerja', 'karyawan.provinsi', 'pesertaMcu', 'dokter', 'paketMcu'])
                ->findOrFail($id);

            // Identifikasi pasien (apakah Karyawan atau Pasien MCU Umum)
            $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
            if (!$patient) {
                return response()->json(['message' => 'Data pasien tidak ditemukan'], 404);
            }

            // Format tanggal pelaksanaan MCU
            $tanggalMcuFormatted = Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y');

            // Mengambil pengaturan/variabel dinamis dari database (Nama Kepala Klinik dan Teks Pengesahan)
            $namaKepalaKlinik = \App\Models\Setting::where('key', 'nama_kepala_klinik')->value('value') ?? 'Dr. Penanggung Jawab';
            $teksDisclaimerRaw = \App\Models\Setting::where('key', 'teks_disclaimer')->value('value') ?? 'Pada Pemeriksaan Kesehatan Berkala di Klinik Semen Tonasa Medical Centre yang dilakukan pada tanggal <b>[TANGGAL]</b>...';
            // Mengganti placeholder [TANGGAL] di database dengan tanggal asli pelaksanaan MCU
            $teksDisclaimerFinal = str_replace('[TANGGAL]', $tanggalMcuFormatted, $teksDisclaimerRaw);

            // Fungsi lokal buatan sendiri (closure) untuk mengubah file gambar fisik menjadi kode teks Base64
            // Ini wajib dilakukan agar gambar logo di dalam PDF tidak terputus/gagal dimuat saat digenerate
            $getBase64Image = function($dbPath, $defaultPath) {
                $fullPath = null;
                // Cek jika path disetting database tidak kosong
                if (!empty($dbPath)) {
                    $cleanPath = str_replace('storage/', '', $dbPath); // Bersihkan prefix storage/
                    $storagePath = storage_path('app/public/' . $cleanPath); // Dapatkan path lokal aslinya
                    if (file_exists($storagePath)) $fullPath = $storagePath;
                }
                // Jika path database gagal, gunakan gambar default (fallback)
                if (!$fullPath && file_exists(public_path($defaultPath))) {
                    $fullPath = public_path($defaultPath);
                }
                // Jika file ditemukan, encode isinya menjadi Base64 string
                if ($fullPath) {
                    $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($fullPath);
                    return 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
                return ''; 
            };

            // Mengonversi Logo STMC dan Semen Tonasa ke bentuk Base64 menggunakan fungsi di atas
            $logoStmcBase64 = $getBase64Image(\App\Models\Setting::where('key', 'logo_stmc')->value('value'), 'images/logo-stmc.png');
            $logoTonasaBase64 = $getBase64Image(\App\Models\Setting::where('key', 'logo_tonasa')->value('value'), 'images/logo-semen-tonasa.png');
            
            // Membuat QR Code sebagai Tanda Tangan Digital Pengesahan Dokumen
            $linkValidasiPublik = route('validasi.pdf', $jadwal->qr_code_id); // Buat URL untuk scan validasi QR
            $qrCode = new QrCode($linkValidasiPublik); // Inisialisasi QR Code
            $qrCode->setSize(150); // Set ukuran QR 150px
            $qrCode->setMargin(0); // Hilangkan garis tepi putih pada QR
            $qrCode->setEncoding(new Encoding('UTF-8')); 
            $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High); // Tingkat koreksi tinggi agar QR tetap bisa dibaca meski buram
            $writer = new PngWriter(); // Tulis sebagai PNG
            $result = $writer->write($qrCode);
            // Konversi gambar QR Code menjadi bentuk String Base64 untuk ditampilkan di PDF
            $qrCodeBase64 = base64_encode($result->getString());

            // Menyusun (mapping) semua variabel data yang akan dikirim ke tampilan PDF Blade (HTML)
            $dataResume = [
                'jadwal' => $jadwal,
                'patient' => $patient,
                'patient_data' => [
                    'nama'           => $patient->nama_karyawan ?? $patient->nama_lengkap ?? 'N/A',
                    'tgl_lahir'      => $patient->tanggal_lahir ?? 'N/A',
                    'alamat'         => $patient->alamat ?? 'N/A',
                    'jenis_kelamin'  => $patient->jenis_kelamin ?? 'N/A',
                    'nik_sap'        => $patient->no_sap ?? $patient->nik_karyawan ?? $patient->nik_pasien ?? 'N/A',
                    'paket_mcu'      => $jadwal->paketMcu->nama_paket ?? 'N/A',
                    'unit_kerja'     => $patient->unitKerja->nama_unit_kerja ?? 'N/A',
                ],
                'tanggal_mcu'     => $tanggalMcuFormatted,
                'resume_body_raw' => $jadwal->resume_body, 
                'resume_saran'    => $jadwal->resume_saran,
                'resume_kategori' => $jadwal->resume_kategori,
                'tanggal_cetak'   => now()->translatedFormat('d F Y'),
                'qrCodeBase64'    => $qrCodeBase64,
                
                'doctor_data'     => [
                    'nama' => $jadwal->dokter->nama_lengkap ?? 'Dokter Tidak Ditunjuk',
                    'nip'  => $jadwal->dokter->nip ?? '-'
                ],

                'setting_kepala_klinik' => $namaKepalaKlinik, 
                'setting_disclaimer'  => $teksDisclaimerFinal,
                'setting_logo_stmc'   => $logoStmcBase64, 
                'setting_logo_tonasa' => $logoTonasaBase64,
            ];

            // Inisialisasi class Merger PDF untuk menyatukan banyak file
            $merger = new Merger();

            // Tahap 1: Membuat halaman Sampul/Resume Akhir (Fit to Work)
            $resumePdf = Pdf::loadView('pdfs.mcu_resume', $dataResume)->output(); // Render HTML ke byte PDF
            $merger->addRaw($resumePdf); // Tambahkan ke dalam penampung Merger

            // Tahap 2: Mengambil semua dokumen hasil pemeriksaan dari masing-masing poli
            $files = $jadwal->jadwalPoli->whereNotNull('file_path')->pluck('file_path')->toArray();

            // Looping untuk menyatukan file poli satu persatu di belakang halaman Resume
            foreach ($files as $filePath) {
                // Mencari file di folder lokal Storage public
                if (Storage::disk('public')->exists($filePath)) {
                    $fileContent = Storage::disk('public')->get($filePath);
                    $merger->addRaw($fileContent);
                } 
                // Fallback: Mencari file jika disetting ke Cloud Amazon S3
                elseif (Storage::disk('s3')->exists($filePath)) {
                    $fileContent = Storage::disk('s3')->get($filePath);
                    $merger->addRaw($fileContent);
                } else {
                    \Log::warning("File poli tidak ditemukan saat diunduh via HP: " . $filePath); // Catat log error
                }
            }

            // Eksekusi penggabungan (Merge) seluruh file
            $output = $merger->merge();
            
            // Membuat nama file yang aman dari karakter khusus
            $namaAman = $patient->nama_karyawan ?? $patient->nama_lengkap ?? 'Pasien';
            $safeName = Str::slug($namaAman); // Menghapus spasi menjadi strip (-)
            $fileName = "Laporan_MCU_{$safeName}_{$jadwal->no_antrean}.pdf";

            // Mengirim file PDF kembali ke perangkat mobile (inline: agar bisa dilihat langsung di browser)
            return response($output)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline; filename=\"$fileName\"");

        } catch (\Exception $e) {
            // Tangkap dan catat error jika proses generate PDF gagal
            \Log::error("Gagal Download Laporan Gabungan (API): " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'message' => 'Gagal memproses laporan gabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Fungsi untuk mendapatkan referensi pilihan Paket MCU di Form Mobile
    public function getPaketMcu()
    {
        try {
            // Mengambil ID dan Nama dari tabel paket_mcus, field nama di-alias menjadi 'name'
            $pakets = \DB::table('paket_mcus')
                ->select('id', 'nama_paket as name') 
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pakets
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data paket'
            ], 500);
        }
    }

    // Fungsi bagi Pasien untuk mengambil antrean (Check-In) ketika sampai di depan ruangan Poliklinik
    public function checkInPoli(Request $request)
    {
        // Verifikasi token pengguna yang memindai QR Code Check-in
        $loginUser = auth('sanctum')->user();
        if (!$loginUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Validasi input parameter ID Jadwal Poli yang didapat dari scan QR ruangan Poli
        $request->validate([
            'id_jadwal_poli' => 'required|exists:jadwal_polis,id'
        ]);

        try {
            // Tarik data poli spesifik yang sedang ingin dikunjungi oleh pasien
            $jadwalPoli = \App\Models\JadwalPoli::findOrFail($request->id_jadwal_poli);

            // Validasi: Tolak check-in jika statusnya bukan 'Pending' (artinya sudah pernah check-in/selesai)
            if ($jadwalPoli->status !== 'Pending') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal: Antrean ini sudah diambil atau sudah selesai.'
                ], 400);
            }

            // Algoritma menghitung sisa antrean otomatis pada poli tersebut di tanggal yang sama
            $currentCount = \App\Models\JadwalPoli::where('poli_id', $jadwalPoli->poli_id)
                // Filter tanggal berdasarkan tanggal MCU
                ->whereHas('jadwalMcu', function($query) use ($jadwalPoli) {
                    $query->whereDate('tanggal_mcu', $jadwalPoli->jadwalMcu->tanggal_mcu);
                })
                ->where('status', '!=', 'Canceled') // Abaikan yang dibatalkan
                ->where('status', '!=', 'Pending')  // Hitung jumlah orang yang sudah lebih dulu Check-in (Waiting/Processed/Done)
                ->count();

            // Nomor antrean pasien ini adalah jumlah orang yang sudah check-in ditambah 1
            $nextNumber = $currentCount + 1; 

            // Update status dari Pending menjadi Waiting, dan simpan nomor urut antreannya
            $jadwalPoli->update([
                'status' => 'Waiting',
                'no_antrean_poli' => $nextNumber 
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil mengambil antrean!',
                'data' => [
                    'no_antrean_poli' => $nextNumber
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan di server: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi pengecekan sisa kuota saat user memilih tanggal di kalender aplikasi Mobile
    public function checkKetersediaan(Request $request)
    {
        try {
            // Mengambil tanggal yang di-klik oleh user di aplikasi Flutter
            $tanggal = $request->query('tanggal');

            // 1. Menghitung jumlah pasien yang sudah berhasil daftar pada tanggal tersebut
            $jumlahTerdaftar = \App\Models\JadwalMcu::whereDate('tanggal_mcu', $tanggal)
                ->where('status', '!=', 'Canceled') 
                ->count();
                
            // Hitung sisa kuota (Batas maksimal 30 orang per hari)
            $sisaKuota = 30 - $jumlahTerdaftar;
            // Cegah angka negatif jika terjadi bug
            if ($sisaKuota < 0) $sisaKuota = 0;

            // 2. Mengecek siapa dokter yang bertugas di tanggal tersebut (Teks Default)
            $namaDokter = 'Dokter Piket (Belum Ditentukan)';

            // Pastikan model JadwalDokter eksis agar tidak terjadi crash jika model belum dibuat
            if (class_exists(\App\Models\JadwalDokter::class)) {
                // Tarik jadwal dokter beserta relasi data diri dokternya
                $jadwalDokter = \App\Models\JadwalDokter::whereDate('tanggal', $tanggal)->with('dokter')->first();
                
                // Jika jadwal piket ada, ganti nama dengan nama asli dokter
                if ($jadwalDokter && $jadwalDokter->dokter) {
                    $namaDokter = $jadwalDokter->dokter->nama_lengkap;
                }
            }

            // Kirim respons balasan ke aplikasi mobile untuk ditampilkan di layar
            return response()->json([
                'success' => true,
                'sisa_kuota' => $sisaKuota,
                'dokter' => $namaDokter
            ], 200);

        } catch (\Exception $e) {
            // Jika ada masalah koneksi database, tangkap pesannya
            return response()->json([
                'success' => false,
                'message' => 'Error Server Laravel: ' . $e->getMessage()
            ], 500);
        }
    }
}