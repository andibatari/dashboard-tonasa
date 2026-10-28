<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\JadwalMcu;
use App\Models\JadwalPoli;
use App\Models\PoliGigiResult;
use App\Models\KebugaranResult;
use App\Models\FisikResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class McuPdfController extends Controller
{
    public function viewPdf($id)
    {
        $poliData = JadwalPoli::findOrFail($id);
        if ($poliData->file_path && Storage::disk('public')->exists($poliData->file_path)) {
            return redirect(Storage::disk('public')->url($poliData->file_path));
        }
        abort(404, "File tidak ditemukan di Local Storage.");
    }

    public function viewPdfGigi($id)
    {
        $result = PoliGigiResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectLocal($result->file_path);
    }

    public function viewPdfKebugaran($id)
    {
        $result = KebugaranResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectLocal($result->file_path);
    }

    public function viewPdfFisik($id)
    {
        $result = FisikResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectLocal($result->file_path);
    }

    private function redirectLocal($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return redirect(Storage::disk('public')->url($filePath));
        }
        abort(404, "File tidak ditemukan di Local Storage.");
    }

    protected function generateResumePdfObject(JadwalMcu $jadwal)
    {
        $jadwal->load(['dokter', 'paketMcu']);
        $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        if (!$patient) return null;

        $doctor = $jadwal->dokter;
        $doctorName = $doctor->nama_lengkap ?? $doctor->name ?? $doctor->nama ?? 'Dokter Tidak Ditunjuk';
        $doctorNip = $doctor->nip ?? 'NIP. N/A';

        $tanggalMcuFormatted = Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y');

        // 🌟 AMBIL DATA DARI DATABASE (System Settings)
        $namaKepalaKlinik = \App\Models\Setting::where('key', 'nama_kepala_klinik')->value('value') ?? 'Dr. Penanggung Jawab';
        $teksDisclaimerRaw = \App\Models\Setting::where('key', 'teks_disclaimer')->value('value') ?? 'Pada Pemeriksaan Kesehatan Berkala di Klinik Semen Tonasa Medical Centre yang dilakukan pada tanggal <b>[TANGGAL]</b>...';
        $teksDisclaimerFinal = str_replace('[TANGGAL]', $tanggalMcuFormatted, $teksDisclaimerRaw);

        // 🌟 FUNGSI PENGUBAH GAMBAR KE BASE64 (Agar DomPDF pasti bisa membaca gambarnya)
        $getBase64Image = function ($dbPath, $defaultPath) {
            $fullPath = null;

            // 1. Cek dari Database (Storage)
            if ($dbPath) {
                $cleanPath = str_replace('storage/', '', $dbPath);
                $storagePath = storage_path('app/public/' . $cleanPath);
                if (file_exists($storagePath)) $fullPath = $storagePath;
            }
            // 2. Jika tidak ada di Storage, pakai default dari public/images
            if (!$fullPath && file_exists(public_path($defaultPath))) {
                $fullPath = public_path($defaultPath);
            }

            // 3. Konversi ke Base64
            if ($fullPath) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            return ''; // Kosong jika tidak ada gambar sama sekali
        };

        // Konversi logo STMC dan Tonasa
        $logoStmcBase64 = $getBase64Image(\App\Models\Setting::where('key', 'logo_stmc')->value('value'), 'images/logo-stmc.png');
        $logoTonasaBase64 = $getBase64Image(\App\Models\Setting::where('key', 'logo_tonasa')->value('value'), 'images/logo-semen-tonasa.png');

        $linkValidasiPublik = route('validasi.pdf', $jadwal->qr_code_id);
        $qrCode = new QrCode($linkValidasiPublik);
        $qrCode->setSize(150);
        $qrCode->setMargin(0);
        $qrCode->setEncoding(new Encoding('UTF-8'));
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrCodeBase64 = base64_encode($result->getString());

        $data = [
            'jadwal' => $jadwal,
            'patient' => $patient,
            'tanggal_mcu' => Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y'),
            'tanggal_cetak' => Carbon::now()->format('d/m/Y'),
            'resume_body_raw' => $jadwal->resume_body,
            'resume_saran' => $jadwal->resume_saran,
            'resume_kategori' => $jadwal->resume_kategori,
            'qrCodeBase64' => $qrCodeBase64,
            'doctor_data' => ['nama' => $doctorName, 'nip' => $doctorNip],
            'patient_data' => [
                'nama' => $patient->nama_lengkap ?? $patient->nama_karyawan,
                'alamat' => $patient->alamat ?? 'N/A',
                'tgl_lahir' => $patient->tanggal_lahir ?? 'N/A',
                'jenis_kelamin' => $patient->jenis_kelamin ?? 'N/A',
                'paket_mcu' => $jadwal->paketMcu->nama_paket ?? 'N/A',
                'nik_sap' => $patient->no_sap ?? $patient->nik_karyawan ?? 'N/A',
                'unit_kerja' => $patient->unitKerja->nama_unit_kerja ?? 'N/A',
                'nab_suhu_kerja' => 28.0
            ],

            // 🌟 KIRIM DATA YANG BENAR KE VIEW
            'setting_kepala_klinik' => $namaKepalaKlinik,
            'setting_disclaimer'  => $teksDisclaimerFinal,
            'setting_logo_stmc'   => $logoStmcBase64,
            'setting_logo_tonasa' => $logoTonasaBase64,
        ];

        return Pdf::loadView('pdfs.mcu_resume', $data);
    }

    public function downloadResume($jadwalId)
    {
        $jadwal = JadwalMcu::with(['karyawan', 'pesertaMcu', 'paketMcu', 'dokter'])->findOrFail($jadwalId);
        $resumePdf = $this->generateResumePdfObject($jadwal);
        $patientName = ($jadwal->karyawan ?? $jadwal->pesertaMcu)->nama_lengkap ?? ($jadwal->karyawan ?? $jadwal->pesertaMcu)->nama_karyawan ?? 'Pasien';
        $fileName = 'Resume_MCU_' . str_replace(' ', '_', $patientName) . '_' . $jadwal->tanggal_mcu . '.pdf';

        return $resumePdf->stream($fileName);
    }

    public function downloadMcuSummary($jadwalId)
    {
        $jadwal = JadwalMcu::with(['jadwalPoli.poli', 'karyawan', 'pesertaMcu'])->findOrFail($jadwalId);
        $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        $patientName = $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien';

        $tempFiles = [];
        $pdfFilesToMerge = [];

        $tempDirFullPath = storage_path('app/temp_pdf');
        if (!file_exists($tempDirFullPath)) {
            mkdir($tempDirFullPath, 0777, true);
        }

        $resumePdf = $this->generateResumePdfObject($jadwal);
        if (!$resumePdf) abort(404, 'Data pasien tidak ditemukan.');

        $tempResumePath = $tempDirFullPath . '/resume_' . $jadwalId . '_' . time() . '.pdf';
        $resumePdf->save($tempResumePath);
        $pdfFilesToMerge[] = $tempResumePath;
        $tempFiles[] = $tempResumePath;

        foreach ($jadwal->jadwalPoli as $jp) {
            $filePath = $jp->file_path;

            // Bersihkan URL jika terlanjur tersimpan sebagai string HTTPS
            if ($filePath && str_contains($filePath, 'http')) {
                $parsedUrl = parse_url($filePath, PHP_URL_PATH);
                $filePath = urldecode(ltrim(str_replace('/stmc-health-bucket/', '', $parsedUrl), '/'));
            }

            if ($filePath) {
                $fileContent = null;

                // LOGIKA BARU: Cek public dulu, kalau gagal/tidak ada, cek Local Storage (public)
                try {
                    if (Storage::disk('public')->exists($filePath)) {
                        $fileContent = Storage::disk('public')->get($filePath);
                        \Log::info("File ditemukan di public: " . $filePath);
                    } elseif (Storage::disk('public')->exists($filePath)) {
                        $fileContent = Storage::disk('public')->get($filePath);
                        \Log::info("File ditemukan di Local Storage: " . $filePath);
                    }
                } catch (\Exception $e) {
                    \Log::error("Error saat mengecek file {$filePath}: " . $e->getMessage());
                }

                // Jika file berhasil ditemukan di salah satu tempat, simpan ke temp untuk di-merge
                if ($fileContent) {
                    $localTempPoli = $tempDirFullPath . '/poli_' . $jp->id . '_' . time() . '.pdf';
                    file_put_contents($localTempPoli, $fileContent);

                    $pdfFilesToMerge[] = $localTempPoli;
                    $tempFiles[] = $localTempPoli;
                } else {
                    \Log::warning("File poli di-skip saat merge karena tidak ditemukan di public maupun Lokal: " . $filePath);
                }
            }
        }

        $pdfMerger = new Fpdi();
        $pdfMerger->setPrintHeader(false);
        $pdfMerger->setPrintFooter(false);

        foreach ($pdfFilesToMerge as $file) {
            try {
                $pageCount = $pdfMerger->setSourceFile($file);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $template = $pdfMerger->importPage($i);
                    $size = $pdfMerger->getTemplateSize($template);
                    $pdfMerger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdfMerger->useTemplate($template);
                }
            } catch (\Exception $e) {
                \Log::error("Gagal merge file: {$file}. Error: " . $e->getMessage());
            }
        }

        foreach ($tempFiles as $tempFile) {
            if (file_exists($tempFile)) unlink($tempFile);
        }

        $fileName = 'Hasil_Lengkap_MCU_' . str_replace(' ', '_', $patientName) . '.pdf';
        
        // 🌟 PERBAIKAN: Gunakan parameter 'I' (Inline) dan hentikan script (exit) 
        // agar PDF langsung dirender oleh browser dengan sempurna
        $pdfMerger->Output($fileName, 'I');
        exit;
    }
}
