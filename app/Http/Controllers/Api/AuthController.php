<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\EmployeeLogin;
use App\Models\PesertaMcuLogin;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * LOGIN API (Employee & Peserta MCU)
     * POST /api/login
     */
    public function login(Request $request)
    {
        try {
            // 🌟 1. Tambahkan fcm_token agar dizinkan masuk oleh Laravel
            $data = $request->validate([
                'identifier' => 'required|string',
                'password'   => 'required|string',
                'fcm_token'  => 'nullable|string', 
            ]);

            $loginUser = $this->findAndAuthenticateApiUser(
                $data['identifier'],
                $data['password']
            );

            if (!$loginUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Identitas atau password salah'
                ], 401);
            }

            // ==========================================
            // 🌟 2. SIMPAN & BERSIHKAN FCM TOKEN GANDA
            // ==========================================
            if (!empty($data['fcm_token'])) {
                $fcmToken = $data['fcm_token'];

                // Hapus token ini dari perangkat akun lain jika ada yang sama (Anti-Duplikat)
                \App\Models\EmployeeLogin::where('fcm_token', $fcmToken)->update(['fcm_token' => null]);
                \App\Models\PesertaMcuLogin::where('fcm_token', $fcmToken)->update(['fcm_token' => null]);

                // Simpan token langsung ke model tabel login yang sedang aktif saat ini
                $loginUser->fcm_token = $fcmToken;
                $loginUser->save();
            }
            // ==========================================

            // 🔑 BUAT TOKEN SANCTUM
            $token = $loginUser
                ->createToken('api-token')
                ->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'user_profile' => $this->getProfileData($loginUser)
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error saat login',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * AKTIVASI AKUN KARYAWAN (Self-Activation via SAP + NIK)
     * POST /api/register/karyawan
     */
    public function registerKaryawan(Request $request)
    {
        try {
            $data = $request->validate([
                'no_sap'                => 'required|string',
                'nik'                   => 'required|string',
                'password'              => 'required|string|min:6|confirmed',
                'no_hp'                 => 'nullable|string|max:20',
                'email'                 => 'nullable|email|max:255',
                'fcm_token'             => 'nullable|string',
            ], [
                'no_sap.required'       => 'Nomor SAP wajib diisi',
                'nik.required'          => 'Nomor NIK KTP wajib diisi',
                'password.required'     => 'Kata sandi wajib diisi',
                'password.min'          => 'Kata sandi minimal 6 karakter',
                'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok',
            ]);

            $noSap = trim($data['no_sap']);
            $nik = trim($data['nik']);

            // 1. Cari data master karyawan di database Semen Tonasa
            $karyawan = Karyawan::where('no_sap', $noSap)->first();

            if (!$karyawan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nomor SAP tidak terdaftar di sistem PT Semen Tonasa. Pastikan No. SAP benar atau hubungi Admin HC/STMC.'
                ], 404);
            }

            // 2. Verifikasi NIK KTP
            if (!empty($karyawan->nik_karyawan) && trim($karyawan->nik_karyawan) !== $nik) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'NIK KTP tidak cocok dengan data Nomor SAP ini. Pastikan Anda memasukkan NIK resmi Anda.'
                ], 422);
            }

            // 3. Cek apakah akun login sudah pernah diaktifkan sebelumnya
            $existingLogin = EmployeeLogin::where('karyawan_id', $karyawan->id)
                ->orWhere('no_sap', $noSap)
                ->first();

            if ($existingLogin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun untuk No. SAP ini sudah aktif. Silakan langsung login di halaman utama.'
                ], 422);
            }

            // 4. Update data pelengkap karyawan jika sebelumnya kosong
            if (!empty($data['no_hp'])) {
                $karyawan->no_hp = $data['no_hp'];
            }
            if (!empty($data['email'])) {
                $karyawan->email = $data['email'];
            }
            if (empty($karyawan->nik_karyawan)) {
                $karyawan->nik_karyawan = $nik;
            }
            $karyawan->save();

            // 5. Buat kredensial login baru
            $loginUser = EmployeeLogin::create([
                'karyawan_id' => $karyawan->id,
                'no_sap'      => $noSap,
                'password'    => Hash::make($data['password']),
                'fcm_token'   => $data['fcm_token'] ?? null,
            ]);

            // 6. Terbitkan Sanctum token untuk langsung masuk
            $token = $loginUser->createToken('api-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Aktivasi akun berhasil! Selamat datang di STMC Health.',
                'token' => $token,
                'user_profile' => $this->getProfileData($loginUser)
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error("Register Karyawan Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses aktivasi akun. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    /**
     * PENDAFTARAN MANDIRI PESERTA UMUM (Pasien Luar / Keluarga)
     * POST /api/register/umum
     */
    public function registerUmum(Request $request)
    {
        try {
            $data = $request->validate([
                'nik'                   => 'required|string|size:16',
                'nama_lengkap'          => 'required|string|max:255',
                'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir'         => 'required|date',
                'tempat_lahir'          => 'nullable|string|max:100',
                'no_hp'                 => 'required|string|max:20',
                'email'                 => 'nullable|email|max:255',
                'alamat'                => 'nullable|string',
                'perusahaan_asal'       => 'nullable|string|max:150',
                'password'              => 'required|string|min:6|confirmed',
                'fcm_token'             => 'nullable|string',
            ], [
                'nik.required'          => 'Nomor NIK KTP (16 digit) wajib diisi',
                'nik.size'              => 'NIK KTP harus 16 digit angka',
                'nama_lengkap.required' => 'Nama lengkap sesuai KTP wajib diisi',
                'jenis_kelamin.required'=> 'Pilih jenis kelamin',
                'tanggal_lahir.required'=> 'Tanggal lahir wajib diisi',
                'no_hp.required'        => 'Nomor HP / WhatsApp wajib diisi',
                'password.required'     => 'Kata sandi wajib diisi',
                'password.min'          => 'Kata sandi minimal 6 karakter',
                'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok',
            ]);

            $nik = trim($data['nik']);

            // 1. Cek apakah NIK sudah punya akun login
            $existingLogin = PesertaMcuLogin::where('nik_pasien', $nik)->first();
            if ($existingLogin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'NIK ini sudah terdaftar sebagai Peserta Umum. Silakan langsung login di aplikasi.'
                ], 422);
            }

            // 2. Hitung umur dari tanggal lahir
            $tglLahir = \Carbon\Carbon::parse($data['tanggal_lahir']);
            $umur = $tglLahir->age;

            // 3. Buat atau update profil PesertaMcu
            $pasien = PesertaMcu::updateOrCreate(
                ['nik_pasien' => $nik],
                [
                    'nama_lengkap'      => trim($data['nama_lengkap']),
                    'tipe_anggota'      => 'Non-Karyawan',
                    'jenis_kelamin'     => $data['jenis_kelamin'],
                    'tanggal_lahir'     => $data['tanggal_lahir'],
                    'tempat_lahir'      => $data['tempat_lahir'] ?? null,
                    'umur'              => $umur,
                    'no_hp'             => $data['no_hp'],
                    'email'             => $data['email'] ?? null,
                    'alamat'            => $data['alamat'] ?? null,
                    'perusahaan_asal'   => $data['perusahaan_asal'] ?? 'Umum / Mandiri',
                ]
            );

            // 4. Buat akun login peserta umum
            $loginUser = PesertaMcuLogin::create([
                'nik_pasien'        => $nik,
                'peserta_mcu_id'    => $pasien->id,
                'password'          => Hash::make($data['password']),
                'fcm_token'         => $data['fcm_token'] ?? null,
            ]);

            // 5. Terbitkan Sanctum token untuk langsung masuk
            $token = $loginUser->createToken('api-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Pendaftaran Peserta Umum berhasil! Selamat datang di STMC Health.',
                'token' => $token,
                'user_profile' => $this->getProfileData($loginUser)
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error("Register Umum Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pendaftaran. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    /**
     * API untuk update token jika login sukses tapi token belum siap
     * POST /api/update-token
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $user = $request->user('sanctum');
        
        Log::info("DEBUG Auth: Mencoba update FCM token untuk user ID: " . $user->id);
        
        // 🌟 PERBAIKAN: Langsung simpan ke model user login saat ini tanpa lewat jembatan profil
        if ($user instanceof EmployeeLogin || $user instanceof PesertaMcuLogin) {
            $user->update(['fcm_token' => $request->fcm_token]);
            return response()->json(['status' => 'success']);
        }
        
        return response()->json(['status' => 'error'], 400);
    }

    /**
     * LOGOUT API
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $user = $request->user('sanctum');

        if ($user) {
            try {
                // 🌟 PERBAIKAN: Langsung bersihkan kolom fcm_token pada tabel login miliknya
                if ($user instanceof EmployeeLogin || $user instanceof PesertaMcuLogin) {
                    $user->update(['fcm_token' => null]);
                    Log::info("Logout: Token untuk Login ID {$user->id} telah dibersihkan.");
                }
            } catch (\Exception $e) {
                Log::error("Error saat menghapus FCM Token saat logout: " . $e->getMessage());
            }

            // 🔑 Hapus session login (Sanctum Token)
            if ($user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout dan token dibersihkan'
        ]);
    }

    /**
     * CHANGE PASSWORD API
     * POST /api/change-password
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $user = $request->user('sanctum');

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password lama salah'
                ], 401);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diubah'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // ===============================
    // HELPER PROFILE DATA (MENGGUNAKAN PUBLIC DISK DENGAN URL ABSOLUT)
    // ===============================

    // ===============================
    // HELPER PROFILE DATA 
    // ===============================

    protected function findAndAuthenticateApiUser(string $input, string $password)
    {
        $loginUser = null;
        $input = trim($input); // Bersihkan spasi kosong

        // 1. CARI DI KARYAWAN (Tembus langsung ke tabel Profil Karyawan)
        // Kita cari kecocokan Email, SAP, atau NIK di tabel 'karyawans'
        $loginUser = EmployeeLogin::whereHas('karyawan', function ($q) use ($input) {
            $q->where('email', $input)
              ->orWhere('no_sap', $input)
              ->orWhere('nik_karyawan', $input);
        })->first();

        // Fallback: Jika tidak ketemu via relasi, cari di tabel Login Karyawan (khusus SAP)
        if (!$loginUser) {
            try {
                $loginUser = EmployeeLogin::where('no_sap', $input)->first();
            } catch (\Throwable $th) {}
        }

        // 2. CARI DI PASIEN UMUM (Tembus langsung ke tabel Profil Pasien)
        // Kita cari kecocokan Email atau NIK di tabel 'peserta_mcus'
        if (!$loginUser) {
            $loginUser = PesertaMcuLogin::whereHas('pasien', function ($q) use ($input) {
                $q->where('email', $input)
                  ->orWhere('nik_pasien', $input);
            })->first();

            // Fallback: Cari di tabel Login Pasien (khusus NIK)
            if (!$loginUser) {
                try {
                    $loginUser = PesertaMcuLogin::where('nik_pasien', $input)->first();
                } catch (\Throwable $th) {}
            }
        }

        // 3. COCOKKAN PASSWORD
        if ($loginUser && Hash::check($password, $loginUser->password)) {
            return $loginUser;
        }

        return null;
    }

    protected function getProfileData($loginUser)
    {
        // ===== KARYAWAN =====
        if ($loginUser instanceof EmployeeLogin) {
            $karyawan = $loginUser->karyawan()
                ->with(['departemen', 'unitKerja', 'provinsi'])
                ->first();

            return $karyawan ? [
                'type' => 'Karyawan',
                'id' => $karyawan->id,
                'nama' => $karyawan->nama_karyawan,
                'no_sap' => $karyawan->no_sap,
                'nik' => $karyawan->nik_karyawan,
                'departemen' => $karyawan->departemen->nama_departemen ?? null,
                'unit_kerja' => $karyawan->unitKerja->nama_unit_kerja ?? null,
                'email' => $karyawan->email,
                'no_hp' => $karyawan->no_hp,
                'foto_path' => $karyawan->foto_profil,
                // 🌟 FIX: Menghasilkan link absolut https://stmc-health.my.id/storage/...
                'foto' => !empty($karyawan->foto_profil)
                    ? asset('storage/' . $karyawan->foto_profil)
                    : null,
                'jabatan' => $karyawan->jabatan,
                'tanggal_lahir' => $karyawan->tanggal_lahir,
                'umur' => $karyawan->umur,
                'jenis_kelamin' => $karyawan->jenis_kelamin,
                'agama' => $karyawan->agama,
                'alamat' => $karyawan->alamat,
                'provinsi' => $karyawan->provinsi->nama_provinsi ?? null,
                'kabupaten' => $karyawan->nama_kabupaten,
                'kecamatan' => $karyawan->nama_kecamatan,
                'tinggi_badan' => $karyawan->tinggi_badan,
                'berat_badan' => $karyawan->berat_badan,
                'golongan_darah' => $karyawan->golongan_darah,
                'is_employee' => true
            ] : null ;
        }

        // ===== PESERTA MCU =====
        if ($loginUser instanceof PesertaMcuLogin) {
            $pasien = $loginUser->pasien;
            if (!$pasien) {
                return [
                    'type' => 'Pasien',
                    'message' => 'Data profil pasien tidak ditemukan di database',
                    'is_employee' => false
                ];
            }

            return [
                'type' => 'Pasien',
                'id' => $pasien->id,
                'nama' => $pasien->nama_lengkap,
                'nik' => $pasien->nik_pasien,
                'email' => $pasien->email,
                'no_hp' => $pasien->no_hp,
                // 🌟 FIX: Menghasilkan link absolut https://stmc-health.my.id/storage/...
                'foto' => !empty($pasien->foto_profil)
                    ? asset('storage/' . $pasien->foto_profil)
                    : null,
                'tanggal_lahir' => $pasien->tanggal_lahir,
                'umur' => $pasien->umur,
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'agama' => $pasien->agama,
                'alamat' => $pasien->alamat,
                'provinsi' => $pasien->provinsi->nama_provinsi ?? null,
                'kabupaten' => $pasien->nama_kabupaten,
                'kecamatan' => $pasien->nama_kecamatan,
                'tinggi_badan' => $pasien->tinggi_badan,
                'berat_badan' => $pasien->berat_badan,
                'golongan_darah' => $pasien->golongan_darah,
                'pendidikan' => $pasien->pendidikan,
                'pekerjaan' => $pasien->pekerjaan,
                'perusahaan' => $pasien->perusahaan_asal,
                'is_employee' => false
            ];
        }

        return null;
    }

    /**
     * UPDATE PROFILE INTERFACES
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user('sanctum');

            if ($user instanceof EmployeeLogin) {
                $profile = $user->karyawan;
            } elseif ($user instanceof PesertaMcuLogin) {
                $profile = $user->pasien;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak dikenali'
                ], 403);
            }

            $request->validate([
                'nik'           => 'nullable|string',
                'nama'          => 'nullable|string',
                'email'         => 'nullable|email',
                'no_hp'         => 'nullable|string',
                'tinggi_badan'  => 'nullable|numeric',
                'berat_badan'   => 'nullable|numeric',
                'alamat'        => 'nullable|string',
                'provinsi'      => 'nullable|string',
                'kabupaten'     => 'nullable|string',
                'kecamatan'     => 'nullable|string',
                'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png,webp,heic|max:5120',
            ]);

            if ($request->hasFile('foto_profil')) {
                $file = $request->file('foto_profil');
                
                if ($file->isValid()) {
                    // Hapus foto lama di penyimpanan lokal public jika ada
                    if (!empty($profile->foto_profil)) {
                        try {
                            Storage::disk('public')->delete($profile->foto_profil);
                        } catch (\Exception $e) {}
                    }

                    // 🌟 SIMPAN KE DISK PUBLIC HOSTING
                    $path = $file->store('profile_photos', 'public');
                    $profile->foto_profil = $path;
                }
            }

            $provinsiId = null; // Default null jika tidak ada
            if (!empty($request->provinsi)) {
                // Cari data provinsi di tabel master berdasarkan namanya
                $provinsiData = \App\Models\Provinsi::where('nama_provinsi', $request->provinsi)->first();
                
                // Jika ketemu, ambil ID-nya
                if ($provinsiData) {
                    $provinsiId = $provinsiData->id;
                }
            }

            $updateData = [
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'tinggi_badan' => $request->tinggi_badan,
                'berat_badan' => $request->berat_badan,
                'provinsi_id' => $provinsiId,
                'nama_kabupaten' => $request->kabupaten,
                'nama_kecamatan' => $request->kecamatan,
            ];

            if ($user instanceof EmployeeLogin) {
                $updateData['nik_karyawan'] = $request->nik;
                $updateData['nama_karyawan'] = $request->nama;
            } else {
                $updateData['nik_pasien'] = $request->nik;
                $updateData['nama_lengkap'] = $request->nama;
            }

            $profile->fill(array_filter(
                $updateData,
                fn($value) => !is_null($value)
            ));

            $profile->save();
            $user->refresh();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui di server hosting',
                'user_profile' => $this->getProfileData($user)
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }
}