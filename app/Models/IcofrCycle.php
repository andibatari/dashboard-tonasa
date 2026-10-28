<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IcofrCycle extends Model
{
    protected $fillable = ['code', 'name', 'bpm_file_path'];

    public function processes()
    {
        return $this->hasMany(IcofrProcess::class);
    }
}