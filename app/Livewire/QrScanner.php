<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\JadwalMcu;
use App\Models\Setting;
use Carbon\Carbon;

class QrScanner extends Component
{
    public $scannedUuid = null;
    public $patient = null;
    public $message = null;
    public $showPopup = false;
    public $jadwal = null;

    protected $listeners = ['qrCodeScanned' => 'handleScan'];

    public function handleScan($uuid)
    {
        // Reset state untuk setiap scan baru
        $this->reset(['scannedUuid', 'patient', 'message', 'showPopup', 'jadwal']);

        // Cari jadwal berdasarkan UUID.
        $this->jadwal = JadwalMcu::where('qr_code_id', $uuid)
                                 ->with(['pesertaMcu', 'karyawan'])
                                 ->first();

        if ($this->jadwal) {
            // Tentukan data pasien dari relasi yang ada
            $this->patient = $this->jadwal->pesertaMcu ?? $this->jadwal->karyawan;

            if ($this->patient) {
                $this->message = "QR Code pasien berhasil dipindai!";
                $this->showPopup = true;
            } else {
                // REVISI: Gunakan data langsung dari tabel jadwal_mcus jika relasi tidak ditemukan
                $this->message = "Data pasien tidak ditemukan untuk jadwal ini. Menggunakan data fallback.";
                $this->patient = (object) [
                    'nama_lengkap' => $this->jadwal->nama_pasien ?? 'Pasien Tidak Ditemukan',
                    'no_sap' => $this->jadwal->no_sap ?? 'N/A',
                ];
                $this->showPopup = true;
            }
        } else {
            $this->message = "Jadwal tidak ditemukan untuk QR Code ini.";
        }
        $this->scannedUuid = $uuid;
    }

    public function cancelRegistration()
    {
        $this->reset(['scannedUuid', 'patient', 'message', 'showPopup', 'jadwal']);
        $this->dispatch('qrScanResumed'); // Kirim event untuk melanjutkan pemindai
    }

    public function continueRegistration()
    {
        if ($this->patient) {
            if ($this->jadwal) {
                if ($this->jadwal->status === 'Scheduled') {
                    $this->jadwal->status = 'Present';
                    $this->jadwal->save();
                    $this->message = "Jadwal berhasil diperbarui. Status: Hadir!";
                } else {
                    $this->message = "Pasien sudah hadir.";
                }

                // Redirect ke halaman detail pasien setelah pembaruan
                return redirect()->route('qr-patient-detail', ['jadwal' => $this->jadwal->id]);
            }
        }
    }

    public function checkPendaftaranStatus()
    {
        // 1. Cek Maintenance
        $isMaintenance = Setting::where('key', 'maintenance_mode')->value('value') == '1';
        if ($isMaintenance) abort(503, 'Sistem Pendaftaran sedang dalam pemeliharaan.');

        // 2. Cek Jam Buka (Logika Anti-Midnight Bug)
        $jamBuka = Setting::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup = Setting::where('key', 'jam_tutup')->value('value') ?? '15:00';
        
        // Kunci zona waktu ke WITA
        $jamSekarang = Carbon::now('Asia/Makassar')->format('H:i');

        if ($jamBuka > $jamTutup) {
            // Logika jika jam operasional melewati tengah malam (contoh: 20:00 - 01:00)
            $isOpen = $jamSekarang >= $jamBuka || $jamSekarang <= $jamTutup;
        } else {
            // Logika jika jam operasional normal di hari yang sama (contoh: 08:00 - 15:00)
            $isOpen = $jamSekarang >= $jamBuka && $jamSekarang <= $jamTutup;
        }

        if (!$isOpen) {
            abort(403, "PENDAFTARAN DITUTUP. JAM BUKA: {$jamBuka} - {$jamTutup} WITA.");
        }

        // 3. Cek Kuota
        $kuotaHarian = Setting::where('key', 'kuota_harian')->value('value') ?? 50;
        $jumlahPasienHariIni = JadwalMcu::whereDate('tanggal_mcu', Carbon::today('Asia/Makassar'))->count();

        if ($jumlahPasienHariIni >= $kuotaHarian) {
            abort(403, "Kuota pendaftaran hari ini penuh (Maks: {$kuotaHarian}).");
        }
    }

    public function render()
    {
        $this->checkPendaftaranStatus();
        return view('livewire.qr-scanner')
            ->layout('layouts.app');
    }
}