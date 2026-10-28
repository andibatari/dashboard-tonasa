<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IcofrSubProcess extends Model
{
    protected $fillable = ['icofr_process_id', 'code', 'name'];

    public function process(): BelongsTo
    {
        return $this->belongsTo(IcofrProcess::class, 'icofr_process_id');
    }

    public function controls(): HasMany
    {
        return $this->hasMany(IcofrControl::class, 'icofr_sub_process_id');
    }
}