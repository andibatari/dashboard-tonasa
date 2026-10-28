<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Mengambil log terbaru beserta data user yang melakukan aksi (causer)
        // Menggunakan paginate karena data log akan sangat panjang seiring waktu
        $logs = Activity::with('causer')->latest()->paginate(20);

        return view('admin.activity-logs.index', compact('logs'));
    }
}