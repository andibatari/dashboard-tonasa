<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalDokter;

class JadwalDokterApiController extends Controller
{
    public function getEvents()
    {
        $jadwal = JadwalDokter::with('dokter')->get();

        return response()->json($jadwal->map(function ($item) {
            return [
                'title' => $item->dokter->nama_lengkap ?? 'Dokter', // Nama dokter
                'start' => $item->tanggal,                          // Tanggal
                'backgroundColor' => $item->dokter->color ?? '#ef4444',
                'borderColor' => $item->dokter->color ?? '#ef4444',
            ];
        }));
    }
}