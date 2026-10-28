<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysdevAuditFinding extends Model
{
    use HasFactory;

    protected $table = 'sysdev_audit_findings';

    protected $fillable = [
        'no',
        'category',
        'jenis',
        'description',
        'root_cause',
        'correction',
        'corrective_action',
        'file_evidence_desc',
        'file_evidence',
        'audit_review',
        'plan_date',
        'real_date',
        'explanation',
        'pic',
        'status_lengkap',
    ];

    /**
     * Scope filter query helper
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('root_cause', 'like', "%{$search}%")
                  ->orWhere('correction', 'like', "%{$search}%")
                  ->orWhere('corrective_action', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        });

        $query->when($filters['category'] ?? null, function ($query, $category) {
            if ($category !== 'all') {
                $query->where('category', $category);
            }
        });

        $query->when($filters['status_lengkap'] ?? null, function ($query, $status) {
            if ($status !== 'all') {
                $query->where('status_lengkap', $status);
            }
        });
    }
}

