<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalMcu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\FCMService;
use App\Models\NotificationLog;

class SendAutomatedMcuReminders extends Command
{
    protected $signature = 'mcu:send-reminders';
    
    protected $description = 'Otomatis mengirim notifikasi pengingat Jadwal MCU (H-1 Jam 19:00 & H-H Jam 06:00)';

    public function handle()
    {
        $timezone = 'Asia/Makassar';
        
        // 1. KITA HANYA CEK JAM (Mengabaikan Menit agar tidak pernah meleset)
        $jamSekarang = Carbon::now($timezone)->hour;

        // 2. Tentukan Tanggal Target & Teks
        if ($jamSekarang == 20) {
            $targetTanggal = Carbon::tomorrow($timezone)->toDateString();
            $waktuTeks = "BESOK";
            $title = "⏰ Pengingat: Besok Jadwal MCU Anda!";
        } elseif ($jamSekarang == 9) { 
            $targetTanggal = Carbon::today($timezone)->toDateString();
            $waktuTeks = "PAGI INI";
            $title = "⏰ Hari Ini Jadwal MCU Anda!";
        } else {
            return; // Berhenti jika bukan jam jadwalnya
        }

        // 3. Tarik Data Jadwal
        $jadwalTarget = JadwalMcu::whereDate('tanggal_mcu', $targetTanggal)
                                ->where('status', 'Scheduled') 
                                ->with(['karyawan', 'pesertaMcu']) 
                                ->get();

        $totalTarget = $jadwalTarget->count();
        $successCount = 0;
        
        // 4. Proses Kirim FCM (HANYA berjalan jika ada target)
        if ($totalTarget > 0) {
            foreach ($jadwalTarget as $jadwal) {
                $targetUser = $jadwal->karyawan ?? $jadwal->pesertaMcu;

                if ($targetUser && !empty($targetUser->fcm_token)) {
                    $nama = $targetUser->nama_karyawan ?? $targetUser->nama_lengkap ?? 'Peserta MCU';
                    
                    $body = "Halo, {$nama}! 👋\n"
                          . "Kami mengingatkan bahwa {$waktuTeks} adalah jadwal Medical Check Up Anda di Klinik STMC.\n\n"
                          . "🌟 PERSIAPAN WAJIB:\n"
                          . "• 💧 Wajib puasa 10-12 jam sebelum ambil darah (hanya boleh minum air putih).\n"
                          . "• 😴 Hindari begadang dan istirahat yang cukup.\n"
                          . "• 🪪 Jangan lupa bawa KTP / ID Card Perusahaan.\n\n"
                          . "Klik tombol di bawah untuk panduan lengkapnya! 👇";

                    $actionLink = 'route:/informasi-mcu'; 
                    $recipientSap = $targetUser->no_sap ?? $targetUser->nik_karyawan ?? $targetUser->nik_pasien ?? 'ALL';

                    $isSent = FCMService::sendPushNotification(
                        $targetUser->fcm_token,
                        $title,
                        $body,
                        $actionLink,
                        $recipientSap
                    );

                    if ($isSent) {
                        $successCount++;
                    }
                }
            }
        }

        // 🌟 5. PAKSA SIMPAN KE RIWAYAT 
        // Kode ini PASTI dieksekusi, meskipun $totalTarget adalah 0 (tidak ada jadwal)
        try {
            NotificationLog::create([
                'mode' => 'automatic',
                'scheduled_date' => $targetTanggal,
                'total_targets' => $totalTarget,
                'fcm_success' => $successCount,
                'email_success' => 0,
                'admin_users_id' => null, 
            ]);
            
            Log::info("CRON SUKSES: Riwayat otomatis berhasil dicatat. Total Target: {$totalTarget}");
            $this->info("CRON SUKSES: Riwayat otomatis berhasil dicatat.");
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan riwayat otomatis: " . $e->getMessage());
            $this->error("GAGAL SIMPAN RIWAYAT: " . $e->getMessage());
        }
    }
}