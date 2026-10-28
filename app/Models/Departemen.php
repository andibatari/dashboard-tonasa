<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;
use App\Models\UnitKerja;
/**
 * Model Departemen
 *
 * @property int $id
 * @property string $nama_departemen
 */

class Departemen extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'departemens';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_departemen',
    ];

    /**
     * Definisi relasi: Satu Departemen memiliki banyak Karyawan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'departemens_id');
    }

    /**
     * Tambahkan relasi ini: Satu Departemen memiliki banyak UnitKerja.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unitKerjas()
    {
        return $this->hasMany(UnitKerja::class, 'departemens_id');
    }
}
