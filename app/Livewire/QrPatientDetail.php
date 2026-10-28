<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\JadwalMcu;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\JadwalPoli;
use Livewire\WithFileUploads;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class QrPatientDetail extends Component
{
    use WithFileUploads;

    public $jadwal;
    public $patient;
    public $activeTab = 'summary';
    public $formInputs = [];
    public $qrCodeImage;
    public $pdfFiles = [];

    public $resumeData = [];
    public $resumeSaran;
    public $resumeKategori;
    public $uploadedFileNames = [];
    public $uploadablePoliNames = ['LABORATORIUM', 'SPIROMETRI', 'AUDIOMETRI', 'EKG', 'THORAX PHOTO', 'TREADMILL', 'USG'];

    protected $listeners = ['updatePoliStatus', 'changeTab'];

    public function handleUploadError($name, $errors, $isMultiple)
    {
        $this->dispatch('error', ['message' => 'File terlalu besar atau koneksi terputus.']);
    }

    public function changeTab($tabName)
    {
        if ($tabName) $this->activeTab = $tabName;
    }

    protected $rules = [
        'pdfFiles.*' => 'nullable|file|mimes:pdf|max:10240',
        'resumeData.bmi' => 'nullable|string|max:255',
        'resumeData.laboratorium' => 'nullable|string|max:255',
        'resumeData.ekg' => 'nullable|string|max:255',
        'resumeData.gigi' => 'nullable|string|max:255',
        'resumeData.mata' => 'nullable|string|max:255',
        'resumeData.spirometri' => 'nullable|string|max:255',
        'resumeData.audiometri' => 'nullable|string|max:255',
        'resumeData.kebugaran' => 'nullable|string|max:255',
        'resumeData.temuan_lain' => 'nullable|string|max:255',
        'resumeData.thorax' => 'nullable|string|max:255',
        'resumeData.treadmill' => 'nullable|string|max:255',
        'resumeData.usg' => 'nullable|string|max:255',
        'resumeSaran' => 'nullable|string|max:5000',
        'resumeKategori' => 'nullable|string|max:255',
    ];

    public function mount(JadwalMcu $jadwal)
    {
        $this->jadwal = $jadwal;
        if (request()->has('tab')) $this->activeTab = request()->query('tab');

        $patientData = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        $existingData = $jadwal->resume_body ? json_decode($jadwal->resume_body, true) : [];
        $this->resumeData = array_merge($this->getDefaultResumeData(), $existingData);

        if (empty($this->resumeData['bmi']) && $patientData) {
            $weight = $patientData->berat_badan;
            $height = $patientData->tinggi_badan;
            if ($weight > 0 && $height > 0) {
                $heightInMeter = $height / 100;
                $bmiValue = $weight / ($heightInMeter * $heightInMeter);
                $this->resumeData['bmi'] = number_format($bmiValue, 1);
            }
        }

        $this->resumeSaran = $jadwal->resume_saran ?? '';
        $this->resumeKategori = $jadwal->resume_kategori ?? '';
    }

    protected function getDefaultResumeData()
    {
        return [
            'bmi' => null, 'laboratorium' => null, 'ekg' => null, 'gigi' => null,
            'mata' => null, 'spirometri' => null, 'audiometri' => null,
            'kebugaran' => null, 'temuan_lain' => null, 'thorax' => null,
            'treadmill' => null, 'usg' => null,
        ];
    }

    public function saveResume()
    {
        $this->validate();
        try {
            $this->jadwal->update([
                'resume_body' => json_encode($this->resumeData),
                'resume_saran' => $this->resumeSaran,
                'resume_kategori' => $this->resumeKategori,
            ]);
            session()->flash('success', 'Data resume medis berhasil disimpan!');
            $this->dispatch('status-updated', ['message' => 'Resume berhasil disimpan!']);
            $this->jadwal->refresh();
        } catch (\Throwable $e) {
            Log::error("Gagal menyimpan resume: " . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan resume. Silakan coba lagi.');
            $this->dispatch('error', ['message' => 'Gagal menyimpan resume. Silakan coba lagi.']);
        }
    }

    public function downloadAllPdfs()
    {
        $this->jadwal->refresh();
        if (!$this->jadwal || !$this->jadwal->id) {
            $this->dispatch('show-error', ['message' => 'ID Jadwal tidak dapat diakses untuk penggabungan file.']);
            return;
        }
        $this->dispatch('view-merged-pdf', jadwalId: $this->jadwal->id);
    }

    public function saveStatus($poliId)
    {
        $status = $this->formInputs[$poliId]['status'] ?? null;
        if ($status) $this->updatePoliStatus($poliId, $status);
    }

    // 🌟 FUNGSI BARU UNTUK MENGIRIM SINYAL SILUMAN
    private function sendSilentRefreshSignal()
    {
        $fcmToken = null;
        if ($this->jadwal->karyawan_id) {
            $fcmToken = \App\Models\EmployeeLogin::where('karyawan_id', $this->jadwal->karyawan_id)->value('fcm_token');
        } elseif ($this->jadwal->peserta_mcus_id) {
            $fcmToken = \App\Models\PesertaMcuLogin::where('peserta_mcu_id', $this->jadwal->peserta_mcus_id)->value('fcm_token');
        }

        if ($fcmToken) {
            // Tembak FCM dengan tipe 'silent_update' agar HP tidak berbunyi, tapi layarnya merefresh
            \App\Services\FCMService::sendPushNotification(
                $fcmToken, 
                "Update Siluman", 
                "Refresh Layar", 
                '', 
                null, 
                'silent_update'
            );
        }
    }

    public function updatePoliStatus($poliId, $status)
    {
        $this->jadwal->refresh();
        $jadwalPoli = $this->jadwal->jadwalPoli->firstWhere('poli_id', $poliId);
        if ($jadwalPoli) {
            $jadwalPoli->status = $status;
            $jadwalPoli->save();
            $this->jadwal->load('jadwalPoli.poli');
            $this->dispatch('status-updated', ['message' => 'Status poli berhasil diperbarui.']);
            
            // 🌟 PANGGIL FUNGSI SILUMAN DI SINI
            $this->sendSilentRefreshSignal();
        }
    }

    public function savePdf($poliId)
    {
        $this->validate(["pdfFiles.$poliId" => 'required|file|mimes:pdf|max:10240']);
        $jadwalPoli = $this->jadwal->jadwalPoli()->with('poli')->where('poli_id', $poliId)->first();

        if (!$jadwalPoli) {
            $this->dispatch('error', ['message' => 'Data jadwal poli tidak ditemukan.']);
            return;
        }

        if (isset($this->pdfFiles[$poliId])) {
            try {
                $file = $this->pdfFiles[$poliId];
                $patientName = $this->patient->nama_lengkap ?? $this->patient->nama_karyawan ?? 'Pasien';
                $safePatientName = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', $patientName));
                $namaPoli = $jadwalPoli->poli->nama_poli ?? 'Poli';
                $safeNamaPoli = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', $namaPoli));
                $tahun = now()->format('Y');
                $fileName = 'Hasil_Pemeriksaan_' . $safeNamaPoli . '_' . $safePatientName . '_' . $tahun . '_' . now()->timestamp . '.pdf';

                $path = $file->storeAs('mcu_results', $fileName, 'public');
                $jadwalPoli->file_path = $path;
                $jadwalPoli->status = 'Finished';
                $jadwalPoli->save();

                $this->uploadedFileNames[$poliId] = $fileName;
                $this->pdfFiles[$poliId] = null;
                $this->dispatch('status-updated', ['message' => 'File berhasil diunggah ke Cloud Storage!']);
                
                // 🌟 PANGGIL FUNGSI SILUMAN DI SINI
                $this->sendSilentRefreshSignal();
                
            } catch (\Throwable $e) {
                Log::error("S3 Upload Error: " . $e->getMessage());
                $this->dispatch('error', ['message' => 'Gagal mengunggah ke Cloud: ' . $e->getMessage()]);
            }
        } else {
            $this->dispatch('error', ['message' => 'Silakan pilih file PDF untuk diunggah.']);
        }
    }

    public function markAsDone($poliId) { $this->updatePoliStatus($poliId, 'Finished'); }
    public function markAsPending($poliId) { $this->updatePoliStatus($poliId, 'Pending'); }

    public function panggilPasien($poliId)
    {
        $jadwalPoli = $this->jadwal->jadwalPoli->firstWhere('poli_id', $poliId);

        if ($jadwalPoli) {
            $jadwalPoli->status = 'Calling';
            $jadwalPoli->save();
            $namaPoli = $jadwalPoli->poli->nama_poli ?? 'Poli';

            // 🌟 PERBAIKAN: Ambil token langsung dari tabel login yang baru (employee_logins / peserta_mcu_logins)
            $fcmToken = null;
            if ($this->jadwal->karyawan_id) {
                $fcmToken = \App\Models\EmployeeLogin::where('karyawan_id', $this->jadwal->karyawan_id)->value('fcm_token');
            } elseif ($this->jadwal->peserta_mcus_id) {
                $fcmToken = \App\Models\PesertaMcuLogin::where('peserta_mcu_id', $this->jadwal->peserta_mcus_id)->value('fcm_token');
            }

            if ($fcmToken) {
                $this->sendFcmNotification($fcmToken, $namaPoli);
                $this->dispatch('status-updated', ['message' => "Panggilan ke $namaPoli telah dikirim via FCM!"]);
            } else {
                Log::warning("Gagal mengirim panggilan: Token FCM kosong untuk jadwal ID: " . $this->jadwal->id);
                $this->dispatch('error', ['message' => "Gagal memanggil, token perangkat pasien tidak terdaftar."]);
            }
        }
    }

    private function sendFcmNotification($token, $namaPoli)
    {
        $pasienName = $this->jadwal->karyawan->nama_karyawan ?? $this->jadwal->pesertaMcu->nama_lengkap ?? 'Pasien';
        $noSap = $this->jadwal->karyawan->no_sap ?? null;

        \App\Services\FCMService::sendPushNotification(
            $token, 
            "PANGGILAN PEMERIKSAAN", 
            "Hai {$pasienName}, Giliran Anda! Silakan segera masuk ke ruangan {$namaPoli}", 
            '', 
            $noSap, 
            'panggilan_poli'
        );
    }

    public function render()
    {
        $this->jadwal->refresh()->load(['karyawan', 'pesertaMcu', 'paketMcu.poli', 'jadwalPoli.poli', 'dokter']);
        $relatedPatient = $this->jadwal->karyawan ?? $this->jadwal->pesertaMcu;

        if ($relatedPatient) {
            $this->patient = $relatedPatient;
        } else {
            $this->patient = (object) [
                'nama_lengkap' => $this->jadwal->nama_pasien ?? 'Pasien Tidak Ditemukan',
                'nama_karyawan' => $this->jadwal->nama_pasien ?? 'Pasien Tidak Ditemukan',
                'perusahaan_asal' => $this->jadwal->perusahaan_asal ?? 'N/A',
                'no_sap' => 'N/A', 'nik_pasien' => 'N/A', 'nik_karyawan' => 'N/A',
                'tanggal_lahir' => $this->jadwal->tanggal_mcu, 'jenis_kelamin' => 'N/A', 'agama' => 'N/A',
            ];
        }

        $this->qrCodeImage = null;
        if (!empty($this->jadwal->qr_code_id)) {
            try {
                $qrCode = QrCode::create($this->jadwal->qr_code_id);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $this->qrCodeImage = base64_encode($result->getString());
            } catch (\Exception $e) {
                $this->qrCodeImage = null;
            }
        }

        $polis = $this->jadwal->paketMcu ? $this->jadwal->paketMcu->poli : collect();
        $jadwalPoliData = $this->jadwal->jadwalPoli->keyBy('poli_id');

        foreach ($polis as $poli) {
            if (!isset($this->formInputs[$poli->id])) {
                $poliData = $jadwalPoliData[$poli->id] ?? (object)['status' => 'Pending', 'file_path' => null];
                $this->formInputs[$poli->id]['status'] = $poliData->status;
                $this->uploadedFileNames[$poli->id] = basename($poliData->file_path);
            }
        }

        return view('livewire.qr-patient-detail', compact('polis', 'jadwalPoliData'))
            ->layout('layouts.app', ['title' => 'Detail Pasien MCU']);
    }
}