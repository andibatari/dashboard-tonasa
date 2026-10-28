<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IcofrSummaryExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithEvents
{
    protected array $data;
    protected string $year;
    protected string $tw;

    public function __construct(array $data, int|string $year = '', string $tw = '')
    {
        $this->data = $data;
        $this->year = (string) $year;
        $this->tw   = (string) $tw;
    }

    public function collection(): Collection
    {
        return collect($this->data)->map(function ($row) {
            return [
                'Siklus'              => $row->code . ' - ' . $row->name,
                'Key Control'         => (int) $row->key_controls,
                'Non Key Control'     => (int) $row->non_key_controls,
                'Total Kontrol'       => (int) $row->total_controls,
                'Efektif'             => (int) $row->efektif,
                'Tidak Efektif'       => (int) $row->tidak_efektif,
                'Tidak Ada Transaksi' => (int) $row->tidak_ada,
                'In Progress'         => (int) $row->in_progress,
                'Capaian CSA'         => ($row->csa_progress ?? 0) . ' %',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SIKLUS BISNIS',
            'KEY CONTROL',
            'NON KEY CONTROL',
            'TOTAL KONTROL (RCM)',
            'EFEKTIF',
            'TIDAK EFEKTIF (DEFISIENSI)',
            'TIDAK ADA TRANSAKSI',
            'IN PROGRESS',
            'PROGRESS CAPAIAN CSA (% KEY)'
        ];
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function title(): string
    {
        return 'Dasbor ICoFR ' . ($this->tw ?: 'Ringkasan');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Title Block (Baris 1 - 4)
                $sheet->getRowDimension(1)->setRowHeight(12);

                // Row 2: Nama Perusahaan
                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'PT SEMEN TONASA');
                $sheet->getStyle('A2')->getFont()
                    ->setName('Segoe UI')
                    ->setSize(16)
                    ->setBold(true)
                    ->getColor()->setARGB('FF991B1B'); // Tonasa Crimson
                $sheet->getStyle('A2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(2)->setRowHeight(24);

                // Row 3: Judul Laporan
                $sheet->mergeCells('A3:I3');
                $sheet->setCellValue('A3', 'LAPORAN PEMANTAUAN IMPLEMENTASI INTERNAL CONTROL OVER FINANCIAL REPORTING (ICoFR)');
                $sheet->getStyle('A3')->getFont()
                    ->setName('Segoe UI')
                    ->setSize(11)
                    ->setBold(true)
                    ->getColor()->setARGB('FF1E293B'); // Slate 800
                $sheet->getStyle('A3')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(3)->setRowHeight(20);

                // Row 4: Periode & Tanggal Cetak
                $periodText = !empty($this->tw) && !empty($this->year)
                    ? "Periode: {$this->tw} / {$this->year}"
                    : "Periode: Seluruh Periode";
                $dateText = 'Diekspor pada: ' . date('d F Y, H:i') . ' WITA';
                $sheet->mergeCells('A4:I4');
                $sheet->setCellValue('A4', "{$periodText}   |   {$dateText}");
                $sheet->getStyle('A4')->getFont()
                    ->setName('Segoe UI')
                    ->setSize(9)
                    ->setItalic(true)
                    ->getColor()->setARGB('FF64748B'); // Slate 500
                $sheet->getStyle('A4')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(4)->setRowHeight(18);

                // Row 5: Spacing
                $sheet->getRowDimension(5)->setRowHeight(10);

                // 2. Header Tabel (Baris 6)
                $sheet->getRowDimension(6)->setRowHeight(32);
                $headerRange = 'A6:I6';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'name'  => 'Segoe UI',
                        'size'  => 10,
                        'bold'  => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF991B1B'], // Tonasa Corporate Red
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF7F1D1D'],
                        ],
                    ],
                ]);

                // 3. Data Baris Tabel
                $rowCount = count($this->data);
                $startDataRow = 7;
                $endDataRow = $startDataRow + max(0, $rowCount - 1);

                if ($rowCount > 0) {
                    for ($row = $startDataRow; $row <= $endDataRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(22);
                        $bg = ($row % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF'; // Zebra
                        $rowRange = "A{$row}:I{$row}";
                        $sheet->getStyle($rowRange)->applyFromArray([
                            'font' => [
                                'name'  => 'Segoe UI',
                                'size'  => 10,
                                'color' => ['argb' => 'FF334155'],
                            ],
                            'fill' => [
                                'fillType'   => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $bg],
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color'       => ['argb' => 'FFE2E8F0'],
                                ],
                            ],
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                        // Siklus Bisnis rata kiri dengan sedikit indent
                        $sheet->getStyle("A{$row}")->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                            ->setIndent(1);

                        // Kolom angka & Capaian CSA rata tengah
                        $sheet->getStyle("B{$row}:I{$row}")->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // 4. Baris Total / Kesimpulan
                $totalRow = $endDataRow + 1;
                $sheet->getRowDimension($totalRow)->setRowHeight(26);
                $sheet->setCellValue("A{$totalRow}", 'TOTAL KESELURUHAN');

                if ($rowCount > 0) {
                    $sheet->setCellValue("B{$totalRow}", "=SUM(B{$startDataRow}:B{$endDataRow})");
                    $sheet->setCellValue("C{$totalRow}", "=SUM(C{$startDataRow}:C{$endDataRow})");
                    $sheet->setCellValue("D{$totalRow}", "=SUM(D{$startDataRow}:D{$endDataRow})");
                    $sheet->setCellValue("E{$totalRow}", "=SUM(E{$startDataRow}:E{$endDataRow})");
                    $sheet->setCellValue("F{$totalRow}", "=SUM(F{$startDataRow}:F{$endDataRow})");
                    $sheet->setCellValue("G{$totalRow}", "=SUM(G{$startDataRow}:G{$endDataRow})");
                    $sheet->setCellValue("H{$totalRow}", "=SUM(H{$startDataRow}:H{$endDataRow})");
                    $sheet->setCellValue(
                        "I{$totalRow}",
                        "=IF(B{$totalRow}>0, ROUND((E{$totalRow}+F{$totalRow}+G{$totalRow}+H{$totalRow})/B{$totalRow}*100, 1) & \" %\", \"0 %\")"
                    );
                } else {
                    $sheet->setCellValue("B{$totalRow}", 0);
                    $sheet->setCellValue("C{$totalRow}", 0);
                    $sheet->setCellValue("D{$totalRow}", 0);
                    $sheet->setCellValue("E{$totalRow}", 0);
                    $sheet->setCellValue("F{$totalRow}", 0);
                    $sheet->setCellValue("G{$totalRow}", 0);
                    $sheet->setCellValue("H{$totalRow}", 0);
                    $sheet->setCellValue("I{$totalRow}", "0 %");
                }

                $totalRange = "A{$totalRow}:I{$totalRow}";
                $sheet->getStyle($totalRange)->applyFromArray([
                    'font' => [
                        'name'  => 'Segoe UI',
                        'size'  => 10,
                        'bold'  => true,
                        'color' => ['argb' => 'FF991B1B'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFEF2F2'], // Soft Red Highlight
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF991B1B'],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color'       => ['argb' => 'FF991B1B'],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFE2E8F0'],
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFE2E8F0'],
                        ],
                        'inside' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFE2E8F0'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A{$totalRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setIndent(1);
                $sheet->getStyle("B{$totalRow}:I{$totalRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 5. Freeze Pane di bawah baris Header tabel
                $sheet->freezePane('A7');

                // 6. Lebar Kolom Proporsional
                $sheet->getColumnDimension('A')->setWidth(38);
                $sheet->getColumnDimension('B')->setWidth(16);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(22);
                $sheet->getColumnDimension('H')->setWidth(16);
                $sheet->getColumnDimension('I')->setWidth(24);
            }
        ];
    }
}