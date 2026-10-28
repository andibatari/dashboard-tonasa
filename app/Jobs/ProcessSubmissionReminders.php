<?php

namespace App\Jobs;

use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessSubmissionReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientIds; 
    protected $notificationLog;

    public function __construct(array $recipientIds, $log)
    {
        $this->recipientIds = $recipientIds;
        $this->notificationLog = $log;
    }

    public function handle()
    {
        $successEmailCount = 0;
        $successAppCount = 0;
        $failedRecipients = [];

        foreach ($this->recipientIds as $targetId) {
            $type = 'K'; 
            $id = $targetId;

            if (strpos((string)$targetId, '_') !== false) {
                $parts = explode('_', $targetId);
                if (count($parts) === 2) {
                    $type = $parts[0];
                    $id = $parts[1];
                }
            }

            $userTarget = null;
            $email = null;
            $fcmToken = null;
            $nik = 'N/A';

            if ($type === 'K') {
                $userTarget = Karyawan::with('departemen')->find($id);
                if ($userTarget) {
                    $email = $userTarget->email_karyawan ?? $userTarget->email ?? null;
                    $fcmToken = $userTarget->fcm_token ?? null;
                    $nik = $userTarget->nik_karyawan ?? 'N/A';
                }
            } elseif ($type === 'P') {
                $userTarget = PesertaMcu::find($id);
                if ($userTarget) {
                    $email = $userTarget->email ?? null;
                    $fcmToken = $userTarget->fcm_token ?? null;
                    $nik = $userTarget->nik_pasien ?? 'N/A';
                }
            }

            if (!$userTarget) {
                $failedRecipients[] = ['nik' => $targetId, 'type' => 'All', 'reason' => 'Data pengguna tidak ditemukan.'];
                continue;
            }

            if ($email) {
                try {
                    Mail::to($email)->send(new SubmissionReminderMail($userTarget, $this->notificationLog->scheduled_date));
                    $successEmailCount++;
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim EMAIL pengajuan MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'Email', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            }

            if ($fcmToken) {
                try {
                    $nama = $userTarget->nama_karyawan ?? $userTarget->nama_lengkap ?? 'Karyawan';
                    
                    $title = "📢 Yuk, Atur Jadwal MCU Kamu!";
                    $body = "Halo {$nama}! 👋\n\n"
                          . "Kami perhatikan kamu belum menentukan jadwal Medical Check Up di Klinik STMC. "
                          . "Jika kebetulan besok kamu ada waktu kosong, yuk segera ajukan jadwal MCU kamu sekarang!\n\n"
                          . "Kesehatanmu adalah prioritas kami. 🏥✨\n"
                          . "Klik tombol di bawah ini untuk memilih jadwal ya! 👇";
                          
                    $actionLink = 'route:/pengajuan-mcu'; 
                    // 🌟 Ambil identitas unik user
                    $recipientSap = $userTarget->no_sap ?? $userTarget->nik_karyawan ?? $userTarget->nik_pasien ?? 'ALL';

                    $statusFCM = \App\Services\FCMService::sendPushNotification(
                        $fcmToken,
                        $title,
                        $body,
                        $actionLink,
                        $recipientSap // 🌟 Masukkan ke parameter baru
                    );

                    if ($statusFCM) {
                        $successAppCount++;
                    }
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim FCM pengajuan MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            } else {
                 $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => 'FCM Token tidak tersedia.'];
            }
        }

        try {
            $this->notificationLog->update([
                'email_success' => $successEmailCount,
                'fcm_success' => $successAppCount,
                'failed_recipients' => $failedRecipients,
            ]);
        } catch (Throwable $e) {
            Log::error("Gagal memperbarui NotificationLog. Error: " . $e->getMessage());
        }
    }

    public function failed(Throwable $exception)
    {
        Log::critical("Job ProcessSubmissionReminders GAGAL TOTAL. Error: " . $exception->getMessage());
    }
}