<?php

namespace App\Http\Controllers;

use App\Models\JadwalMcu;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function verifyPdf($uuid)
    {
        // Mencari jadwal berdasarkan UUID unik yang terikat pada QR Code
        $jadwal = JadwalMcu::with(['dokter', 'karyawan', 'pesertaMcu'])
                    ->where('qr_code_id', $uuid)
                    ->first();

        // Mengamankan sistem dari percobaan akses dengan UUID palsu
        if (!$jadwal) {
            return abort(404, 'Dokumen Tidak Dikenali atau Palsu.');
        }

        // Mengekstrak nama pasien secara dinamis (Karyawan vs Umum)
        $namaPasien = $jadwal->karyawan_id 
            ? ($jadwal->karyawan->nama_karyawan ?? 'N/A') 
            : ($jadwal->pesertaMcu->nama_lengkap ?? $jadwal->nama_pasien ?? 'N/A');

        return view('validasi-pdf', compact('jadwal', 'namaPasien'));
    }
}