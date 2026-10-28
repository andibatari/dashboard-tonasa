<?php

namespace App\Exports;

use App\Models\PemantauanLingkungan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class PemantauanLingkunganExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;

    /**
     * @param Collection $data Koleksi data PemantauanLingkungan
     */
    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Transformasi data untuk format Excel yang rapi
        return $this->data->map(function ($data) {
            $pemantauan = $data->data_pemantauan ?? [];

            return [
                'ID' => $data->id,
                'Area' => $data->area,
                'Lokasi' => $data->lokasi,
                'Tanggal Pemantauan' => Carbon::parse($data->tanggal_pemantauan)->format('d-m-Y'),                
                // Data Pengukuran
                'Cahaya (Lux)' => $pemantauan['cahaya'] ?? 'N/A',
                'Bising (dB)' => $pemantauan['bising'] ?? 'N/A',
                'Debu (mg/Nm3)' => $pemantauan['debu'] ?? 'N/A',
                'Suhu Basah (°C)' => $pemantauan['suhu_basah'] ?? 'N/A',
                'Suhu Kering (°C)' => $pemantauan['suhu_kering'] ?? 'N/A',
                'Suhu Radiasi (°C)' => $pemantauan['suhu_radiasi'] ?? 'N/A',
                'ISBB Indoor (°C)' => $pemantauan['isbb_indoor'] ?? 'N/A',
                'ISBB Outdoor (°C)' => $pemantauan['isbb_outdoor'] ?? 'N/A',
                'RH (%)' => $pemantauan['rh'] ?? 'N/A',
                'NAB Suhu (°C)' => $pemantauan['nab_suhu'] ?? 'N/A',

                // Nilai Ambang Batas (NAB) Statis
                'NAB Cahaya' => $data->nab_cahaya,
                'NAB Bising' => $data->nab_bising,
                'NAB Debu' => $data->nab_debu,
            ];
        });
    }

    /**
     * Menyediakan baris judul/header untuk Excel.
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Area',
            'Lokasi',
            'Tanggal Pemantauan',
            'Cahaya (Lux)',
            'Bising (dB)',
            'Debu (mg/Nm3)',
            'Suhu Basah (°C)',
            'Suhu Kering (°C)',
            'Suhu Radiasi (°C)',
            'ISBB Indoor (°C)',
            'ISBB Outdoor (°C)',
            'RH (%)',
            'NAB Suhu (°C)',
            'NAB Cahaya',
            'NAB Bising',
            'NAB Debu',
        ];
    }
}
