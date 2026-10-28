<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use App\Imports\KaryawanImport;
use App\Imports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PesertaMcuImport;
use App\Models\PesertaMcu;

class KaryawanController extends Controller
{
    /**
     * Tampilkan daftar semua karyawan.
     */
    public function index()
    {
        // dan nested relation untuk lokasi
        $karyawans = Karyawan::with(['departemen', 'unitKerja', 'provinsi'])->get();
        return view('karyawan.index', compact('karyawans'));
    }

    /**
     * Tampilkan formulir untuk membuat karyawan baru.
     */
    public function create()
    {
        return view('karyawan.create');
    }

    public function edit(Karyawan $karyawan)
    {
        // Pastikan variabel 'karyawan' dikirim ke view
        return view('karyawan.edit', compact('karyawan'));
    }

    /**
     * Menampilkan detail satu karyawan
     */
    public function show(Karyawan $karyawan)
    {
        return view('karyawan.show', compact('karyawan'));
    }

    /**
     * Hapus data karyawan dari database.
     */
    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_karyawan' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new KaryawanImport, $request->file('file_karyawan'));

        return redirect()->back()->with('success', 'Data karyawan berhasil diimport.');
    }

    public function downloadExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KaryawanExport, 'Data Karyawan.xlsx');
    }

    public function PesertaMcuImport(Request $request)
    {
        $request->validate([
            'file_peserta_mcu' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new PesertaMcuImport, $request->file('file_peserta_mcu'));

        return redirect()->back()->with('success', 'Data pasien berhasil diimport.');
    }
    public function pesertaMcuExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PesertaMcuExport, 'Data Pasien Mcu.xlsx');
    }

    public function addKeluarga($karyawan_id = null)
    {
        $karyawan = null;
        if ($karyawan_id) {
            $karyawan = Karyawan::findOrFail($karyawan_id);
        }

        

        return view('karyawan.addKeluarga', compact('karyawan'));
    }

    /**
     * Simpan data anggota keluarga baru atau pasien non-karyawan ke database.
     * Menggunakan parameter opsional $karyawan_id.
     */
    public function storeKeluarga(Request $request, $karyawan_id = null)
    {
        // Logika validasi dan penyimpanan data di sini
        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:istri,suami,anak1,anak2,anak3,non_karyawan',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        if ($karyawan_id) {
            // Logika untuk menambahkan anggota keluarga karyawan
            $karyawan = Karyawan::findOrFail($karyawan_id);
            if ($request->tipe === 'istri' || $request->tipe === 'suami') {
                $karyawan->suami_istri = $request->nama;
                // Anda dapat menambahkan logika untuk menyimpan jenis kelamin suami/istri jika diperlukan
                // $karyawan->pekerjaan_suami_istri = $request->pekerjaan;
            } elseif ($request->tipe === 'anak1') {
                $karyawan->anak1_nama = $request->nama;
                // $karyawan->anak1_tanggal_lahir = $request->tanggal_lahir;
            }
            // Tambahkan logika untuk anak2 dan anak3
            $karyawan->save();
            return redirect()->route('karyawan.show', $karyawan->id)->with('success', 'Anggota keluarga berhasil ditambahkan!');
        } else {
            // Logika untuk menambahkan pasien non-karyawan
            // Misalnya, simpan ke tabel yang sama atau tabel lain jika diperlukan
            // Contoh:
            // $pasien = new Karyawan();
            // $pasien->nama_karyawan = $request->nama;
            // $pasien->hubungan = 'Non-Karyawan';
            // $pasien->save();
            return redirect()->route('bebas.route.pasien')->with('success', 'Pasien non-karyawan berhasil ditambahkan!');
        }
    }
    
    // --- METODE API KHUSUS UNTUK FLUTTER (INDEX) ---

    /**
     * Mengambil daftar semua karyawan dalam format JSON (API INDEX).
     * Endpoint: /api/karyawan
     */
    public function apiIndex()
    {
        // Ambil data karyawan dengan relasi dasar yang dibutuhkan di LIST view Flutter
        $karyawans = Karyawan::with(['departemen', 'unitKerja'])
                         ->select('id', 'no_sap', 'nama_karyawan', 'departemens_id', 'unit_kerjas_id', 'jabatan', 'email')
                         ->get();
        
        return KaryawanResource::collection($karyawans);
    }

    /**
     * Mengambil detail satu karyawan dalam format JSON (API SHOW).
     * Endpoint: /api/karyawan/{id}
     */
    public function apiShow($id)
    {
        try {
            // Ambil detail lengkap dengan semua relasi yang mungkin dibutuhkan (Lokasi, Kerja, Keluarga)
            $karyawan = Karyawan::with([
                'departemen', 
                'unitKerja',
                'provinsi',
                'keluargas', // Relasi ke PesertaMcu/Keluarga
                'pasangan' // Relasi ke pasangan
            ])->findOrFail($id);
            
            return new KaryawanResource($karyawan);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
             // JIKA TERJADI KESALAHAN SERVER LAINNYA, kembalikan status 500
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}
