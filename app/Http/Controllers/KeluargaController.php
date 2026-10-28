<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keluarga;
use App\Models\PesertaMcu;

class KeluargaController extends Controller
{
    public function show(PesertaMcu $pesertaMcu)
    {
        return view('keluarga.show', compact('pesertaMcu'));
    }
    public function edit(Keluarga $keluarga)
    {
        return view('keluarga.edit', compact('keluarga'));
    }

    public function destroy(Keluarga $keluarga)
    {
        $keluarga->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data keluarga berhasil dihapus.');
    }

     // --- METODE API KHUSUS UNTUK FLUTTER ---
    
    /**
     * Mengambil data keluarga yang terkait dengan pengguna yang sedang login (API SHOW BY USER).
     * Endpoint: /api/keluarga/user
     * Catatan: Membutuhkan middleware 'auth:sanctum'.
     */
    public function apiShowByUser(Request $request)
    {
        // Asumsi: Anda dapat mengakses model Karyawan dari user yang login, 
        // atau relasi PesertaMcu/Keluarga terhubung ke user ID.
        
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak terautentikasi.'], 401);
        }

        try {
            // Asumsi: User model memiliki relasi ke Karyawan, dan Karyawan memiliki relasi ke Keluarga
            // Jika user adalah Karyawan:
            $karyawan = $user->karyawan; // Asumsi ada relasi 'karyawan' di model User/EmployeeLogin
            
            if (!$karyawan) {
                return response()->json(['status' => 'error', 'message' => 'Data karyawan tidak ditemukan untuk pengguna ini.'], 404);
            }

            // Ambil semua data keluarga (suami/istri/anak) yang terkait dengan karyawan ini
            $dataKeluarga = Keluarga::where('karyawan_id', $karyawan->id)->get();
            
            // Alternatif: Ambil data PesertaMcu yang terdaftar atas nama karyawan (jika Keluarga adalah alias untuk PesertaMcu)
            // $dataKeluarga = PesertaMcu::where('karyawan_id', $karyawan->id)->get();


            return response()->json([
                'status' => 'success',
                'message' => 'Data keluarga berhasil diambil.',
                'karyawan_id' => $karyawan->id,
                'data' => $dataKeluarga,
            ]);

        } catch (\Exception $e) {
             return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengambil data keluarga.'], 500);
        }
    }

}