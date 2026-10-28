<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'notification_logs';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'scheduled_date',
        'mode', // 'manual' atau 'automatic'
        'total_targets',
        'email_success',
        'fcm_success',
        'failed_recipients',
        'admin_users_id', // Ini sesuai dengan foreign key di migrasi Anda
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data native.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_date' => 'date',
        'failed_recipients' => 'array', // Penting: Menyimpan data gagal sebagai array JSON
    ];

    // --- Relasi ---

    /**
     * Relasi ke AdminUser, menunjukkan admin yang memicu pengiriman notifikasi manual.
     * Kolom foreign key adalah 'admin_users_id'.
     */
    public function admin(): BelongsTo
    {
        // Pastikan Model AdminUser Anda berada di App\Models\AdminUser
        // atau namespace yang sesuai jika berbeda.
        return $this->belongsTo(AdminUser::class, 'admin_users_id');
    }
}