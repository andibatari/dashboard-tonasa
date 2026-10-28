<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\JadwalMcu;

class JadwalMcuNotification extends Notification
{
    use Queueable;

    public $jadwalMcu;

    /**
     * Buat instance notifikasi baru.
     * Metode ini akan menerima objek JadwalMcu.
     * @param JadwalMcu $jadwalMcu
     */
    public function __construct(JadwalMcu $jadwalMcu)
    {
        $this->jadwalMcu = $jadwalMcu;
    }

    /**
     * Tentukan kanal (channel) pengiriman notifikasi.
     * Di sini kita menggunakan 'database' untuk menyimpan notifikasi di tabel 'notif'.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Dapatkan representasi notifikasi dalam bentuk array untuk disimpan di database.
     * Ini akan menjadi isi dari kolom 'data' di tabel 'notif'.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'Jadwal MCU',
            'judul' => 'Jadwal MCU Anda telah terbit!',
            'pesan' => 'Jadwal MCU Anda untuk tanggal ' . $this->jadwalMcu->tanggal_mcu->format('d F Y') . ' telah tersedia.',
            'jadwal_mcu_id' => $this->jadwalMcu->id,
            'tanggal_mcu' => $this->jadwalMcu->tanggal_mcu,
            'status' => $this->jadwalMcu->status,
        ];
    }
}
