<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PemantauanLingkungan;
use Carbon\Carbon;
use App\Models\Departemen; // Asumsi Model Departemen ada
use App\Models\UnitKerja; // Asumsi Model UnitKerja ada

class PemantauanLingkunganForm extends Component
{
    // Properties untuk Model
    public $area;
    public $tanggal_pemantauan;
    public $departemens_id; // BARU: Dari SearchableDepartemen
    public $unit_kerjas_id; // BARU: Dari SearchableDepartemen

    // KRITIS: Struktur data untuk menampung banyak lokasi
    public $lokasiData = [];

    // NAB parameters (Sekarang dapat diubah dan disimpan di kolom terpisah)
    public $nabCahaya = 100;
    public $nabBising = 85;
    public $nabDebu = '10 mg/Nm3';
    public $nabSuhu = 26.7; // BARU: Kolom terpisah

    // Livewire Listeners untuk SearchableDepartemen component
    protected $listeners = [
        'departemenUpdated' => 'setDepartemenId',
        'unitKerjaUpdated' => 'setUnitKerjaId',
    ];
    
    // Setter methods untuk event yang dipancarkan
    public function setDepartemenId($data = null)
    {
        // Cek apakah $data berbentuk Array (['id' => 1]) atau Angka langsung (1)
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        
        $this->departemens_id = $id;
        $this->unit_kerjas_id = null; // Reset Unit Kerja ketika Departemen berubah
    }
    
    public function setUnitKerjaId($data = null)
    {
        // Cek apakah $data berbentuk Array atau Angka langsung
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        
        $this->unit_kerjas_id = $id;
    }

    protected $rules = [
        // Rules untuk ID dan NAB (NAB sekarang wajib)
        'departemens_id' => 'required|numeric|exists:departemens,id',
        'unit_kerjas_id' => 'required|numeric|exists:unit_kerjas,id',
        'nabCahaya' => 'required|numeric|min:0',
        'nabBising' => 'required|numeric|min:0',
        'nabDebu' => 'required|string|max:50', 
        'nabSuhu' => 'required|numeric|min:0', // NAB Suhu sekarang wajib
        
        'area' => 'required|string|max:255',
        'tanggal_pemantauan' => 'required|date',
        // Rules untuk data lokasi
        'lokasiData.*.lokasi' => 'required|string|max:255',
        'lokasiData.*.kesimpulan' => 'nullable|string|max:2000',
        'lokasiData.*.pemantauan.cahaya' => 'nullable|numeric',
        'lokasiData.*.pemantauan.bising' => 'nullable|numeric',
        'lokasiData.*.pemantauan.debu' => 'nullable|numeric',
        'lokasiData.*.pemantauan.suhu_basah' => 'nullable|numeric',
        'lokasiData.*.pemantauan.suhu_kering' => 'nullable|numeric',
        'lokasiData.*.pemantauan.suhu_radiasi' => 'nullable|numeric',
        'lokasiData.*.pemantauan.isbb_indoor' => 'nullable|numeric',
        'lokasiData.*.pemantauan.isbb_outdoor' => 'nullable|numeric',
        'lokasiData.*.pemantauan.rh' => 'nullable|numeric',
    ];

    public function mount()
    {
        $this->tanggal_pemantauan = Carbon::now()->toDateString();
        // Inisialisasi data lokasi
        $this->lokasiData = [
            ['lokasi' => '', 'kesimpulan' => '', 'pemantauan' => $this->getDefaultPemantauanData()],
        ];
    }
    
    protected function getDefaultPemantauanData()
    {
        return [
            // nab_suhu telah dihapus dari sini
            'cahaya' => null, 'bising' => null, 'debu' => null,
            'suhu_basah' => null, 'suhu_kering' => null, 'suhu_radiasi' => null,
            'isbb_indoor' => null, 'isbb_outdoor' => null, 'rh' => null,
        ];
    }

    // Metode untuk menambah dan menghapus lokasi tetap sama...
    public function addLokasi()
    {
        $this->lokasiData[] = ['lokasi' => '', 'pemantauan' => $this->getDefaultPemantauanData()];
    }

    public function removeLokasi($index)
    {
        unset($this->lokasiData[$index]);
        $this->lokasiData = array_values($this->lokasiData); // Re-index array
    }
    
    public function simpanPemantauan()
    {
        $this->validate();

        // Mengulang untuk setiap lokasi dan menyimpannya ke database
        foreach ($this->lokasiData as $lokasiItem) {
            PemantauanLingkungan::create([
                'departemens_id' => $this->departemens_id,
                'unit_kerjas_id' => $this->unit_kerjas_id,
                'area' => $this->area,
                'lokasi' => $lokasiItem['lokasi'],

                // NAB sekarang diambil dari property level component yang dapat diedit
                'nab_cahaya' => $this->nabCahaya,
                'nab_bising' => $this->nabBising,
                'nab_debu' => $this->nabDebu,
                'nab_suhu' => $this->nabSuhu, // Simpan NAB Suhu di kolom terpisah
                
                'data_pemantauan' => $lokasiItem['pemantauan'],
                'tanggal_pemantauan' => $this->tanggal_pemantauan,
                'kesimpulan' => $lokasiItem['kesimpulan'] ?? null, // Kesimpulan per lokasi, jika ada
            ]);
        }

        session()->flash('message', 'Data pemantauan lingkungan berhasil disimpan!');
        $this->reset([
            'area', 'lokasiData', 'departemens_id', 'unit_kerjas_id', 
            'nabCahaya', 'nabBising', 'nabDebu', 'nabSuhu'
        ]);
        $this->mount();
        return redirect()->route('pemantauan.index');
    }

    public function render()
    {
        return view('livewire.pemantauan-lingkungan-form')
            ->layout('layouts.app');
    }
}
