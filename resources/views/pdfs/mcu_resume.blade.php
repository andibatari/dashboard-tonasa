<!DOCTYPE html>
<html>
<head>
    <title>Resume MCU</title>
    {{-- Memastikan encoding dokumen terset ke UTF-8 agar penulisan simbol karakter tidak rusak --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* Mengatur font standar sans-serif ukuran 10pt dengan batas margin cetak kertas agar padat (compact) */
        body { font-family: sans-serif; font-size: 10pt; margin: 0.3in 0.4in; color: #1e293b; }
        
        /* Layouting tabel header instansi menggunakan display table standar agar kompatibel penuh dengan DomPDF */
        .header-container { width: 100%; display: table; border-bottom: 2px solid #000; }
        .header-cell { display: table-cell; vertical-align: middle; padding-bottom: 5px; }
        .header-left { width: 30%; text-align: left; }
        .header-center { width: 80%; text-align: left; padding-left: 0; line-height: 1.2; }
        
        /* Penataan ukuran gambar logo kop surat */
        .header-logo { height: 50px; width: auto; margin-right: 5px; }
        .text-title { font-size: 18pt; margin: 0; font-weight: 900;}
        .text-subtitle { font-size: 16pt; margin: 0; text-decoration: underline; font-weight: bold;}
        .text-priority { font-size: 14pt; margin: 0; color: #cc3333; font-weight: bold; }
        
        /* Grid data demografi pasien rekam medis */
        .data-pasien { margin-top: 7px; margin-bottom: 7px; }
        .data-pasien table { width: 100%; border-collapse: collapse; }
        .data-pasien td { padding: 1px 0; vertical-align: top; font-size: 10pt; }
        .data-pasien .label-col { width: 18%; padding-right: 5px; font-weight: bold;}
        .data-pasien .colon-col { width: 1%; }
        .data-pasien .value-col { width: 37%; }

        /* Blok paragraf kata pengantar surat kelayakan kerja */
        .greeting { margin-top: 8px; margin-bottom: 8px; line-height: 1.3; text-align: justify; }
        .greeting strong { font-size: 10.5pt; color: #000; display: inline-block; margin-bottom: 2px; }

        /* Blok seksi kontainer per butir indikator rekam medis */
        .content-section { margin-top: 10px; }
        .content-section h4 { margin: 0 0 2px 0; font-size: 11pt; border-bottom: 1px solid #000; padding-bottom: 2px; }
        
        /* Desain list butir rekomendasi saran medis */
        .saran-list { margin: 2px 0 2px 0; padding-left: 15px; list-style-type: disc; }
        .saran-list li { margin-bottom: 2px; line-height: 1.3; font-size: 10pt;}
        
        .pre-wrap { white-space: pre-wrap; margin: 0; }
    </style>
</head>
<body>

    {{-- KOP SURAT / HEADER KLINIK STMC --}}
    <div class="header-container">
        <div class="header-cell header-left">
            @if(!empty($setting_logo_stmc))
                <img class="header-logo" src="{{ $setting_logo_stmc }}" alt="Logo SIG">
            @endif
            @if(!empty($setting_logo_tonasa))
                <img class="header-logo" src="{{ $setting_logo_tonasa }}" alt="Logo Tonasa">
            @endif
        </div>
        <div class="header-cell header-center">
            <p class="text-title">MEDICAL CHECK UP</p>
            <p class="text-subtitle">KLINIK SEMEN TONASA MEDICAL CENTRE</p>
            <p class="text-priority">YOUR HEALTH, OUR PRIORITY</p>
        </div>
    </div>
    <hr style="border: none; border-bottom: 0.5px solid #000; margin-top: 0; margin-bottom: 6px;">    

    {{-- BIODATA IDENTITAS PASIEN --}}
    <div class="data-pasien">
        <table>
            <tr><td colspan="6" style="padding-bottom: 3px;">Kepada Yth.</td></tr>
            <tr>
                <td class="label-col">Nama</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['nama'] ?? '-' }}</td>
                <td class="label-col">Tgl. Lahir</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['tgl_lahir'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Alamat</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['alamat'] ?? '-' }}</td>
                <td class="label-col">Jenis Kelamin</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['jenis_kelamin'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">NIK / SAP</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['nik_sap'] ?? '-' }}</td>
                <td class="label-col">Paket MCU</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['paket_mcu'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Unit Kerja</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['unit_kerja'] ?? '-' }}</td>
                <td></td><td></td><td></td>
            </tr>
        </table>
    </div>

    {{-- PARAGRAF SURAT PENGANTAR (DISCLAIMER) --}}
    <div class="greeting">
        <strong>Dengan Hormat,</strong><br>
        {{-- PENGAMAN DISCLAIMER --}}
        {!! $setting_disclaimer ?? 'Berikut adalah hasil pemeriksaan kesehatan Medical Check Up (MCU) Anda:' !!}
    </div>

    {{-- PEMBENTUKAN STRING BUTIR REKAM MEDIS --}}
    @php
    $resumeData = (!empty($resume_body_raw) && is_string($resume_body_raw)) ? json_decode($resume_body_raw, true) : null; 
    $resumeText = '';
    
    if ($resumeData) {
        $resumeMap = [
            'bmi' => 'BMI', 'laboratorium' => 'Hasil Laboratorium', 'ekg' => 'Hasil Pemeriksaan EKG / Rekam Jantung',
            'gigi' => 'Hasil Pemeriksaan Gigi', 'mata' => 'Hasil Pemeriksaan Mata', 'spirometri' => 'Hasil Pemeriksaan Spirometri',
            'audiometri' => 'Hasil Pemeriksaan Audiometri', 'kebugaran' => 'Hasil Pemeriksaan Kesegaran Jasmani', 'thorax' => 'Hasil Pemeriksaan Thorax Photo',
            'treadmill' => 'Hasil Treadmill', 'usg' => 'Hasil USG','temuan_lain' => 'Temuan lain',
        ];

        $i = 1;
        foreach ($resumeMap as $key => $label) {
            $value = $resumeData[$key] ?? '—'; 
            $resumeText .= "<p style='margin:0; padding-left: 15px; text-indent: -15px; line-height: 1.3;'>{$i}. {$label} : <b>{$value}</b></p>";
            $i++;
        }
    }
    
    $saranArray = [];
    if (!empty($resume_saran)) {
        $saranTemp = preg_split("/[\r\n,]+/", $resume_saran, -1, PREG_SPLIT_NO_EMPTY);
        $saranArray = array_map('trim', $saranTemp);
        $saranArray = array_filter($saranArray); 
    }
    @endphp

    {{-- OUTPUT HASIL PEMERIKSAAN MEDIS --}}
    <div class="content-section">
        @if ($resumeText)
            <div style="font-size: 10pt;">{!! $resumeText !!}</div> 
        @else
            <pre class="pre-wrap" style="font-size: 10pt;">Data hasil pemeriksaan (Butir 1-9) belum diisi.</pre>
        @endif
    </div>
    
    {{-- OUTPUT SARAN MEDICAL DOKTER --}}
    <div class="content-section">
        <h4>Saran:</h4>
        @if (!empty($saranArray))
            <ul class="saran-list">
                @foreach ($saranArray as $saran)
                    <li>{{ $saran }}</li>
                @endforeach
            </ul>
        @else
            <p style="margin: 2px 0 0 15px; font-size: 10pt;">Saran dokter belum diisi.</p>
        @endif
    </div>
    
    {{-- OUTPUT KATEGORI KELAYAKAN AKHIR --}}
    <div class="content-section" style="margin-top: 10px;">
        <h4 style="border: none;">Kategori Akhir:</h4>
        <p style="font-weight: 900; font-size: 12pt; margin: 0;">{{ $resume_kategori ?? 'Belum Ditentukan' }}</p>
    </div>
    
    {{-- TANDA TANGAN GANDA COMPACT DENGAN INTEGRASI QR VALIDATION SEAL --}}
    <div class="content-section" style="page-break-inside: avoid; margin-top: 15px;">
        <table style="width: 100%; text-align: center; border-collapse: collapse; font-size: 10pt; table-layout: fixed;">
            <tr>
                <td style="width: 35%;"></td>
                <td style="width: 30%;"></td>
                <td style="width: 35%; padding-bottom: 5px; font-size: 9.5pt; text-align: right;">Pangkep, {{ $tanggal_cetak ?? date('d M Y') }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top; text-align: left; padding-left: 10px;">Mengetahui,<br>Kepala Klinik STMC,</td>
                <td style="vertical-align: middle; text-align: center;">
                    {{-- PENGAMAN QR CODE --}}
                    @if(!empty($qrCodeBase64))
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Segel Elektronik STMC" style="width: 100px; height: 100px;"><br>
                        <span style="font-size: 8pt; font-weight: bold; color: #475569; font-family: monospace;">E-SIGN VERIFIED</span>
                    @else
                        <br><br><br>
                    @endif
                </td>
                <td style="vertical-align: top; text-align: right; padding-right: 10px;">Dokter Pemeriksa,</td>
            </tr>
            <tr>
                <td style="height: 10px;"></td>
                <td style="height: 10px;"></td>
                <td style="height: 10px;"></td>
            </tr>
            <tr>
                <td style="vertical-align: top; text-align: left; padding-left: 10px;">
                    <span style="font-weight: bold; text-decoration: underline;">{{ $setting_kepala_klinik ?? 'dr. (Kepala Klinik)' }}</span>
                </td>
                <td></td>
                <td style="vertical-align: top; text-align: right; padding-right: 10px;">
                    <span style="font-weight: bold; text-decoration: underline;">{{ $doctor_data['nama'] ?? '(Nama Dokter)' }}</span><br>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>