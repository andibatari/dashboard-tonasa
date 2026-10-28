<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['code_dept', 'name_dept', 'leader_title', 'leader_name', 'is_icofr_pic'];

    protected $casts = [
        'is_icofr_pic' => 'boolean',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
