<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        // Super Admin bisa melihat role selain Developer
        $roles = Role::where('name', '!=', 'Developer')->get();
        $units = Unit::all();

        return view('admin.accounts.index', compact('users', 'roles', 'units'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'exists:roles,name'],
            'unit_id' => ['nullable', 'exists:units,id'], //Validasi Unit
        ]);

        // 2. Buat Akun dengan Sandi Default (Tonasa2026!)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'unit_id' => $request->unit_id, // Simpan unit_id jika ada
            'password' => Hash::make('Tonasa2026!'),
        ]);

        // 3. Pasang Role
        $user->assignRole($request->role);

        return back()->with('success', 'Akun berhasil ditambahkan dengan sandi default: Tonasa2026!');
    }

    public function update(Request $request, User $user)
    {
        // Proteksi 1: Super Admin tidak boleh mengedit akun Developer
        if ($user->hasRole('Developer') && !auth()->user()->hasRole('Developer')) {
            return back()->with('error', 'Pelanggaran Akses: Anda tidak berhak mengubah akun Developer!');
        }

        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Validasi email ini akan mengabaikan email milik user itu sendiri jika tidak diubah
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'exists:roles,name'],
            'unit_id' => ['nullable', 'exists:units,id'], //Validasi Unit
        ]);

        // Proteksi 2: Mencegah penurunan pangkat Developer menjadi User biasa
        if ($user->hasRole('Developer') && $request->role !== 'Developer') {
            return back()->with('error', 'Peringatan Sistem: Hak akses Developer tidak dapat diturunkan!');
        }

        // 2. Perbarui Data User
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'unit_id' => $request->unit_id, // Perbarui unit_id jika ada
        ]);

        // 3. Sinkronisasi Role (Menghapus role lama & mengganti dengan yang baru)
        $user->syncRoles([$request->role]);

        return back()->with('success', 'Data akun ' . $user->name . ' berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Proteksi tingkat tinggi: Akun Developer tidak boleh dihapus dari web
        if ($user->hasRole('Developer')) {
            return back()->with('error', 'Peringatan Sistem: Akun Developer tidak dapat dihapus!');
        }
        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus secara permanen.');
    }
}