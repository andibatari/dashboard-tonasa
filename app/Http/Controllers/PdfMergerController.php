<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\JadwalMcu;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response; // PENTING: Tambahkan Response Facade
use Error; // Import Error Class
use iio\libmergepdf\Merger; // <-- Class yang BENAR
use iio\libmergepdf\Exception as LibMergeException; // <-- Penanganan error dari libmergepdf

class PdfMergerController extends Controller
{
    /**
     * Menggabungkan semua PDF dari poli yang sudah selesai dan MENAMPILKANNYA SECARA INLINE.
     * @param int $jadwalId
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function mergeAndDownload($jadwalId)
    {
        try {
            $jadwal = JadwalMcu::with(['jadwalPoli', 'karyawan', 'pesertaMcu'])->findOrFail($jadwalId); 
            $patientName = $jadwal->karyawan->nama_lengkap ?? ($jadwal->pesertaMcu->nama_lengkap ?? ($jadwal->nama_pasien ?? 'Pasien_MCU'));

            // 2. Ambil path file PDF (Harus konsisten untuk semua jenis file)
            $pdfPaths = $jadwal->jadwalPoli
                ->filter(fn($jp) => $jp->file_path && in_array($jp->status, ['Done', 'Finished']))
                ->pluck('file_path')
                ->map(function($pathInDb) {
                    // Cek jika path adalah nama file saja (dari upload) atau path penuh (dari form)
                    if (str_contains($pathInDb, '/')) {
                        // Jika sudah berbentuk 'public/pdf_reports/nama_file.pdf'
                        $cleanPath = str_replace('public/', '', $pathInDb);
                    } else {
                        // Jika hanya nama file (dari upload, karena di savePdf() kita hanya simpan nama file)
                        $cleanPath = 'pdf_reports/' . $pathInDb;
                    }
                    
                    $fullPath = Storage::disk('public')->path($cleanPath); 
                    return $fullPath;
                }) 
                ->filter(fn($fullPath) => file_exists($fullPath)) 
                ->toArray();
            
            if (empty($pdfPaths)) {
                return back()->with('error', 'Tidak ada file PDF hasil poli yang berstatus Selesai (Finished) untuk dilihat. Pastikan status Finished.');
            }

            // 3. Inisialisasi library penggabungan PDF
            $pdf = new Merger; 
            $filesSuccessfullyAdded = 0; // Tambahkan counter

            foreach ($pdfPaths as $filePath) {
                // filePath adalah path absolut (contoh: C:/xampp/htdocs/stmc-backend/storage/app/public/pdf-results/...)
                
                try {
                    // Coba tambahkan file. Jika file korup/gagal dibuka, ia akan melempar exception.
                    $pdf->addFile($filePath); 
                    $filesSuccessfullyAdded++;
                } catch (\Throwable $e) {
                    // Log error untuk file spesifik yang gagal
                    Log::warning("Gagal menambahkan file PDF ke merger: " . basename($filePath) . " Error: " . $e->getMessage());
                    // Lanjutkan ke file berikutnya
                    continue; 
                }
            }

            if ($filesSuccessfullyAdded === 0) {
                return back()->with('error', 'Semua file PDF yang ditemukan tidak valid atau rusak dan gagal digabungkan.');
            }

            // 4. Gabungkan dan dapatkan konten biner PDF secara langsung
            $fileContent = $pdf->merge(); 
            
            // 5. Response
            $fileName = 'Hasil_MCU_Gabungan_' . preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', $patientName)) . '_' . time() . '.pdf';
            return Response::make($fileContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Content-Length' => strlen($fileContent),
            ]);

        } catch (\Throwable $e) {
            Log::error('PDF Merge/Fatal Error: ' . $e->getMessage());
            return back()->with('error', 'Kesalahan kritis saat memproses file: ' . $e->getMessage());
        }
    }
}
