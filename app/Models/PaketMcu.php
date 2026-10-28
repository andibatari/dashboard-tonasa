<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketMcu extends Model
{
    use HasFactory;

     protected $fillable = [
        'nama_paket'
    ];

    public function poli()
    {
        return $this->belongsToMany(Poli::class, 'paket_poli');
    }
}
