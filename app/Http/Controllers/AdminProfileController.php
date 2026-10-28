<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser; // Model untuk Admin Web
use App\Models\Karyawan; // Model Karyawan untuk data profil mobile

class AdminProfileController extends Controller
{
    // --- METODE API KHUSUS UNTUK FLUTTER ---

    /**
     * Mengambil detail profil karyawan yang sedang login (API SHOW PROFILE).
     * Endpoint: /api/user (Sesuai routes/api.php)
     * Catatan: Menggunakan data Karyawan yang terhubung dengan user yang login.
     */
    public function apiShowProfile(Request $request)
    {
        // $request->user() mengembalikan model User/EmployeeLogin yang sedang login
        $user = $request->user(); 

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Pengguna tidak terautentikasi.'], 401);
        }

        try {
            // Asumsi: User/EmployeeLogin memiliki field 'no_sap' yang sesuai dengan tabel Karyawans
            // Ambil detail Karyawan lengkap dengan semua relasi yang dibutuhkan di Flutter
            $profile = Karyawan::with([
                'departemen', 
                'unitKerja', 
                'provinsi',
                'keluargas', // Relasi keluarga
                'pasangan'   // Relasi pasangan
            ])
            ->where('no_sap', $user->no_sap) // Mencari Karyawan berdasarkan no_sap dari user yang login
            ->firstOrFail(); 

            // Kita juga bisa menambahkan path foto profil yang dapat diakses publik di sini
            if ($profile->foto_profil) {
                // Asumsi: Storage::url() dikonfigurasi untuk public disk
                $profile->foto_profil_url = Storage::url($profile->foto_profil);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data profil berhasil diambil.',
                'data' => $profile,
            ]);

        } catch (\Exception $e) {
            // Jika data karyawan tidak ditemukan atau ada error relasi
            return response()->json([
                'status' => 'error',
                'message' => 'Data profil karyawan tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage(),
            ], 404);
        }
    }
    
    // ----------------------------------------------------------------------
    // --- METODE WEB VIEW UNTUK ADMIN (KODE ASLI ANDA) ---
    // ----------------------------------------------------------------------

    /**
     * Menampilkan form untuk mengedit profil Admin yang sedang login.
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Ambil data admin yang sedang login
        $admin = Auth::guard('admin_users')->user();
        
        // Tampilkan view edit profil dan kirim data admin
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Menyimpan pembaruan data profil Admin (termasuk foto profil, jika diunggah).
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin_users')->user();

        // 1. Validasi Input
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('admin_users', 'email')->ignore($admin->id)],
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maks 2MB
            'password' => 'nullable|string|min:6|confirmed',
        ];

        // Jalankan validasi
        $validatedData = $request->validate($rules); // Menggunakan variabel untuk menyimpan data yang sudah divalidasi

        // 2. Persiapan Data Update (mengambil data yang sudah divalidasi)
        // Kita hanya mengambil 'nama_lengkap' dan 'email' dari request
        $data = $request->only('nama_lengkap', 'email');
        
        // 3. LOGIKA UPLOAD FOTO PROFIL (Menggunakan Storage Disk 'public')
        if ($request->hasFile('foto_profil')) {
        
            // Hapus foto lama JIKA ADA di dalam disk 'public'
            if ($admin->foto_profil && Storage::disk('public')->exists($admin->foto_profil)) {
                Storage::disk('public')->delete($admin->foto_profil);
            }
            
            // Simpan foto baru ke folder 'profile_photos' menggunakan disk 'public'
            $path = $request->file('foto_profil')->store('profile_photos', 'public');

            // Simpan path-nya ke database
            $data['foto_profil'] = $path; 
        }

        // 4. LOGIKA UPDATE PASSWORD
        // Menggunakan $request->filled() memastikan field ada DAN tidak kosong
        if ($request->filled('password')) {
            // Kita menggunakan Hash::make() untuk mengenkripsi password
            $data['password'] = Hash::make($request->input('password'));
        }
        
        // 5. Simpan ke Database
        $admin->update($data);

        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}