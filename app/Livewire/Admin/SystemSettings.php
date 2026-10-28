<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SystemSettings extends Component
{
    use WithFileUploads;

    // Laporan
    public $nama_kepala_klinik, $teks_disclaimer;
    public $logo_stmc, $logo_tonasa; 
    public $current_logo_stmc, $current_logo_tonasa; 

    // Pendaftaran
    public $kuota_harian, $jam_buka, $jam_tutup;

    // Sistem
    public $maintenance_mode;

    public function mount()
    {
        $this->nama_kepala_klinik = $this->getSetting('nama_kepala_klinik', 'Dr. Hj. Fulanah, Sp.KJ');
        $this->teks_disclaimer = $this->getSetting('teks_disclaimer', 'Pada Pemeriksaan Kesehatan Berkala di Klinik Semen Tonasa Medical Centre yang dilakukan pada tanggal <b>[TANGGAL]</b>, ternyata Bapak/Ibu/Sdr (i) harus memperhatikan hal-hal sebagai berikut:');
        $this->current_logo_stmc = $this->getSetting('logo_stmc', 'images/logo-stmc.png');
        $this->current_logo_tonasa = $this->getSetting('logo_tonasa', 'images/logo-semen-tonasa.png');
        
        $this->kuota_harian = $this->getSetting('kuota_harian', 50);
        $this->jam_buka = $this->getSetting('jam_buka', '08:00');
        $this->jam_tutup = $this->getSetting('jam_tutup', '15:00');
        
        $this->maintenance_mode = $this->getSetting('maintenance_mode', '0') == '1';
    }

    private function getSetting($key, $default)
    {
        $setting = Setting::firstOrCreate(['key' => $key], ['value' => $default, 'type' => 'text']);
        return $setting->value;
    }

    private function updateSetting($key, $value)
    {
        Setting::where('key', $key)->update(['value' => $value]);
    }

    public function simpanPengaturan()
    {
        $this->updateSetting('nama_kepala_klinik', $this->nama_kepala_klinik);
        $this->updateSetting('teks_disclaimer', $this->teks_disclaimer);
        $this->updateSetting('kuota_harian', $this->kuota_harian);
        $this->updateSetting('jam_buka', $this->jam_buka);
        $this->updateSetting('jam_tutup', $this->jam_tutup);
        $this->updateSetting('maintenance_mode', $this->maintenance_mode ? '1' : '0');

        if ($this->logo_stmc) {
            $path1 = $this->logo_stmc->store('logos', 'public');
            $this->updateSetting('logo_stmc', 'storage/' . $path1);
            $this->current_logo_stmc = 'storage/' . $path1;
        }

        if ($this->logo_tonasa) {
            $path2 = $this->logo_tonasa->store('logos', 'public');
            $this->updateSetting('logo_tonasa', 'storage/' . $path2);
            $this->current_logo_tonasa = 'storage/' . $path2;
        }

        session()->flash('success', 'Semua pengaturan sistem berhasil disimpan!');
    }

    public function backupDatabase()
    {
        try {
            $filename = "backup_stmc_" . date('Y-m-d_H-i-s') . ".sql";
            $path = storage_path("app/public/" . $filename);
            
            $command = sprintf(
                'mysqldump --user="%s" --password="%s" --host="%s" "%s" > "%s"',
                env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_HOST'), env('DB_DATABASE'), $path
            );
            exec($command);

            if (file_exists($path)) {
                return response()->download($path)->deleteFileAfterSend(true);
            }
            session()->flash('error', 'Gagal memproses backup.');
        } catch (\Exception $e) {
            Log::error('Backup DB Error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function render()
    {
        return view('livewire.admin.system-settings')->layout('layouts.app');
    }
}