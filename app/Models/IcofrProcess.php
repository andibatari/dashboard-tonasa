<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IcofrProcess extends Model
{
    protected $fillable = [
        'icofr_cycle_id', 'code', 'name',
        'dokumentasi_done', 'validasi_done', 'bpm_updated', 'rcm_updated'
    ];

    public function cycle()
    {
        return $this->belongsTo(IcofrCycle::class, 'icofr_cycle_id');
    }

    public function controls()
    {
        return $this->hasMany(IcofrControl::class);
    }

    public function subProcesses()
    {
        return $this->hasMany(IcofrSubProcess::class, 'icofr_process_id');
    }
}