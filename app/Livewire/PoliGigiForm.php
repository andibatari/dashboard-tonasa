<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalPoli; 
use App\Models\Karyawan; 
use App\Models\PesertaMcu; 
use App\Models\PoliGigiResult; 
use App\Models\UnitKerja; 
use App\Models\Dokter; 
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Log; 

class PoliGigiForm extends Component
{
    public $jadwalId; 
    public $patient; 
    public $poliData; 
    public $poliGigiResult; 
    public $dokterId; 
    public $listDokter; 
    public $isKaryawan = false; 
    public $instansiPasien; 
    
    public $dataForm;
    public $gigiKlinis = []; 
    public $kesimpulan; 
    public $keterangan; 
    
    // 🌟 PERBAIKAN: Menyesuaikan aturan (rules) dengan struktur form STMC yang asli
    protected $rules = [
        'dataForm.ekstraOral.kelenjar_submandibular' => 'nullable',
        'dataForm.ekstraOral.kelenjar_leher' => 'nullable',
        'dataForm.intraOral.mukosa_pipi' => 'required',
        'dataForm.intraOral.palatum' => 'required',
        'dataForm.intraOral.mukosa_mulut' => 'required',
        'dataForm.intraOral.lidah' => 'required',
        'dataForm.intraOral.ginggiva_ra' => 'required',
        'dataForm.intraOral.ginggiva_rb' => 'required',
        'dataForm.intraOral.karang_gigi' => 'nullable', // Boleh string kosong
        'dataForm.intraOral.pocket' => 'required',
        'keterangan' => 'nullable|string',
        'kesimpulan' => 'nullable|string',
        'gigiKlinis' => 'nullable|array',
        'dokterId' => 'required|exists:dokters,id', 
    ]; 
    
    public function mount($jadwalId, $poliData)
    {
        $this->jadwalId = $jadwalId;
        $this->poliData = $poliData; 
        
        try {
            $this->listDokter = Dokter::all();
        } catch (\Exception $e) {
            $this->listDokter = collect([]); 
        }

        $jadwalMcu = $poliData->jadwalMcu ?? null;
        $karyawanId = $jadwalMcu->karyawan_id ?? null;
        $pesertaMcuId = $jadwalMcu->peserta_mcus_id ?? null;

        if ($karyawanId) {
            $this->patient = Karyawan::with('unitKerja')->find($karyawanId); 
            $this->isKaryawan = true;
        } elseif ($pesertaMcuId) {
            $this->patient = PesertaMcu::find($pesertaMcuId);
            $this->isKaryawan = false;
        } else {
            $this->patient = null;
            $this->isKaryawan = false;
        } 
        
        if ($this->patient) {
            if ($this->isKaryawan) {
                $unitKerja = $this->patient->unitKerja->nama_unit_kerja ?? 'Unit Kerja Tidak Diketahui';
                $this->instansiPasien = "PT SEMEN TONASA (Unit: {$unitKerja})";
                $this->patient->nama_lengkap = $this->patient->nama_karyawan ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_karyawan ?? 'N/A';
                $this->patient->no_sap = $this->patient->id ?? 'N/A';
                $this->patient->nomor_hp = $this->patient->nomor_hp ?? 'N/A';
            } else {
                $perusahaanAsal = $this->patient->perusahaan_asal ?? $jadwalMcu->perusahaan_asal ?? null; 
                $this->instansiPasien = $perusahaanAsal ? $perusahaanAsal : 'NON-KARYAWAN/UMUM';
                $this->patient->nama_lengkap = $this->patient->nama_lengkap ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_pasien ?? 'N/A';
                $this->patient->no_sap = 'N/A'; 
                $this->patient->nomor_hp = $this->patient->nomor_hp ?? 'N/A';
            }
            $this->patient->alamat = $this->patient->alamat ?? 'N/A';
            $this->patient->jenis_kelamin = $this->patient->jenis_kelamin ?? 'N/A';
            $this->patient->tanggal_lahir = $this->patient->tanggal_lahir ?? now()->toDateString(); 
        } else {
            $this->patient = (object)[
                'no_sap' => 'N/A', 
                'nama_lengkap' => $jadwalMcu->nama_pasien ?? 'Pasien Tidak Ditemukan', 
                'nik_pasien' => $jadwalMcu->nik_pasien ?? 'N/A', 
                'tanggal_lahir' => now()->toDateString(),
                'alamat' => 'N/A',
                'nomor_hp' => 'N/A',
                'jenis_kelamin' => 'N/A',
            ];
            $this->instansiPasien = 'N/A';
        }

        $this->poliGigiResult = PoliGigiResult::firstOrNew(['jadwal_poli_id' => $this->poliData->id]);

        if ($this->poliGigiResult->exists) {
            $data = $this->poliGigiResult->data_pemeriksaan; 
            $this->dataForm = $data['dataForm'] ?? [];
            $this->gigiKlinis = $data['gigiKlinis'] ?? [];
            $this->kesimpulan = $this->poliGigiResult->kesimpulan;
            $this->keterangan = $this->poliGigiResult->keterangan;
            $this->dokterId = $this->poliGigiResult->dokter_id; 
        } else {
            // 🌟 PERBAIKAN: Default form value sesuai standar form gigi STMC
            $this->dataForm = [
                'ekstraOral' => [
                    'kelenjar_submandibular' => 'NORMAL', 
                    'kelenjar_leher' => 'NORMAL'
                ],
                'intraOral' => [
                    'mukosa_pipi' => 'NORMAL', 
                    'palatum' => 'SEDANG', 
                    'mukosa_mulut' => 'NORMAL', 
                    'lidah' => 'NORMAL', 
                    'ginggiva_ra' => 'TIDAK ADA', 
                    'ginggiva_rb' => 'TIDAK ADA', 
                    'karang_gigi' => '', 
                    'pocket' => 'TIDAK ADA'
                ],
            ];
            $this->gigiKlinis = [];
            $this->dokterId = null; 
        }
    }

