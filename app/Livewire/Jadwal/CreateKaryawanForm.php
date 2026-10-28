<?php

namespace App\Livewire\Jadwal;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Illuminate\Support\Collection;
use App\Models\JadwalMcu;
use App\Models\Dokter;
use App\Models\PaketMcu;
use App\Models\JadwalPoli;
use App\Models\JadwalDokter; // Memanggil model JadwalDokter untuk pengecekan hari praktik
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CreateKaryawanForm extends Component
{
    public $jadwalId = null;
    public $isEditMode = false;

    public $search = '';
    public $karyawan_id = null;
    public $peserta_mcus_id = null;
    public $tanggal_mcu;
    public $results = [];
    public $selectedKaryawan;
    public $patientType = null;
    public $paket_mcus_id = null;
    public $dokter_id = null;
    public $daftarDokter = [];
    public $daftarPaket = [];
    public $finalNoSap = null;
    public $finalNamaPasien = null;
    public $finalNikPasien = null;
    public $finalPerusahaanAsal = null;

    protected $rules = [
        'tanggal_mcu' => 'required|date',
        'karyawan_id' => 'required_without:peserta_mcus_id|nullable|exists:karyawans,id',
        'peserta_mcus_id' => 'required_without:karyawan_id|nullable|exists:peserta_mcus,id',
        'dokter_id' => 'required|exists:dokters,id',
        'paket_mcus_id' => 'required|exists:paket_mcus,id',
    ];

    public function mount($jadwalId = null)
    {
        $this->results = new Collection();
        $this->daftarDokter = Dokter::all();
        $this->daftarPaket = PaketMcu::all();
        
        if ($jadwalId) {
            $jadwal = JadwalMcu::with(['karyawan', 'pesertaMcu'])->findOrFail($jadwalId);
            
            $this->jadwalId = $jadwalId;
            $this->isEditMode = true;

            $this->tanggal_mcu = $jadwal->tanggal_mcu;
            $this->dokter_id = $jadwal->dokter_id;
            $this->paket_mcus_id = $jadwal->paket_mcus_id;

            if ($jadwal->karyawan_id) {
                $this->patientType = 'karyawan';
                $this->karyawan_id = $jadwal->karyawan_id;
                $this->selectedKaryawan = $jadwal->karyawan;
                $this->search = $jadwal->karyawan->nama_karyawan;
            } elseif ($jadwal->peserta_mcus_id) {
                $this->patientType = 'peserta_mcu';
                $this->peserta_mcus_id = $jadwal->peserta_mcus_id;
                $this->selectedKaryawan = $jadwal->pesertaMcu;
                $this->search = $jadwal->pesertaMcu->nama_lengkap;
            }
            
            $this->finalNoSap = $jadwal->no_sap;
            $this->finalNamaPasien = $jadwal->nama_pasien;
            $this->finalNikPasien = $jadwal->nik_pasien;
            $this->finalPerusahaanAsal = $jadwal->perusahaan_asal;
        } else {
            $this->tanggal_mcu = now()->toDateString();
            // {{-- Memicu pengecekan dokter otomatis untuk tanggal hari ini saat halaman pertama kali dibuka --}}
            $this->autoSetDokter(); 
        }
    }

    // {{-- 
    //   AUTOMATIC DETECT HOOK: 
    //   Fungsi ini otomatis berjalan setiap kali variabel $tanggal_mcu berubah di sisi klien.
    //   Metode ini memanfaatkan fitur Lifecycle Hooks dari Livewire (updatedProperty).
    // --}}
    public function updatedTanggalMcu($value)
    {
        if ($value) {
            $this->autoSetDokter();
        }
    }

    // {{-- Logika utama untuk mencari dokter berdasarkan tanggal praktik di database --}}
    public function autoSetDokter()
    {
        // // {{-- Mencari baris jadwal dokter yang memiliki kecocokan tanggal --}}
        $jadwalDokter = JadwalDokter::whereDate('tanggal', $this->tanggal_mcu)->first();

        if ($jadwalDokter) {
            // // {{-- Jika ditemukan dokter yang bertugas, kunci ID-nya ke variabel $dokter_id untuk mengisi dropdown otomatis --}}
            $this->dokter_id = $jadwalDokter->dokter_id;
        } else {
            // // {{-- Jika tidak ada dokter yang terjadwal pada tanggal tersebut, kosongkan dropdown agar diisi manual oleh admin --}}
            $this->dokter_id = null;
        }
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->results = [];
            return;
        }

        $searchTerm = $this->search;
        $karyawanResults = Karyawan::where('nama_karyawan', 'like', '%' . $searchTerm . '%')
                                     ->orWhere('nik_karyawan', 'like', '%' . $searchTerm . '%')
                                     ->orWhere('no_sap', 'like', '%' . $searchTerm . '%')
                                     ->limit(10)->get();
        $pesertaMcuResults = PesertaMcu::where('nama_lengkap', 'like', '%' . $searchTerm . '%')
                                           ->orWhere('nik_pasien', 'like', '%' . $searchTerm . '%')
                                           ->orWhere('no_sap', 'like', '%' . $searchTerm . '%')
                                           ->limit(10)->get();
        $mappedKaryawan = $karyawanResults->map(function ($item) {
            return [
                'id' => $item->id,
                'search_name' => $item->nama_karyawan,
                'search_nik' => $item->nik_karyawan,
                'no_sap' => $item->no_sap,
                'search_type' => 'karyawan',
            ];
        })->toArray();
        $mappedPesertaMcu = $pesertaMcuResults->map(function ($item) {
            return [
                'id' => $item->id,
                'search_name' => $item->nama_lengkap,
                'search_nik' => $item->nik_pasien,
                'no_sap' => $item->no_sap,
                'search_type' => 'peserta_mcu',
            ];
        })->toArray();
        $this->results = array_merge($mappedKaryawan, $mappedPesertaMcu);
    }

    public function selectPatient($id, $type)
    {
        $this->reset(['karyawan_id', 'peserta_mcus_id', 'selectedKaryawan', 'patientType']);
        $this->patientType = $type;

        if ($type === 'karyawan') {
            $this->karyawan_id = $id;
            $this->selectedKaryawan = Karyawan::find($id);
            if ($this->selectedKaryawan) {
                $this->search = $this->selectedKaryawan->nama_karyawan;
                $this->finalNoSap = $this->selectedKaryawan->no_sap ?? null;
                $this->finalNamaPasien = $this->selectedKaryawan->nama_karyawan ?? null;
                $this->finalNikPasien = $this->selectedKaryawan->nik_karyawan ?? null;
                $this->finalPerusahaanAsal = 'PT. Semen Tonasa';
            }
        } elseif ($type === 'peserta_mcu') {
            $this->peserta_mcus_id = $id;
            $this->selectedKaryawan = PesertaMcu::find($id);
            if ($this->selectedKaryawan) {
                $this->search = $this->selectedKaryawan->nama_lengkap;
                $this->finalNoSap = $this->selectedKaryawan->no_sap ?? null;
                $this->finalNamaPasien = $this->selectedKaryawan->nama_lengkap ?? null;
                $this->finalNikPasien = $this->selectedKaryawan->nik_pasien ?? null;
                $this->finalPerusahaanAsal = $this->selectedKaryawan->perusahaan_asal ?? null;
            }
        }
        $this->results = [];
    }

    public function save()
    {
        $this->validate();

        if (!$this->isEditMode) {
            $tanggal_mcu_cek = Carbon::parse($this->tanggal_mcu)->toDateString();
            $kuotaTerisi = JadwalMcu::whereDate('tanggal_mcu', $tanggal_mcu_cek)->where('status', '!=', 'Canceled')->count();

            if ($kuotaTerisi >= 30) {
                $this->dispatch('jadwal-created', [
                    'type' => 'error',
                    'message' => 'Mohon maaf, kuota jadwal untuk tanggal ' . Carbon::parse($tanggal_mcu_cek)->format('d-m-Y') . ' sudah penuh (30 orang).'
                ]);
                return;
            }
        }

        $finalKaryawanId = $this->karyawan_id;
        $finalPesertaMcuId = $this->peserta_mcus_id;
        $finalNoSap = $this->finalNoSap;
        $finalNamaPasien = $this->finalNamaPasien;
        $finalNikPasien = $this->finalNikPasien;
        $finalPerusahaanAsal = $this->finalPerusahaanAsal;

        try {
            DB::beginTransaction();
            
            if ($this->isEditMode) {
                $jadwal = JadwalMcu::findOrFail($this->jadwalId);
                $jadwal->update([
                    'karyawan_id' => $finalKaryawanId,
                    'peserta_mcus_id' => $finalPesertaMcuId,
                    'dokter_id' => $this->dokter_id,
                    'tanggal_mcu' => $this->tanggal_mcu,
                    'paket_mcus_id' => $this->paket_mcus_id,
                    'no_sap' => $finalNoSap,
                    'nama_pasien' => $finalNamaPasien,
                    'nik_pasien' => $finalNikPasien,
                    'perusahaan_asal' => $finalPerusahaanAsal,
                ]);

                $this->dispatch('jadwal-created', [
                    'type' => 'success',
                    'message' => 'Jadwal MCU berhasil diperbarui!',
                ]);
            } else {
                $lastAntrean = JadwalMcu::where('tanggal_mcu', $this->tanggal_mcu)->where('dokter_id', $this->dokter_id)->select(DB::raw('MAX(CAST(SUBSTR(no_antrean, 2) AS UNSIGNED)) as max_number'))->first();
                $lastNumber = $lastAntrean ? $lastAntrean->max_number : 0;
                $newNumber = $lastNumber + 1;
                $newAntrean = 'C' . sprintf('%03d', $newNumber);
                $uuid = Str::uuid()->toString();

                $jadwal = JadwalMcu::create([
                    'karyawan_id' => $finalKaryawanId,
                    'peserta_mcus_id' => $finalPesertaMcuId,
                    'dokter_id' => $this->dokter_id,
                    'tanggal_mcu' => $this->tanggal_mcu,
                    'tanggal_pendaftaran' => now(),
                    'no_antrean' => $newAntrean,
                    'no_sap' => $finalNoSap,
                    'nama_pasien' => $finalNamaPasien,
                    'nik_pasien' => $finalNikPasien,
                    'perusahaan_asal' => $finalPerusahaanAsal,
                    'status' => 'Scheduled',
                    'qr_code_id' => $uuid,
                    'paket_mcus_id' => $this->paket_mcus_id,
                ]);

                $paket = PaketMcu::with('poli')->find($this->paket_mcus_id);
                if ($paket) {
                    foreach ($paket->poli as $poli) {
                        JadwalPoli::create([
                            'jadwal_mcus_id' => $jadwal->id,
                            'poli_id' => $poli->id,
                            'status' => 'Pending',
                        ]);
                    }
                }
                
                $this->dispatch('jadwal-created', [
                    'type' => 'success',
                    'message' => 'Jadwal MCU berhasil ditambahkan!',
                ]);
                $this->resetForm();
            }

            DB::commit();
            return redirect()->route('jadwal.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('jadwal-created', [
                'type' => 'error',
                'message' => 'Gagal memproses jadwal: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'search', 'karyawan_id', 'peserta_mcus_id', 'tanggal_mcu', 
            'dokter_id', 'paket_mcus_id', 'results', 'selectedKaryawan', 
            'patientType', 'finalNoSap', 'finalNamaPasien', 'finalNikPasien', 
            'finalPerusahaanAsal'
        ]);
    }

    public function render()
    {
        return view('livewire.jadwal.create-karyawan-form');
    }
}