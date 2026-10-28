<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = ['code_unit', 'name_unit', 'department_id'];

    // Relasi: Unit ini milik satu Departemen
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Relasi: Unit ini memiliki banyak User/Karyawan
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
