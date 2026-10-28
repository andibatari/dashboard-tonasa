<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataResult extends Model
{
    use HasFactory;

    protected $table = 'mata_results';
    protected $guarded = ['id'];

    protected $casts = [
        'data_mata' => 'array',
    ];

    public function jadwalPoli()
    {
        return $this->belongsTo(JadwalPoli::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }
}