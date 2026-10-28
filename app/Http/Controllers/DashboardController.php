<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalMcu;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\UnitKerja;
use App\Models\Departemen;
use App\Models\Dokter;
// Pastikan Anda mengimpor Model Pemantauan Lingkungan Anda
use App\Models\PemantauanLingkungan; // <-- GANTI dengan nama Model Anda yang sebenarnya
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama dengan semua data statistik.
     */
    public function index(Request $request)
    {
        // Statistik Kartu (diasumsikan sudah ada, kita tambahkan Pasien Hari Ini)
        $totalKaryawan = Karyawan::count();
        $totalPesertaMcu = PesertaMcu::count();
        $totalUnitKerja = UnitKerja::count();
        $totalDepartemen = Departemen::count();
        $totalDokter = Dokter::count();
        
        $today = Carbon::today()->toDateString();
        $totalPasienHariIni = JadwalMcu::whereDate('tanggal_mcu', $today)->count();
        $totalJadwalMcu = JadwalMcu::count();

        // ========================================================
        // 🌟 FITUR BARU 1: DATA KINERJA KLINIK HARI INI
        // ========================================================
        // Asumsi: Pasien yang sedang MCU hari ini, tetapi kolom 'kategori_resume' nya masih kosong
        // (GANTI 'kategori_resume' dengan nama kolom yang benar di tabel database jadwal_mcus kamu)
        $pasienMenungguResume = JadwalMcu::whereDate('tanggal_mcu', $today)
                                         ->where('status', 'Present') 
                                         ->whereNull('resume_kategori') // <-- GANTI INI JIKA NAMA KOLOMNYA BEDA
                                         ->count();
                                         
        // Asumsi: Resume selesai = jadwal yang statusnya sudah 'Finished' dan di-update hari ini
        $resumeSelesaiHariIni = JadwalMcu::whereDate('updated_at', $today)
                                         ->where('status', 'Finished')
                                         ->count();

        // ========================================================
        // 🌟 FITUR BARU 2: DATA GRAFIK KELAYAKAN KERJA (TAHUN BERJALAN)
        // ========================================================
        // Tangkap tahun dari request, jika kosong gunakan tahun ini
        $tahunFilter = $request->input('tahun_kelayakan', Carbon::now()->year);
        
        // Ambil data kelayakan dari tabel JadwalMcu
        // (GANTI 'resume_kategori' dengan nama kolom yang benar di database kamu)
        $kelayakan = JadwalMcu::select('resume_kategori', DB::raw('count(*) as total'))
            ->whereYear('tanggal_mcu', $tahunFilter)
            ->whereNotNull('resume_kategori')
            ->groupBy('resume_kategori')
            ->pluck('total', 'resume_kategori')
            ->toArray();

        // Memetakan hasil inputan dokter ke dalam 3 kategori utama grafik
        // Karena dokter menginput teks manual (misal: "Fit With Note (K2)"), 
        // kita menggunakan simbol ?? untuk menangkap berbagai variasi ketikan dokter.
        $dataKelayakan = [
            'Fit To Work (K1)' => 
                ($kelayakan['Fit To Work (K1)'] ?? 0) + ($kelayakan['Fit to Work'] ?? 0) + ($kelayakan['Fit To Work'] ?? 0),
                
            'Fit With Note (K2)' => 
                ($kelayakan['Fit With Note (K2)'] ?? 0) + ($kelayakan['Fit with Note'] ?? 0) + ($kelayakan['Fit With Note'] ?? 0),
                
            'Fit With Restrictive (K3)' => 
                ($kelayakan['Fit With Restrictive (K3)'] ?? 0),
                
            'Temporary Unfit (K4)' => 
                ($kelayakan['Temporary Unfit (K4)'] ?? 0),
                
            'Unfit (K5)' => 
                ($kelayakan['Unfit (K5)'] ?? 0) + ($kelayakan['Unfit'] ?? 0),
        ];

        // 1. Analitik Data Grafik Tahunan (Perbedaan Karyawan vs Non-Karyawan)
        $analytics = $this->getMcuAnalytics();
        
        // 2. Analitik Sisa Karyawan
        $karyawanMcuStatus = $this->getKaryawanMcuStatus();
        
        // 3. Analitik Pemantauan Lingkungan BARU
        $lingkunganStatus = $this->getLingkunganMonitoringStatus(); // <-- PANGGIL FUNGSI BARU

        return view('dashboard.index', [
            'totalKaryawan' => $totalKaryawan,
            'totalPesertaMcu' => $totalPesertaMcu,
            'totalUnitKerja' => $totalUnitKerja,
            'totalDepartemen' => $totalDepartemen,
            'totalDokter' => $totalDokter,
            'totalPasienHariIni' => $totalPasienHariIni,
            'totalJadwalMcu' => $totalJadwalMcu,
            'pasienMenungguResume' => $pasienMenungguResume,
            'resumeSelesaiHariIni' => $resumeSelesaiHariIni,
            
            // Kirim data filter tahun ke View
            'dataKelayakan' => $dataKelayakan,
            'tahunFilter' => $tahunFilter,

            // Data Grafik
            'mcuCountsByYear' => $analytics['mcuCountsByYear'],
            'karyawanCounts' => $analytics['karyawanCounts'],
            'nonKaryawanCounts' => $analytics['nonKaryawanCounts'],
            'years' => $analytics['years'],
            
            // Data Sisa Karyawan
            'totalKaryawan' => $karyawanMcuStatus['totalKaryawan'],
            'karyawanSudahMcu' => $karyawanMcuStatus['sudahMcu'],
            'karyawanBelumMcu' => $karyawanMcuStatus['belumMcu'],
            'persenSelesai' => $karyawanMcuStatus['persenSelesai'],
            
            // Data Pemantauan Lingkungan BARU
            'areaBermasalah' => $lingkunganStatus['areaBermasalah'], // <-- KIRIM KE VIEW
        ]);
    }
    
    // ... (Fungsi getMcuAnalytics)
    private function getMcuAnalytics()
    {
        // ... (Logika yang sudah ada)
        // Mengambil total MCU per tahun dan tipe pasien
        $results = JadwalMcu::select(
            DB::raw('YEAR(tanggal_mcu) as year'),
            DB::raw('COUNT(CASE WHEN karyawan_id IS NOT NULL THEN 1 END) as karyawan_count'),
            DB::raw('COUNT(CASE WHEN peserta_mcus_id IS NOT NULL THEN 1 END) as non_karyawan_count')
        )
        ->where('status', 'Finished') // Hanya menghitung yang sudah selesai
        ->groupBy('year')
        ->orderBy('year', 'asc')
        ->get();

        $years = $results->pluck('year')->toArray();
        $karyawanCounts = $results->pluck('karyawan_count')->toArray();
        $nonKaryawanCounts = $results->pluck('non_karyawan_count')->toArray();
        
        // Total gabungan untuk grafik kedua
        $mcuCountsByYear = $results->map(fn($item) => $item->karyawan_count + $item->non_karyawan_count)->toArray();

        return compact('mcuCountsByYear', 'karyawanCounts', 'nonKaryawanCounts', 'years');
    }
    
    // ... (Fungsi getKaryawanMcuStatus)
    private function getKaryawanMcuStatus()
    {
        $totalKaryawan = Karyawan::count();
        
        // Tentukan batas waktu (misalnya, 1 tahun terakhir)
        $oneYearAgo = Carbon::now()->subYears(1); 
        
        // Ambil ID Karyawan yang sudah MCU dalam satu tahun terakhir
        $karyawanSudahMcuIds = JadwalMcu::where('karyawan_id', '!=', null)
            ->where('status', 'Finished')
            ->where('tanggal_mcu', '>=', $oneYearAgo)
            ->pluck('karyawan_id')
            ->unique();
            
        $sudahMcu = $karyawanSudahMcuIds->count();
        $belumMcu = $totalKaryawan - $sudahMcu;
        $persenSelesai = $totalKaryawan > 0 ? round(($sudahMcu / $totalKaryawan) * 100, 1) : 0;
        
        return compact('totalKaryawan', 'sudahMcu', 'belumMcu', 'persenSelesai');
    }
    
    /**
     * Menghitung area pemantauan lingkungan yang bermasalah (melebihi NAB).
     * Diasumsikan: Kolom 'melebihi_nab' = 1 menandakan masalah.
     * Jika Anda tidak memiliki kolom 'melebihi_nab', Anda harus mengganti logika where().
     * Misalnya, jika kolom adalah 'hasil' dan 'nab' (contoh: where('hasil', '>', DB::raw('nab'))).
     */
    /**
     * Menghitung area pemantauan lingkungan yang bermasalah (melebihi NAB)
     * dengan membandingkan nilai pengukuran dengan NAB yang didefinisikan secara hardcode.
     */
    private function getLingkunganMonitoringStatus()
    {
        // ... (Definisi NAB di sini, jika Anda memutuskan untuk menggunakannya lagi)
        // Saya hapus bagian ini karena sudah dicomment out, pastikan NAB didefinisikan 
        // atau gunakan angka hardcode jika DB::raw('nab_kolom') masih bermasalah.
        
        // Tentukan batas waktu (misalnya, data dalam 30 hari terakhir)
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Dapatkan ID dari Area yang memiliki setidaknya satu parameter MELEBIHI NAB
        $areaBermasalahData = PemantauanLingkungan::select(DB::raw('DISTINCT area')) // Hanya ambil area unik
            // Filter berdasarkan tanggal (opsional)
            // ->where('tanggal_pemantauan', '>=', $thirtyDaysAgo) 
            ->where(function ($query) {
                
                // 1. Logika Cahaya: Hasil pengukuran > NAB Cahaya
                $query->orWhere(
                    DB::raw('CAST(JSON_EXTRACT(data_pemantauan, "$.cahaya") AS DECIMAL)'), '>', DB::raw('nab_cahaya')
                )
                // 2. Logika Bising: Hasil pengukuran > NAB Bising
                ->orWhere(
                    DB::raw('CAST(JSON_EXTRACT(data_pemantauan, "$.bising") AS DECIMAL)'), '>', DB::raw('nab_bising')
                )
                // 3. Logika Debu: Hasil pengukuran > NAB Debu (Gunakan hardcode 10 karena kolom NAB_DEBU mungkin string)
                ->orWhere(
                    DB::raw('CAST(JSON_EXTRACT(data_pemantauan, "$.debu") AS DECIMAL)'), '>', 10 
                )
                // 4. Logika Suhu: Hasil pengukuran > NAB Suhu
                ->orWhere(
                    DB::raw('CAST(JSON_EXTRACT(data_pemantauan, "$.suhu_kering") AS DECIMAL)'), '>', DB::raw('nab_suhu')
                );
            })
            ->get(); // Ambil seluruh hasilnya

        $areaBermasalahCount = $areaBermasalahData->count();
        $areaBermasalahNames = $areaBermasalahData->pluck('area')->toArray(); // Ambil array nama area

        return [
            'areaBermasalah' => $areaBermasalahCount,
            'areaNames' => $areaBermasalahNames, // <-- Data Baru
        ];
    }

    /**
     * Mengembalikan data status pemantauan lingkungan dalam format JSON untuk AJAX.
     */
    public function getLingkunganDataJson()
    {
        $lingkunganStatus = $this->getLingkunganMonitoringStatus();
        
        return response()->json([
            'areaBermasalah' => $lingkunganStatus['areaBermasalah'],
            'areaNames' => $lingkunganStatus['areaNames'], // <-- Data Baru
        ]);
    }
        
}