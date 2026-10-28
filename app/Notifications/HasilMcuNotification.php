<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HasilMcu; // Impor model HasilMcu

class HasilMcuNotification extends Notification
{
    use Queueable;

    public $hasilMcu;

    /**
     * Create a new notification instance.
     * Metode ini menerima objek HasilMcu yang akan digunakan di notifikasi.
     *
     * @param HasilMcu $hasilMcu
     * @return void
     */
    public function __construct(HasilMcu $hasilMcu)
    {
        $this->hasilMcu = $hasilMcu;
    }

    /**
     * Get the notification's delivery channels.
     * Kita menggunakan channel 'database' untuk menyimpan notifikasi ini di tabel `notif`.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     * Metode ini mendefinisikan data yang akan disimpan ke kolom 'data' di tabel `notif`.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Mendapatkan tanggal MCU yang terkait dari jadwalnya
        $tanggalMcu = $this->hasilMcu->jadwalMcu->tanggal_mcu->format('d F Y');
        
        return [
            'type' => 'Hasil MCU',
            'judul' => 'Hasil MCU Anda Sudah Tersedia!',
            'pesan' => "Hasil MCU Anda untuk tanggal $tanggalMcu telah diterbitkan oleh dokter.",
            'hasil_mcu_id' => $this->hasilMcu->id,
            'tanggal_mcu' => $tanggalMcu,
            'kesimpulan' => $this->hasilMcu->kesimpulan_mcu,
        ];
    }
}
