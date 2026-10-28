<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\JadwalMcu;
use App\Models\Poli;
use App\Exports\PemeriksaanExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportPemeriksaan extends Component
{
    use WithPagination;

    public $departemens_id = '';
    public $tipe_anggota = '';
    public $status_kehadiran = '';
    public $poli_id = '';
    public $date_start;
    public $date_end;
    public $total_preview = 0;

    public $searchTable = '';
    public $tableDept = '';
    public $tableUnit = '';

    public function mount()
    {
        $this->date_start = '';
        $this->date_end = '';
        $this->hitungPreview();
    }

    public function updatingSearchTable() { $this->resetPage(); }
    public function updatingTableDept() { 
        $this->resetPage(); 
        $this->tableUnit = ''; 
    }
    public function updatingTableUnit() { $this->resetPage(); }

    public function updated($property)
    {
        $exportProperties = ['date_start', 'date_end', 'departemens_id', 'tipe_anggota', 'status_kehadiran', 'poli_id'];
        if (in_array($property, $exportProperties)) {
            $this->hitungPreview();
        }
    }

    public function hitungPreview()
    {
        // Gunakan query yang clean
        $query = JadwalMcu::query();

        // Filter Tanggal
        if ($this->date_start && $this->date_end) {
            $query->whereBetween('tanggal_mcu', [$this->date_start . ' 00:00:00', $this->date_end . ' 23:59:59']);
        }

        // Filter Status
        if ($this->status_kehadiran) {
            $query->where('status', $this->status_kehadiran);
        }

        // 4. TAMBAHKAN FILTER POLI
        // Asumsi: tabel jadwal_mcu punya relasi atau kolom 'poli_id'
        if ($this->poli_id) {
            $query->where(function($q) {
                $q->whereNotNull('resume_body->' . $this->poli_id)
                  ->where('resume_body->' . $this->poli_id, '!=', 'null') // Cek teks 'null' bawaan JSON
                  ->where('resume_body->' . $this->poli_id, '!=', '');    // Cek string kosong
            });
        }

        // Filter Departemen (Otomatis Karyawan)
        if ($this->departemens_id) {
            $query->whereNotNull('karyawan_id')
                  ->whereHas('karyawan', function($q) {
                      $q->where('departemens_id', $this->departemens_id);
                  });
            $this->tipe_anggota = ''; // Reset jika departemen dipilih
        } 
        // Filter Kategori (Karyawan vs Keluarga/Umum)
        elseif ($this->tipe_anggota) {
            if ($this->tipe_anggota == 'Karyawan') {
                $query->whereNotNull('karyawan_id');
            } else {
                $query->whereNull('karyawan_id'); // Keluarga/Umum biasanya tidak punya karyawan_id
            }
        }
        
        $this->total_preview = $query->count();
    }

    public function exportExcel()
    {
        $filters = [
            'departemens_id'   => $this->departemens_id,
            'tipe_anggota'     => $this->tipe_anggota,
            'status_kehadiran' => $this->status_kehadiran,
            'poli_id'          => $this->poli_id,
            'date_start'       => $this->date_start,
            'date_end'         => $this->date_end,
        ];
        return Excel::download(new PemeriksaanExport($filters), 'Laporan_MCU_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function render()
    {
        $statusLabels = ['Scheduled' => 'Terjadwal', 'Present' => 'Hadir / Proses', 'Finished' => 'Selesai', 'Canceled' => 'Dibatalkan'];
        
        // List Kategori Statis agar dropdown selalu muncul
        $listKategori = ['Karyawan', 'Keluarga/Umum'];
        // BUAT LIST POLI SESUAI DENGAN KEY JSON DI RESUME_BODY
        $listPoli = [
            'laboratorium' => 'Laboratorium',
            'gigi'         => 'Gigi',
            'fisik'        => 'Fisik',
            'mata'         => 'Mata',
            'ekg'          => 'EKG',
            'audiometri'   => 'Audiometri',
            'spirometri'   => 'Spirometri',
            'kebugaran'    => 'Kebugaran',
            'treadmill'    => 'Treadmill',
            'thorax' => 'Thorax Photo',
            'usg'          => 'USG'
        ];

        $listUnitKerja = collect();
        if ($this->tableDept) {
            $listUnitKerja = UnitKerja::where('departemens_id', $this->tableDept)->orderBy('nama_unit_kerja')->get();
        }

        $queryTable = JadwalMcu::with(['karyawan.departemen', 'pesertaMcu']);

        if ($this->searchTable) {
            $search = '%' . $this->searchTable . '%';
            $queryTable->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', $search)
                  ->orWhereHas('karyawan', function($k) use ($search) {
                      $k->where('nama_karyawan', 'like', $search)->orWhere('no_sap', 'like', $search);
                  });
            });
        }

        if ($this->tableDept) { $queryTable->whereHas('karyawan', function($q) { $q->where('departemens_id', $this->tableDept); }); }
        if ($this->tableUnit) { $queryTable->whereHas('karyawan', function($q) { $q->where('unit_kerjas_id', $this->tableUnit); }); }

        return view('livewire.admin.export-pemeriksaan', [
            'listDepartemen' => Departemen::orderBy('nama_departemen')->get(),
            'listPoli'       => $listPoli,
            'listKategori'   => $listKategori, 
            'statusLabels'   => $statusLabels,
            'listUnitKerja'  => $listUnitKerja,
            'jadwalTable'    => $queryTable->orderBy('tanggal_mcu', 'desc')->paginate(10)
        ])->layout('layouts.app');
    }
}