    public function toggleGigiKlinis($gigiId)
    {
        $currentStatus = $this->gigiKlinis[$gigiId] ?? 'Normal';
        $nextStatus = match ($currentStatus) {
            'Normal' => 'Caries', 'Caries' => 'Missing', 'Missing' => 'Tambal', 'Tambal' => 'Normal', default => 'Normal', 
        };
        if ($nextStatus === 'Normal') {
            unset($this->gigiKlinis[$gigiId]);
        } else {
            $this->gigiKlinis[$gigiId] = $nextStatus;
        }
    }

    public function getDynamicCssProperty()
    {
        $cssString = '';
        foreach ($this->gigiKlinis as $gigiId => $status) {
            $styles = '';
            if ($status === 'Caries') {
                $styles = 'background-color: #DC2626; border-color: #B91C1C; color: #FFFFFF; font-weight: bold;'; 
            } elseif ($status === 'Missing') {
                $styles = 'background-color: #6B7280; border-color: #4B5563; color: #000000; font-weight: bold;'; 
            } elseif ($status === 'Tambal') {
                $styles = 'background-color: #10B981; border-color: #059669; color: #FFFFFF; font-weight: bold;'; 
            }
            if (!empty($styles)) {
                $cssString .= "#gigi-{$gigiId} { {$styles} }";
            }
        }
        return $cssString;
    }

    public function simpanHasil()
    {
        $this->validate();

        $dokter = Dokter::find($this->dokterId); 
        if (!$dokter) {
            session()->flash('error', 'Gagal: Data dokter yang dipilih tidak ditemukan.');
            return; 
        }

        $dataPemeriksaan = ['dataForm' => $this->dataForm, 'gigiKlinis' => $this->gigiKlinis];
        
        $this->poliGigiResult->jadwal_poli_id = $this->poliData->id;
        $this->poliGigiResult->fill([
            'data_pemeriksaan' => $dataPemeriksaan, 
            'kesimpulan' => $this->kesimpulan,
            'keterangan' => $this->keterangan,
            'dokter_id' => $this->dokterId, 
        ]);

        $patientIdentifier = $this->patient->nama_pasien ?? $this->patient->nama_karyawan ?? 'N/A';
        $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientIdentifier);
        $safeIdentifier = str_replace(' ', '_', $safeIdentifier);
        $safeIdentifier = substr($safeIdentifier, 0, 50); 

        $fileName = 'Hasil_Pemeriksaan_Poli_Gigi_' . $safeIdentifier . '_Jadwal_' . $this->poliGigiResult->jadwal_poli_id .'_'. time(). '.pdf';
        
        $folderPath = 'pdf_reports';
        $storagePath = $folderPath . '/' . $fileName; 

        try {
            $data = [
                'dynamicCss' => $this->getDynamicCssProperty(), 
                'patient' => $this->patient,
                'ekstraOral' => $this->dataForm['ekstraOral'] ?? [],
                'intraOral' => $this->dataForm['intraOral'] ?? [],
                'keterangan' => $this->keterangan,
                'kesimpulan' => $this->kesimpulan,
                'gigiKlinis' => $this->gigiKlinis,
                'dokter' => $dokter, 
                'isKaryawan' => $this->isKaryawan,
                'instansiPasien' => $this->instansiPasien,
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.poli-gigi-report', $data);

            $uploadSuccess = Storage::disk('public')->put($storagePath, $pdf->output());
            if (!$uploadSuccess) {
                throw new \Exception("Sistem Gagal Mengunggah PDF ke Google Cloud Storage. Pastikan file JSON kredensial valid dan koneksi internet stabil.");
            }
                        
            $this->poliGigiResult->file_path = $storagePath;
            $this->poliGigiResult->save(); 

            $this->poliData->file_path = $storagePath;
            $this->poliData->status = 'Finished';
            $this->poliData->save();
            
            session()->flash('success', 'Hasil pemeriksaan gigi dan laporan PDF berhasil disimpan!');
            $this->dispatch('status-updated', ['message' => 'Poli Gigi Selesai.']);
            
        } catch (\Exception $e) {
            Log::error('PDF Generation GAGAL (Poli Gigi): ' . $e->getMessage());
            session()->flash('error', 'Gagal membuat/menyimpan file PDF. Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->poliGigiResult = PoliGigiResult::firstOrNew(['jadwal_poli_id' => $this->poliData->id]);
        return view('livewire.poli-gigi-form');
    }
}