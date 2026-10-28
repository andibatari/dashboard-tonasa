<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Mata - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; margin: 0; padding: 30px; color: #334155; }
        .page { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* HEADER KOP SURAT */
        .header { width: 100%; border-bottom: 3px solid #0f4a7b; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .logo { width: 65px; height: auto; }
        .header-text { text-align: center; }
        .header-main { font-size: 16pt; font-weight: 900; color: #0f4a7b; margin: 0; letter-spacing: 1px; }
        .header-sub { font-size: 11pt; font-weight: bold; color: #475569; margin: 3px 0; }
        
        .document-title { text-align: center; font-size: 13pt; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 20px; padding: 10px; background-color: #f1f5f9; border-radius: 4px; border: 1px solid #cbd5e1; letter-spacing: 0.5px;}

        /* KARTU INFO PASIEN */
        .patient-card { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .patient-card td { padding: 6px 10px; border: 1px solid #cbd5e1; font-size: 9.5pt; }
        .patient-card .label { background-color: #f8fafc; font-weight: bold; color: #475569; width: 18%; }
        .patient-card .value { font-weight: 500; color: #0f172a; width: 32%; }

        /* TABEL HASIL PEMERIKSAAN MATA KHUSUS OPTIK */
        .section-title { font-size: 11pt; font-weight: bold; color: #ffffff; background-color: #0f4a7b; padding: 6px 12px; margin: 15px 0 10px 0; border-radius: 3px; }
        
        .mata-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        .mata-table th { background-color: #e2e8f0; color: #334155; font-weight: bold; padding: 8px; border: 1px solid #cbd5e1; text-align: center;}
        .mata-table td { padding: 8px 12px; border: 1px solid #cbd5e1; vertical-align: middle; }
        
        .mata-table .row-title { font-weight: bold; background-color: #f8fafc; width: 25%; text-align: left;}
        .mata-table .col-value { font-weight: bold; color: #0f172a; text-align: center;}

        /* BOX KESIMPULAN */
        .result-box { background-color: #f0fdf4; border-left: 5px solid #16a34a; padding: 12px 15px; margin-bottom: 25px; font-size: 10pt; color: #14532d; border-radius: 2px;}
        
        /* TANDA TANGAN DOKTER */
        .signature-section { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-section td { width: 50%; text-align: center; vertical-align: bottom; }
        .sign-area { height: 70px; }
    </style>
</head>
<body>
    <div class="page">
        <table class="header">
            <tr>
                <td style="width: 15%; text-align: left;"><img src="{{ public_path('images/logo-semen-tonasa.png') }}" class="logo"></td>
                <td style="width: 70%;" class="header-text">
                    <p class="header-main">SEMEN TONASA MEDICAL CENTRE</p>
                    <p class="header-sub">KLINIK UTAMA PT SEMEN TONASA</p>
                </td>
                <td style="width: 15%; text-align: right;"><img src="{{ public_path('images/logo-stmc.png') }}" class="logo"></td>
            </tr>
        </table>

        <div class="document-title">Laporan Hasil Pemeriksaan Mata</div>

        <table class="patient-card">
            <tr>
                <td class="label">Nama Pasien</td><td class="value">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</td>
                <td class="label">NIK / SAP</td><td class="value">{{ $patient->nik_pasien ?? $patient->no_sap ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Umur / Kelamin</td><td class="value">{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->age }} Thn / {{ $patient->jenis_kelamin ?? 'N/A' }}</td>
                <td class="label">Tgl. Periksa</td><td class="value">{{ now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Instansi/Perusahaan</td><td colspan="3" class="value">{{ $instansiPasien ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">A. HASIL PEMERIKSAAN REFRAKSI (VISUS)</div>
        @php $mata = $mataResult->data_mata ?? []; @endphp
        
        <table class="mata-table">
            <thead>
                <tr>
                    <th>Indikator</th>
                    <th>Mata Kanan (VOD)</th>
                    <th>Mata Kiri (VOS)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="row-title">Tajam Penglihatan</td>
                    <td class="col-value">{{ $mata['visus_kanan'] ?? 'Plano 6/6' }}</td>
                    <td class="col-value">{{ $mata['visus_kiri'] ?? 'Plano 6/6' }}</td>
                </tr>
                <tr>
                    <td class="row-title">Addition (ADD)</td>
                    <td colspan="2" class="col-value" style="font-size: 11pt; color: #0369a1;">
                        {{ $mata['add'] ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="row-title">Pupillary Distance (PD)</td>
                    <td colspan="2" class="col-value" style="font-size: 11pt; color: #0369a1;">
                        {{ $mata['pd'] ?? '-' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">B. KESIMPULAN & TINDAK LANJUT</div>
        <div class="result-box">
            <strong>Kesimpulan Diagnosa Medis:</strong><br>
            <span style="display:inline-block; margin-top:5px; margin-bottom: 10px;">
                {{ $mataResult->kesimpulan ?? 'Belum ada kesimpulan medis yang dicatat.' }}
            </span>
            <br>
            <strong>Saran / Resep / Keterangan:</strong><br>
            <span style="display:inline-block; margin-top:5px;">
                {{ $mataResult->keterangan ?? 'Tidak ada catatan atau resep khusus.' }}
            </span>
        </div>
        
        <table class="signature-section">
            <tr>
                <td></td>
                <td>
                    <p style="margin: 0;">Pangkep, {{ now()->format('d F Y') }}</p>
                    <p style="margin: 0; color: #64748b;">Dokter Spesialis / Pemeriksa,</p>
                    <div class="sign-area"></div>
                    <p style="font-weight: bold; text-decoration: underline; color: #0f4a7b; margin:0; font-size: 11pt;">{{ $dokter->nama_lengkap ?? 'Nama Dokter' }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>