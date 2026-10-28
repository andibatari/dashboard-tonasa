<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    /* Notifikasi ini milik satu Karyawan */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
