<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalPoli;
use App\Models\Dokter;
use App\Models\MataResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PoliMataForm extends Component
{
    // Hanya gunakan ID (integer) agar Livewire tidak error saat proses penyimpanan
    public $jadwalPoliId; 
    public $patient;
    public $mataResult;
    public $listDokter;
    public $dokterId;
    
    public $dataMata = [];
    public $kesimpulan;
    public $keterangan;

    // Aturan validasi HANYA untuk 4 field sesuai gambar
    protected $rules = [
        'dokterId' => 'required',
        'dataMata.visus_kanan' => 'required|string',
        'dataMata.visus_kiri' => 'required|string',
        'dataMata.add' => 'nullable|string',
        'dataMata.pd' => 'nullable|string',
        'kesimpulan' => 'nullable|string',
        'keterangan' => 'nullable|string',
    ];

    public function mount() 
    {
        $this->listDokter = Dokter::all();

        // Ambil data Mata berdasarkan jadwalPoliId secara langsung (Sangat aman dari error)
        $this->mataResult = MataResult::firstOrNew(['jadwal_poli_id' => $this->jadwalPoliId]);

        if ($this->mataResult->exists) {
            $this->dataMata = $this->mataResult->data_mata;
            $this->kesimpulan = $this->mataResult->kesimpulan;
            $this->keterangan = $this->mataResult->keterangan;
            $this->dokterId = $this->mataResult->dokter_id;
        } else {
            // Nilai Default sesuai gambar referensimu
            $this->dataMata = [
                'visus_kanan' => 'Plano 6/6',
                'visus_kiri' => 'Plano 6/6',
                'add' => '+2.00',
                'pd' => '-/60',
            ];
            $this->dokterId = null;
        }
    }

    public function simpanHasil()
    {
        $this->validate();

        try {
            $dataToSave = [
                'data_mata' => $this->dataMata,
                'kesimpulan' => $this->kesimpulan,
                'keterangan' => $this->keterangan,
                'dokter_id' => $this->dokterId,
            ];

            // 1. Simpan ke Database
            $this->mataResult = MataResult::updateOrCreate(
                ['jadwal_poli_id' => $this->jadwalPoliId],
                $dataToSave
            );

            // 2. Generate PDF Khusus Mata
            $dokter = Dokter::find($this->dokterId);
            $patientName = is_array($this->patient) 
                            ? ($this->patient['nama_lengkap'] ?? $this->patient['nama_karyawan'] ?? 'Pasien')
                            : ($this->patient->nama_lengkap ?? $this->patient->nama_karyawan ?? 'Pasien');
                            
            $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientName);
            $fileName = 'Hasil_Pemeriksaan_Poli_Mata_' . $safeIdentifier . '_Jadwal_' . $this->jadwalPoliId . '_' . time() . '.pdf';
            $storagePath = 'pdf_reports/' . $fileName; 

            $reportData = [
                'patient' => $this->patient,
                'mataResult' => $this->mataResult,
                'dokter' => $dokter,
                'instansiPasien' => is_array($this->patient) ? ($this->patient['perusahaan_asal'] ?? 'PT SEMEN TONASA') : ($this->patient->perusahaan_asal ?? 'PT SEMEN TONASA'),
            ];

            $pdf = Pdf::loadView('pdfs.poli-mata-report', $reportData);
            $uploadSuccess = Storage::disk('public')->put($storagePath, $pdf->output());

            if (!$uploadSuccess) {
                throw new \Exception("Gagal menyimpan file PDF ke server.");
            }

            // 3. Simpan Path File dan Ubah Status
            $this->mataResult->file_path = $storagePath; 
            $this->mataResult->save();

            $poliModel = JadwalPoli::find($this->jadwalPoliId);
            if ($poliModel) {
                $poliModel->file_path = $storagePath;
                $poliModel->status = 'Finished';
                $poliModel->save();
            }

            session()->flash('success', 'Hasil Poli Mata & PDF berhasil dibuat dan disimpan!');

        } catch (\Exception $e) {
            Log::error('Penyimpanan Poli Mata GAGAL: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.poli-mata-form');
    }
}