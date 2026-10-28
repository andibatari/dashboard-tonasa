<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'unit_kerjas';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_unit_kerja',
        'departemens_id',
    ];

    /**
     * Definisi relasi: Satu UnitKerja memiliki banyak Karyawan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'unit_kerjas_id');
    }

     /**
     * Definisi relasi: Satu UnitKerja milik satu Departemen.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemens_id');
    }
}
