<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kebugaran</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; font-size: 11pt; color: #000; }
        
        /* HEADER TEAL/HIJAU TOSCA */
        .header-bg { background-color: #009688; padding: 25px 30px; border-bottom: 5px solid #000; color: #000; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .logo-cell { width: 60px; vertical-align: middle;}
        .logo { height: 45px; }
        .title-cell { padding-left: 10px; vertical-align: middle; }
        .title-main { font-size: 13pt; font-weight: 900; margin: 0; }
        .title-sub { font-size: 10pt; font-weight: bold; margin: 2px 0; text-decoration: underline; }
        .title-tag { font-size: 9pt; font-weight: bold; color: #cc0000; margin: 0; }
        
        .info-table { width: 100%; border-collapse: collapse; font-size: 11pt; font-weight: bold; }
        .info-table td { padding: 3px 0; }
        .info-label { text-decoration: underline; width: 80px; }
        .info-colon { width: 20px; text-align: center; }
        .info-val { text-decoration: underline; }

        /* BODY CONTENT */
        .content { padding: 40px 50px; }
        .main-heading { text-align: center; font-size: 16pt; font-weight: 900; text-decoration: underline; margin-bottom: 40px; }
        
        .sub-heading { font-size: 12pt; font-weight: 900; text-decoration: underline; margin-bottom: 20px; }
        
        /* RESULT LIST */
        .result-table { width: 100%; border-collapse: collapse; font-size: 11pt; margin-bottom: 40px; }
        .result-table td { padding: 8px 0; vertical-align: top; }
        .col-bullet { width: 5%; font-weight: bold; text-align: center; }
        .col-label { width: 50%; }
        .col-colon { width: 5%; text-align: center; }
        .col-val { width: 10%; text-align: right; }
        .col-unit { width: 30%; padding-left: 15px; }

        /* CATEGORY SECTION */
        .cat-heading { font-size: 12pt; font-weight: 900; font-style: italic; text-decoration: underline; margin-bottom: 15px; }
        .cat-value { font-size: 14pt; font-weight: 900; }
    </style>
</head>
<body>
    <div class="header-bg">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if(!empty($logoTonasaBase64))
                        <img src="{{ $logoTonasaBase64 }}" class="logo">
                    @endif
                </td>
                <td class="logo-cell">
                    @if(!empty($logoStmcBase64))
                        <img src="{{ $logoStmcBase64 }}" class="logo">
                    @endif
                </td>
                <td class="title-cell">
                    <p class="title-main">MEDICAL CHECK UP</p>
                    <p class="title-sub">KLINIK SEMEN TONASA MEDICAL CENTRE</p>
                    <p class="title-tag">YOUR HEALTH, OUR PRIORITY</p>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="info-label">Tanggal</td>
                <td class="info-colon">:</td>
                {{-- 🌟 PERBAIKAN: Format tanggal ISO diubah agar menampilkan Tanggal Bulan(Teks) Tahun --}}
                <td class="info-val">{{ \Carbon\Carbon::parse($kebugaranResult->created_at ?? now())->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Nama</td>
                <td class="info-colon">:</td>
                <td class="info-val">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">NIK/SAP</td>
                <td class="info-colon">:</td>
                <td class="info-val">{{ $patient->nik_pasien ?? $patient->no_sap ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <div class="main-heading">PEMERIKSAAN KESEGARAN JASMANI</div>

        <div class="sub-heading">HASIL PEMERIKSAAN</div>
        
        <table class="result-table">
            <tr>
                <td class="col-bullet">*</td>
                <td class="col-label">Lama pemeriksaan</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ $kebugaranResult->durasi_menit }}</td>
                <td class="col-unit">Menit</td>
            </tr>
            <tr>
                <td class="col-bullet">*</td>
                <td class="col-label">Beban Latihan</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ $kebugaranResult->beban_latihan }}</td>
                <td class="col-unit">Watt</td>
            </tr>
            <tr>
                <td class="col-bullet">*</td>
                <td class="col-label">Jumlah denyut nadi selama 1 ( Satu ) menit terakhir</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ $kebugaranResult->denyut_nadi }}</td>
                <td class="col-unit">x/menit</td>
            </tr>
            <tr>
                <td class="col-bullet">*</td>
                <td class="col-label">Kebutuhan VO2 Maksimal</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ number_format($kebugaranResult->vo2_max, 2, ',', '.') }}</td>
                <td class="col-unit">ml/kg/ menit</td>
            </tr>
        </table>

        <div class="cat-heading">KATEGORI KESEGARAN JASMANI</div>
        <div class="cat-value">
            {{-- 🌟 PERBAIKAN: Simbol ≈ diganti dengan = agar kompatibel dengan font DomPDF --}}
            VO<sub>2</sub>max = {{ number_format($kebugaranResult->vo2_max, 2, ',', '.') }} ml/kg/menit ({{ $kebugaranResult->kategori }})
        </div>
    </div>
</body>
</html>