<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable(['name', 'email', 'password', 'unit_id', 'provider', 'provider_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]

//Pastikan class User menggunakan trait HasRoles untuk mengaktifkan fitur role dan permission dari package Spatie
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles; // Menggunakan trait HasRoles untuk role dan permission
    use LogsActivity; // Menggunakan trait LogsActivity untuk mencatat aktivitas pengguna

    // Konfigurasi Perekaman Aktivitas
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Rekam semua kolom yang ada di $fillable
            ->logOnlyDirty() // Hanya rekam kolom yang nilainya benar-benar berubah
            ->dontSubmitEmptyLogs() // Jangan buat log jika tidak ada perubahan
            ->setDescriptionForEvent(fn(string $eventName) => "Telah melakukan {$eventName} pada data pengguna");
    }

    // Relasi: User ini milik satu Unit
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
