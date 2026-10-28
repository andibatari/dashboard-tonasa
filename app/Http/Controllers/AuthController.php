<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Karyawan; // Asumsi model Karyawan atau EmployeeLogin digunakan
use App\Models\AdminUser;

class AuthController extends Controller
{
    // Logika untuk menampilkan halaman login (WEB)
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Logika untuk menangani proses permintaan login (WEB dan API)
     * Rute /login (WEB) dan /api/login (API) akan memanggil metode ini.
     * Menggunakan Request->expectsJson() untuk membedakan.
     */
    public function login(Request $request)
    {
        // 1. Validasi input menggunakan nama 'login_id' yang baru
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $loginId = $request->login_id;
        $password = $request->password;

        // 2. LOGIKA UTAMA: Cari admin berdasarkan Email ATAU NIK ATAU No. SAP
        $user = AdminUser::where('email', $loginId)
                        ->orWhere('nik', $loginId)
                        ->orWhere('no_sap', $loginId)
                        ->first();

        // 3. Cek apakah user ditemukan DAN passwordnya cocok
        if ($user && Hash::check($password, $user->password)) {
            
            // Catat sesinya menggunakan guard 'admin_users'
            Auth::guard('admin_users')->login($user);
            $request->session()->regenerate();

            // Arahkan ke dashboard
            return redirect('/admin/dashboard')->with('message', 'Login berhasil! Selamat datang, ' . $user->nama_lengkap . '.');
        } 

        // 4. Jika otentikasi gagal, kembalikan ke halaman login
        return back()->withErrors([
             'login_id' => 'Akses ditolak. Email/NIK/No. SAP atau password salah.',
        ])->onlyInput('login_id');
    }

    /**
     * Logika untuk menangani permintaan logout (WEB dan API)
     */
    public function logout(Request $request)
    {

        // --- PENANGANAN WEB ---
        Auth::guard('admin_users')->logout();
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Anda telah berhasil logout.');
    }

    
}