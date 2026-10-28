<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Gigi - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; margin: 0; padding: 20px; color: #334155; }
        .page { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* HEADER PREMIUM */
        .header { width: 100%; border-bottom: 3px solid #0f4a7b; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .logo { width: 70px; height: auto; }
        .header-text { text-align: center; }
        .header-main { font-size: 18pt; font-weight: 900; color: #0f4a7b; margin: 0; letter-spacing: 1px; }
        .header-sub { font-size: 12pt; font-weight: bold; color: #475569; margin: 4px 0; }
        .header-address { font-size: 9pt; color: #64748b; margin: 0; }

        /* JUDUL DOKUMEN */
        .document-title { text-align: center; font-size: 14pt; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 20px; padding: 10px; background-color: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0; }

        /* INFO PASIEN (CARD STYLE) */
        .patient-card { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .patient-card td { padding: 6px 10px; border: 1px solid #cbd5e1; font-size: 9.5pt; }
        .patient-card .label { background-color: #f8fafc; font-weight: bold; color: #475569; width: 15%; }
        .patient-card .value { font-weight: 500; color: #0f172a; width: 35%; }
        
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 8pt; font-weight: bold; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        /* TABEL PEMERIKSAAN */
        .section-title { font-size: 11pt; font-weight: bold; color: #ffffff; background-color: #0f4a7b; padding: 6px 10px; margin: 0 0 10px 0; border-radius: 3px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .data-table .col-label { width: 35%; font-weight: bold; color: #64748b; }
        .data-table .col-value { width: 65%; color: #1e293b; font-weight: 500; }

        /* ODONTOGRAM & LEGENDA (KETERANGAN) */
        .dental-chart-container { border: 2px solid #e2e8f0; padding: 15px; border-radius: 6px; text-align: center; margin-bottom: 20px; background-color: #ffffff; }
        .chart-description { font-size: 9pt; color: #64748b; margin-top: 0; margin-bottom: 15px; font-style: italic; }
        
        .legend-table { width: 100%; margin-top: 20px; border-collapse: collapse; font-size: 9pt; background-color: #f8fafc; border: 1px solid #e2e8f0; }
        .legend-table td { text-align: center; vertical-align: middle; padding: 8px 5px; width: 25%; border-right: 1px solid #e2e8f0; }
        .legend-table td:last-child { border-right: none; }
        
        .legend-color { display: inline-block; width: 18px; height: 18px; border-radius: 4px; border: 1px solid #94a3b8; margin-right: 6px; vertical-align: middle; text-align: center; line-height: 18px; font-weight: bold; font-size: 10px; }
        .color-normal { background-color: #E5E7EB; }
        .color-caries { background-color: #DC2626; border-color: #B91C1C; color: #ffffff; }
        .color-tambal { background-color: #10B981; border-color: #059669; color: #ffffff; }
        .color-missing { background-color: #6B7280; border-color: #4B5563; color: #ffffff; }
        
        .legend-text { vertical-align: middle; color: #1e293b; font-weight: bold; }

        /* KESIMPULAN BOX */
        .result-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 10px 15px; margin-bottom: 20px; font-size: 10pt; color: #78350f; }

        /* TANDA TANGAN */
        .signature-section { width: 100%; margin-top: 40px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-section td { width: 50%; text-align: center; vertical-align: bottom; }
        .sign-area { height: 70px; }
        .doctor-name { font-weight: bold; text-decoration: underline; color: #0f4a7b; }

        /* CSS Dinamis Livewire (Menyesuaikan warna pada SVG) */
        {{ $dynamicCss }}
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <table class="header">
            <tr>
                <td style="width: 15%; text-align: left;"><img src="{{ public_path('images/logo-semen-tonasa.png') }}" class="logo"></td>
                <td style="width: 70%;" class="header-text">
                    <p class="header-main">SEMEN TONASA MEDICAL CENTRE</p>
                    <p class="header-sub">KLINIK UTAMA PT SEMEN TONASA</p>
                    <p class="header-address">Jl. Jend. Sudirman No. 1, Kab. Pangkep, Sulawesi Selatan</p>
                </td>
                <td style="width: 15%; text-align: right;"><img src="{{ public_path('images/logo-stmc.png') }}" class="logo"></td>
            </tr>
        </table>

        <div class="document-title">Laporan Hasil Pemeriksaan Poli Gigi</div>

        <!-- INFO PASIEN -->
        <div style="margin-bottom: 8px;">
            <span class="badge">@if ($isKaryawan) PASIEN KARYAWAN @else PASIEN UMUM / NON-KARYAWAN @endif</span>
        </div>
        <table class="patient-card">
            <tr>
                <td class="label">Nama Pasien</td><td class="value">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</td>
                <td class="label">NIK / SAP</td><td class="value">{{ $patient->nik_pasien ?? $patient->nik_karyawan ?? 'N/A' }} / {{ $patient->no_sap ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Umur / Kelamin</td><td class="value">{{ $patient->tanggal_lahir ? \Carbon\Carbon::parse($patient->tanggal_lahir)->age . ' Tahun' : '-' }} / {{ $patient->jenis_kelamin ?? 'N/A' }}</td>
                <td class="label">Tgl. Periksa</td><td class="value">{{ now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Perusahaan</td><td colspan="3" class="value">{{ $instansiPasien ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- EKSTRA ORAL -->
        <div class="section-title">1. PEMERIKSAAN EKSTRA ORAL</div>
        <table class="data-table">
            <tr>
                <td class="col-label">Kelenjar Submandibular</td><td class="col-value">: {{ $ekstraOral['kelenjar_submandibular'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="col-label">Kelenjar Leher</td><td class="col-value">: {{ $ekstraOral['kelenjar_leher'] ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- INTRA ORAL -->
        <div class="section-title">2. PEMERIKSAAN INTRA ORAL</div>
        <table class="data-table">
            {{-- 🌟 PERBAIKAN: Hanya ubah daftar kata di dalam kurung siku ini sesuai form baru --}}
            @foreach (['mukosa_pipi', 'palatum', 'mukosa_mulut', 'lidah', 'ginggiva_ra', 'ginggiva_rb', 'karang_gigi', 'pocket'] as $key)
            <tr>
                <td class="col-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                <td class="col-value">: {{ $intraOral[$key] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </table>

        <!-- ODONTOGRAM -->
        <div style="page-break-inside: avoid;">
            <div class="section-title">3. ODONTOGRAM (PETA GIGI)</div>
            <div class="dental-chart-container">
                <p class="chart-description">Diagram di bawah ini memetakan posisi dan kondisi klinis gigi pasien saat ini.</p>
                
                <!-- SVG Gambar Peta Gigi -->
                <div style="display: block; margin: 0 auto;">
                    @include('livewire.components.dental-chart-svg')
                </div>

                <!-- Legenda / Keterangan Gambar untuk Orang Awam -->
                <table class="legend-table">
                    <tr>
                        <td>
                            <span class="legend-color color-normal"></span>
                            <span class="legend-text">Sehat (Normal)</span>
                        </td>
                        <td>
                            <span class="legend-color color-caries"></span>
                            <span class="legend-text">Berlubang (Caries)</span>
                        </td>
                        <td>
                            <span class="legend-color color-tambal"></span>
                            <span class="legend-text">Ditambal</span>
                        </td>
                        <td>
                            <span class="legend-color color-missing">X</span>
                            <span class="legend-text">Hilang / Cabut</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- KESIMPULAN -->
        <div style="page-break-inside: avoid;">
            <div class="section-title">4. KESIMPULAN & SARAN TINDAK LANJUT</div>
            <div class="result-box">
                <strong>Kesimpulan Klinis:</strong><br>
                {{ $kesimpulan ?? 'Belum ada kesimpulan medis yang dicatat.' }}
                <br><br>
                <strong>Keterangan / Saran Perawatan:</strong><br>
                {{ $keterangan ?? 'Tidak ada keterangan tambahan.' }}
            </div>
            
            <!-- TANDA TANGAN -->
            <table class="signature-section">
                <tr>
                    <td></td>
                    <td>
                        <p style="margin: 0;">Pangkep, {{ now()->format('d F Y') }}</p>
                        <p style="margin: 0; color: #64748b;">Dokter Pemeriksa,</p>
                        <div class="sign-area"></div>
                        <p class="doctor-name">{{ $dokter->nama_lengkap ?? 'Nama Dokter' }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>