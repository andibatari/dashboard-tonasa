<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IcofrPic extends Model
{
    use HasFactory;

    protected $table = 'icofr_pics';

    protected $fillable = [
        'name',
        'position',
        'unit',
        'department',
        'phone',
        'notes',
    ];

    /**
     * Scope untuk pencarian PIC
     */
    public function scopeSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('position', 'like', "%{$keyword}%")
              ->orWhere('unit', 'like', "%{$keyword}%")
              ->orWhere('department', 'like', "%{$keyword}%");
        });
    }
}

