<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class OAuthController extends Controller
{
    /**
     * Provider yang didukung: google & microsoft (azure)
     */
    protected array $allowedProviders = ['google', 'microsoft'];

    /**
     * Arahkan pengguna ke halaman otorisasi penyedia OAuth (Google / Microsoft SIG).
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            return redirect()->route('login')->withErrors(['oauth' => 'Metode login tidak didukung.']);
        }

        $driver = $provider === 'microsoft' ? 'azure' : $provider;

        try {
            return Socialite::driver($driver)->redirect();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("OAuth Redirect Error [{$provider}]: " . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'oauth' => "Gagal menghubungkan ke layanan login {$provider}. Pastikan konfigurasi OAuth telah diisi pada file .env."
            ]);
        }
    }

    /**
     * Tangani callback balik dari Google / Microsoft SIG setelah pengguna login.
     */
    public function callback(Request $request, string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            return redirect()->route('login')->withErrors(['oauth' => 'Metode login tidak didukung.']);
        }

        $driver = $provider === 'microsoft' ? 'azure' : $provider;

        try {
            // Ambil data user dari penyedia OAuth
            $oauthUser = Socialite::driver($driver)->user();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("OAuth Callback Error [{$provider}]: " . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'oauth' => 'Otentikasi dibatalkan atau terjadi gangguan saat berkomunikasi dengan server login. Silakan coba lagi.'
            ]);
        }

        $email = $oauthUser->getEmail();
        if (empty($email)) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Akun Anda tidak menyediakan informasi email resmi yang diperlukan sistem.'
            ]);
        }

        $name = $oauthUser->getName() ?: ($oauthUser->getNickname() ?: 'Karyawan Semen Tonasa');

        // 1. Cari user di database berdasarkan email atau ID penyedia
        $user = User::where('email', $email)
            ->orWhere(function ($query) use ($provider, $oauthUser) {
                $query->where('provider', $provider)
                      ->where('provider_id', $oauthUser->getId());
            })
            ->first();

        if ($user) {
            // Perbarui data OAuth jika belum terisi
            $user->update([
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
                'avatar' => $user->avatar ?: $oauthUser->getAvatar(),
            ]);

            // Jika user belum punya role, pasang role 'User'
            if ($user->roles->isEmpty()) {
                $user->assignRole('User');
            }
        } else {
            // 2. Jika user baru, periksa apakah email cocok dengan data Karyawan master
            $karyawan = Karyawan::where('email', $email)->first();

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
                'avatar' => $oauthUser->getAvatar(),
                'unit_id' => $karyawan?->unit_kerjas_id ?? null,
                'email_verified_at' => now(),
            ]);

            // Berikan role standar: User (Karyawan umum)
            $user->assignRole('User');
        }

        // Login ke session
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with(
            'status',
            "Selamat datang, {$user->name}! Anda berhasil masuk menggunakan akun " . ucfirst($provider) . "."
        );
    }
}
