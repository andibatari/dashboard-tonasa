<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class McuSubmissionNotification extends Notification
{
    use Queueable;

    public $karyawan;
    public $targetDate;

    /**
     * Create a new notification instance.
     */
    public function __construct($karyawan, $targetDate)
    {
        $this->karyawan = $karyawan;
        $this->targetDate = $targetDate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'fcm'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
    

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toFCM($notifiable)
    {
        return [
            // KRITIS: Menggunakan fcm_token dari Model Karyawan
            'token' => $notifiable->fcm_token, 
            'notification' => [
                'title' => '🚨 Penting: Segera Ajukan Jadwal MCU',
                'body' => "Departemen Anda ({$this->karyawan->departemen->nama_departemen}) diimbau mengajukan MCU sebelum " . \Carbon\Carbon::parse($this->targetDate)->format('d F Y') . ". Tekan untuk mengajukan.",
            ],
            'data' => [
                'type' => 'mcu_submission_reminder',
            ],
        ];
    }
}
