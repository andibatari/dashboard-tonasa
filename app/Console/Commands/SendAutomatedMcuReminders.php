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
    /**
     * The name and signature of the console command.
     * Option --type=morning atau --type=night memungkinkan pengujian manual kapan saja.
     */
    protected $signature = 'mcu:send-reminders {--type= : Tipe pengingat: morning (jam 8 pagi) atau night (jam 8 malam)}';
    
    /**
     * The console command description.
     */
    protected $description = 'Otomatis mengirim push notifikasi pengingat Jadwal MCU (Jam 20:00 malam wajib puasa, Jam 08:00 pagi jadwal hari ini)';

    public function handle()
    {
        $timezone = 'Asia/Makassar'; // WITA
        $forcedType = $this->option('type');
        
        $jamSekarang = Carbon::now($timezone)->hour;

        // Tentukan mode berdasarkan parameter opsi atau jam server sekarang
        if ($forcedType === 'night' || ($forcedType === null && $jamSekarang == 20)) {
            // Pengingat Jam 20:00 Malam untuk MCU Besok (Wajib Puasa)
            $targetTanggal = Carbon::tomorrow($timezone)->toDateString();
            $title = "🌙 Pengingat: Besok Jadwal MCU Anda (Wajib Puasa)!";
            $isNight = true;
        } elseif ($forcedType === 'morning' || ($forcedType === null && $jamSekarang == 8)) {
            // Pengingat Jam 08:00 Pagi untuk MCU Hari Ini
            $targetTanggal = Carbon::today($timezone)->toDateString();
            $title = "🏥 Pengingat: Hari Ini Jadwal Medical Check Up Anda!";
            $isNight = false;
        } else {
            $this->info("Bukan jam pengiriman pengingat MCU (Hanya jam 08:00 pagi dan 20:00 malam WITA). Gunakan --type=morning atau --type=night untuk pengujian manual.");
            return 0;
        }

        // Ambil data jadwal MCU yang berstatus Scheduled pada tanggal target
        $jadwalTarget = JadwalMcu::whereDate('tanggal_mcu', $targetTanggal)
                                ->where('status', 'Scheduled') 
                                ->with(['karyawan', 'pesertaMcu']) 
                                ->get();

        $totalTarget = $jadwalTarget->count();
        $successCount = 0;
        
        if ($totalTarget > 0) {
            foreach ($jadwalTarget as $jadwal) {
                $targetUser = $jadwal->karyawan ?? $jadwal->pesertaMcu;

                if ($targetUser && !empty($targetUser->fcm_token)) {
                    $nama = $targetUser->nama_karyawan ?? $targetUser->nama_lengkap ?? 'Peserta MCU';
                    
                    if ($isNight) {
                        $body = "Halo, {$nama}! 👋\n\n"
                              . "Mengingatkan bahwa BESOK adalah jadwal Medical Check Up Anda di Klinik STMC.\n\n"
                              . "⚠️ PERSIAPAN WAJIB PUASA:\n"
                              . "• Wajib berpuasa 10–12 jam sebelum pengambilan darah (hanya diperbolehkan minum air putih).\n"
                              . "• Istirahat yang cukup dan hindari begadang malam ini.\n"
                              . "• Bawa KTP / ID Card Perusahaan besok pagi.\n\n"
                              . "Buka aplikasi STMC untuk melihat e-tiket dan kode QR antrean Anda.";
                    } else {
                        $body = "Halo, {$nama}! 👋\n\n"
                              . "HARI INI adalah jadwal Medical Check Up Anda di Klinik Semen Tonasa (STMC).\n\n"
                              . "Silakan hadir tepat waktu dan tunjukkan QR Code pada menu Jadwal MCU di aplikasi STMC kepada petugas klinik.\n\n"
                              . "Semoga pemeriksaan kesehatan Anda berjalan lancar!";
                    }

                    $actionLink = 'route:/informasi-mcu'; 
                    $recipientSap = $targetUser->no_sap ?? $targetUser->nik_karyawan ?? $targetUser->nik_pasien ?? 'ALL';

                    $isSent = FCMService::sendPushNotification(
                        $targetUser->fcm_token,
                        $title,
                        $body,
                        $actionLink,
                        $recipientSap,
                        'pengingat_mcu'
                    );

                    if ($isSent) {
                        $successCount++;
                    }
                }
            }
        }

        // Catat riwayat ke database NotificationLog
        try {
            NotificationLog::create([
                'mode' => 'automatic',
                'scheduled_date' => $targetTanggal,
                'total_targets' => $totalTarget,
                'fcm_success' => $successCount,
                'email_success' => 0,
                'admin_users_id' => null, 
            ]);
            
            $waktuKirim = $isNight ? "Malam (Puasa)" : "Pagi (Hari Ini)";
            Log::info("CRON MCU SUKSES: Notifikasi pengingat {$waktuKirim} terkirim. Target: {$totalTarget}, Berhasil: {$successCount}");
            $this->info("CRON SUKSES: Pengingat {$waktuKirim} selesai. Total Target: {$totalTarget}, FCM Berhasil: {$successCount}");
        } catch (\Exception $e) {
            Log::error("Gagal mencatat log notifikasi otomatis: " . $e->getMessage());
            $this->error("Gagal mencatat riwayat log: " . $e->getMessage());
        }

        return 0;
    }
}
