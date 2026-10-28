<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalMcu; 
use App\Models\Karyawan; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class JadwalMcuController extends Controller
{
    /**
     * Menampilkan daftar jadwal MCU.
     */
    public function index(Request $request)
    {
        // 1. Ambil input filter
        $status = $request->input('status');
        $search_sap = $request->input('search_sap');
        
        // 🌟 TAMBAHAN: Tangkap input per_page, default ke 10
        $perPage = $request->input('per_page', 10);

        // LOGIKA OTOMATIS: Jika tidak ada filter tanggal DAN tidak sedang mencari nama/SAP, default ke HARI INI
        // Tapi jika sedang mencari nama/SAP, kita kosongkan tanggal agar bisa mencari di seluruh riwayat
        if ($request->filled('search_sap')) {
            $tanggal_filter = $request->input('tanggal_filter'); // biarkan opsional
        } else {
            $tanggal_filter = $request->input('tanggal_filter', \Carbon\Carbon::today()->toDateString());
        }

        $query = JadwalMcu::with('dokter', 'paketMcu', 'karyawan'); // Pastikan relasi karyawan dimuat

        // Apply date filter (Hanya jalan jika variabel ada isinya)
        if (!empty($tanggal_filter)) {
            $query->whereDate('tanggal_mcu', $tanggal_filter);
        }

        // Apply status filter
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // 2. Apply search filter untuk SAP
        if (!empty($search_sap)) {
            $query->where(function ($q) use ($search_sap) {
                // 1. Cari berdasarkan SAP (di tabel jadwal_mcus)
                $q->where('no_sap', 'like', '%' . $search_sap . '%')
                  
                  // 2. ATAU cari di kolom nama_pasien (jika diisi manual)
                  ->orWhere('nama_pasien', 'like', '%' . $search_sap . '%')
                  
                  // 3. ATAU cari di tabel relasi Karyawan (kolom nama_karyawan)
                  ->orWhereHas('karyawan', function ($subQuery) use ($search_sap) {
                      $subQuery->where('nama_karyawan', 'like', '%' . $search_sap . '%');
                  })
                  
                  // 4. ATAU cari di tabel relasi Peserta Umum/Keluarga (kolom nama_lengkap)
                  ->orWhereHas('pesertaMcu', function ($subQuery) use ($search_sap) {
                      $subQuery->where('nama_lengkap', 'like', '%' . $search_sap . '%');
                  });
            });
        }
        
        // Gunakan variabel $perPage di paginate()
        $jadwals = $query->orderBy('no_antrean', 'asc')->paginate($perPage);

        // 3. QUERY UNTUK CARD ANTREAN POLI (HARI INI)
        $polis = \App\Models\Poli::with(['jadwalPoli' => function ($query) {
            $query->where('status', 'Waiting')
                  ->whereHas('jadwalMcu', function ($qJadwal) {
                      $qJadwal->where('status', 'Present')
                              ->whereDate('tanggal_mcu', \Carbon\Carbon::today());
                  })
                  ->with(['jadwalMcu.karyawan', 'jadwalMcu.pesertaMcu'])
                  ->orderBy('created_at', 'asc'); 
        }])->get();

        // MENGHITUNG KUOTA HARI INI
        $hariIni = \Carbon\Carbon::today();
        $kuotaTerisi = JadwalMcu::whereDate('tanggal_mcu', $hariIni)
                                ->where('status', '!=', 'Canceled')
                                ->count();
        $sisaKuota = 30 - $kuotaTerisi;
        // Pastikan sisa kuota tidak minus jika ada error data
        $sisaKuota = $sisaKuota < 0 ? 0 : $sisaKuota; // Cegah angka minus

        // 4. Kirimkan $search_sap ke view agar nilai tetap ada di input setelah cari
        return view('jadwal.index', [
            'jadwals' => $jadwals,
            'tanggal_filter' => $tanggal_filter, 
            'status' => $status,
            'search_sap' => $search_sap,
            'perPage' => $perPage,
            'polis' => $polis,
            'kuotaTerisi' => $kuotaTerisi,
            'sisaKuota' => $sisaKuota,
        ]);
    }

    /**
     * Menampilkan formulir untuk membuat jadwal baru.
     */
    public function create()
    {
        return view('jadwal.create');
    }

    /**
     * Menyimpan jadwal MCU baru ke database.
     */
    public function store(Request $request)
    {
        //1.TAMBAHKAN LOGIKA PENGECEKAN KUOTA DI SINI
        $tanggal_mcu_cek = \Carbon\Carbon::parse($request->tanggal_mcu)->toDateString();

        $kuotaTerisi = JadwalMcu::whereDate('tanggal_mcu', $tanggal_mcu_cek)
                                ->where('status', '!=', 'Canceled') // Abaikan yang dibatalkan
                                ->count();

        if ($kuotaTerisi >= 30) {
            return back()
                ->with('error', 'Mohon maaf, kuota Medical Check Up untuk tanggal ' . \Carbon\Carbon::parse($tanggal_mcu_cek)->format('d-m-Y') . ' sudah penuh (30 orang). Silakan pilih hari lain.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Logika nomor antrean otomatis
            $tanggal_mcu = \Carbon\Carbon::parse($request->tanggal_mcu);
            $prefix = $tanggal_mcu->format('Ymd');
            $lastQueue = JadwalMcu::where('tanggal_mcu', $tanggal_mcu)
                                 ->orderBy('no_antrean', 'desc')
                                 ->first();
            
            $nextQueueNumber = 1;
            if ($lastQueue) {
                $lastNumber = intval(substr($lastQueue->no_antrean, -3));
                $nextQueueNumber = $lastNumber + 1;
            }
            
            $antrean = $prefix . '_' . str_pad($nextQueueNumber, 3, '0', STR_PAD_LEFT);

            if ($request->tipe_pasien === 'ptst') {
                $validatedData = $request->validate([
                    'karyawan_id' => 'required|exists:karyawans,id',
                    'tanggal_mcu' => 'required|date',
                ]);

                $karyawan = Karyawan::findOrFail($validatedData['karyawan_id']);

                JadwalMcu::create([
                    'tipe_pasien' => 'ptst',
                    'karyawan_id' => $karyawan->id,
                    'no_sap' => $karyawan->no_sap,
                    'tanggal_mcu' => $validatedData['tanggal_mcu'],
                    'tanggal_pendaftaran' => now(),
                    'no_antrean' => $antrean, // Simpan nomor antrean
                    'status' => 'Scheduled',
                ]);

            } elseif ($request->tipe_pasien === 'non-ptst') {
                $validatedData = $request->validate([
                    'nama_pasien' => 'required|string|max:255',
                    'no_identitas' => 'required|string',
                    'perusahaan_afiliasi' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'tanggal_mcu' => 'required|date',
                ]);

                JadwalMcu::create([
                    'tipe_pasien' => 'non-ptst',
                    'nama_pasien' => $validatedData['nama_pasien'],
                    'no_identitas' => $validatedData['no_identitas'],
                    'perusahaan_afiliasi' => $validatedData['perusahaan_afiliasi'],
                    'tanggal_lahir' => $validatedData['tanggal_lahir'],
                    'tanggal_mcu' => $validatedData['tanggal_mcu'],
                    'tanggal_pendaftaran' => now(),
                    'no_antrean' => $antrean, // Simpan nomor antrean
                    'status' => 'Scheduled',
                ]);

            } else {
                return back()->withErrors(['tipe_pasien' => 'Tipe pasien tidak valid.'])->withInput();
            }

            DB::commit();
            return redirect()->route('jadwal.index')->with('success', 'Medical Check up schedule successfully added!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while saving the schedule: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Memperbarui status jadwal.
     */
    public function updateStatus(Request $request, JadwalMcu $jadwal)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:Present,Scheduled,Finished,Canceled',
        ]);
        
        $jadwal->update(['status' => $validatedData['status']]);
        
        return back()->with('success', 'Schedule status successfully updated!');
    }

    public function destroy(JadwalMcu $jadwal)
    {
        try {
            $jadwal->delete();
            return back()->with('success', 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
    
    public function edit(JadwalMcu $jadwal)
    {
        // Untuk form edit, kita arahkan ke view yang sama dengan create, 
        // namun dengan membawa data $jadwal untuk di-bind ke Livewire atau di-isi di form.
        // Asumsikan Livewire component yang menangani form dapat menerima $jadwal sebagai parameter.
        return view('jadwal.edit', compact('jadwal'));
    }

    /**
     * Memperbarui jadwal MCU di database.
     */
    public function update(Request $request, JadwalMcu $jadwal)
    {
        DB::beginTransaction();

        try {
            // Logika validasi data
            // Gunakan validasi yang sama seperti di store, disesuaikan untuk update
            $validatedData = [];
            $updateData = [
                'tanggal_mcu' => $request->tanggal_mcu,
                'dokter_id' => $request->dokter_id,
                'paket_mcus_id' => $request->paket_mcus_id,
            ];
            
            // Tambahkan validasi untuk form saat ini (Livewire)
            $validatedData = $request->validate([
                // Pastikan pasien sudah terpilih
                'karyawan_id' => 'nullable|exists:karyawans,id', // Di Livewire ini akan diisi jika tipe ptst
                'peserta_mcus_id' => 'nullable|exists:peserta_mcus,id', // Di Livewire ini akan diisi jika tipe non-ptst
                'tanggal_mcu' => 'required|date',
                'dokter_id' => 'required|exists:dokters,id',
                'paket_mcus_id' => 'required|exists:paket_mcus,id',
            ]);
            
            // Update data dasar jadwal
            $jadwal->update([
                'tanggal_mcu' => $validatedData['tanggal_mcu'],
                'dokter_id' => $validatedData['dokter_id'],
                'paket_mcus_id' => $validatedData['paket_mcus_id'],
                // Pastikan karyawan_id atau peserta_mcus_id juga di update jika ada perubahan pasien di form edit
                'karyawan_id' => $validatedData['karyawan_id'] ?? null,
                'peserta_mcus_id' => $validatedData['peserta_mcus_id'] ?? null,
                // Kolom lain (no_antrean, status) biasanya tidak diupdate di sini
            ]);


            DB::commit();
            return redirect()->route('jadwal.index')->with('success', 'Jadwal Medical Check up berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui jadwal: ' . $e->getMessage())->withInput();
        }
    }

}
