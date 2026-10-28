<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JadwalMcu;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessMcuReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientIds;
    protected $log;

    public function __construct(array $recipientIds, $log)
    {
        $this->recipientIds = $recipientIds;
        $this->log = $log;
    }

    public function handle()
    {
        $jadwals = JadwalMcu::whereIn('id', $this->recipientIds)
                    ->with(['karyawan', 'pesertaMcu'])
                    ->get();
                    
        $fcmSuccessCount = 0;

        foreach ($jadwals as $jadwal) {
            $targetUser = $jadwal->karyawan ?? $jadwal->pesertaMcu;

            if ($targetUser && !empty($targetUser->fcm_token)) {
                $nama = $targetUser->nama_karyawan ?? $targetUser->nama_lengkap ?? 'Peserta MCU';
                
                Carbon::setLocale('id'); 
                $tanggal = Carbon::parse($jadwal->tanggal_mcu)->translatedFormat('l, d F Y');

                $title = "⏰ Pengingat: Jadwal MCU Anda!";
                $body = "Halo, {$nama}! 👋\n"
                    . "Kami mengingatkan bahwa pada {$tanggal} adalah jadwal Medical Check Up Anda di Klinik STMC.\n\n"
                    . "🌟 PERSIAPAN WAJIB:\n"
                    . "• 💧 Wajib puasa 10-12 jam sebelum ambil darah (hanya boleh minum air putih).\n"
                    . "• 😴 Hindari begadang dan istirahat yang cukup.\n"
                    . "• 🪪 Jangan lupa bawa KTP / ID Card Perusahaan.\n\n"
                    . "Klik tombol di bawah untuk panduan lengkapnya! 👇";
                    
                $actionLink = 'route:/informasi-mcu';
                // 🌟 Ambil identitas unik user
                $recipientSap = $targetUser->no_sap ?? $targetUser->nik_karyawan ?? $targetUser->nik_pasien ?? 'ALL';

                try {
                    $statusFCM = FCMService::sendPushNotification(
                        $targetUser->fcm_token,
                        $title,
                        $body,
                        $actionLink,
                        $recipientSap // 🌟 Masukkan ke parameter baru
                    );

                    if ($statusFCM) {
                        $fcmSuccessCount++;
                    }
                } catch (\Throwable $e) {
                    Log::warning("Gagal kirim FCM pengingat jadwal MCU ke {$nama}. Error: " . $e->getMessage());
                }
            }
        }

        $this->log->update([
            'fcm_success' => $fcmSuccessCount
        ]);
    }
}