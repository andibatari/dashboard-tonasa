<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Fisik - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9.5pt; margin: 0; padding: 20px 30px; color: #334155; }
        .page { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* HEADER */
        .header { width: 100%; border-bottom: 3px solid #0f4a7b; padding-bottom: 10px; margin-bottom: 15px; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .logo { width: 65px; height: auto; }
        .header-text { text-align: center; }
        .header-main { font-size: 16pt; font-weight: 900; color: #0f4a7b; margin: 0; letter-spacing: 1px; }
        .header-sub { font-size: 11pt; font-weight: bold; color: #475569; margin: 3px 0; }
        
        .document-title { text-align: center; font-size: 12pt; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 15px; padding: 8px; background-color: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0; }

        /* INFO PASIEN */
        .patient-card { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .patient-card td { padding: 5px 8px; border: 1px solid #cbd5e1; font-size: 9pt; }
        .patient-card .label { background-color: #f8fafc; font-weight: bold; color: #475569; width: 15%; }
        .patient-card .value { font-weight: 500; color: #0f172a; width: 35%; }

        /* TABEL DATA LENGKAP */
        .section-title { font-size: 10.5pt; font-weight: bold; color: #ffffff; background-color: #0f4a7b; padding: 5px 10px; margin: 15px 0 5px 0; border-radius: 3px; }
        .sub-title { font-size: 9.5pt; font-weight: bold; color: #0f4a7b; background-color: #e0f2fe; padding: 4px 8px; margin-bottom: 5px; margin-top: 10px; border-left: 3px solid #0284c7; }
        
        .grid-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; font-size: 8.5pt; }
        .grid-table td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .grid-table .label-col { width: 22%; font-weight: bold; color: #64748b; }
        .grid-table .value-col { width: 28%; color: #1e293b; font-weight: bold; border-right: 1px solid #e2e8f0; }
        .grid-table .value-col-last { width: 28%; color: #1e293b; font-weight: bold; }
        .grid-table tr:last-child td { border-bottom: none; }

        /* TABEL PAJANAN */
        .pajanan-container { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .pajanan-container td { vertical-align: top; width: 50%; }
        .pajanan-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .pajanan-table td { padding: 3px 5px; border-bottom: 1px dotted #cbd5e1; }
        .pajanan-table .item-label { width: 75%; color: #475569; }
        .pajanan-table .item-value { width: 25%; font-weight: bold; color: #0f4a7b; text-align: right; }
        .pajanan-category-header { background-color: #e2e8f0; color: #0f172a; font-weight: bold; padding: 4px 5px !important; border-bottom: 1px solid #94a3b8 !important; }

        .result-box { background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 8px 12px; margin-bottom: 15px; font-size: 9pt; color: #14532d; }
        
        .signature-section { width: 100%; margin-top: 20px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-section td { width: 50%; text-align: center; vertical-align: bottom; }
        .sign-area { height: 60px; }
        
        .page-break-avoid { page-break-inside: avoid; }
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

        <div class="document-title">Laporan Hasil Pemeriksaan Fisik Lengkap</div>

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
                <td class="label">Perusahaan</td><td colspan="3" class="value">{{ $instansiPasien ?? 'N/A' }}</td>
            </tr>
        </table>

        @php 
            $df = $fisikResult->data_fisik ?? [];
            $anamnesa = $df['anamnesa'] ?? [];
            $tv = $df['tanda_vital'] ?? []; 
            $kepala = $df['kepala'] ?? []; 
            $leher = $df['leher'] ?? []; 
            $dada = $df['dada'] ?? []; 
            $paru = $df['paru'] ?? []; 
            $abdomen = $df['abdomen'] ?? []; 
            $eks = $df['ekstremitas'] ?? []; 
        @endphp

        <div class="page-break-avoid">
            <div class="section-title">1. ANAMNESA</div>
            <table class="grid-table">
                <tr>
                    <td class="label-col">Keluhan Utama</td>
                    <td class="value-col" colspan="3">: {{ $anamnesa['keluhan_utama'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Riwayat Kesehatan</td>
                    <td class="value-col">: {{ ($anamnesa['riwayat_kesehatan'] ?? '') === 'Lainnya' ? ($anamnesa['riwayat_kesehatan_lainnya'] ?? '-') : ($anamnesa['riwayat_kesehatan'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Riwayat Penyakit Keluarga</td>
                    <td class="value-col-last">: {{ ($anamnesa['riwayat_penyakit_keluarga'] ?? '') === 'Lainnya' ? ($anamnesa['riwayat_penyakit_keluarga_lainnya'] ?? '-') : ($anamnesa['riwayat_penyakit_keluarga'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Merokok</td>
                    <td class="value-col">: {{ $anamnesa['merokok'] ?? '-' }} {{ ($anamnesa['merokok'] ?? '') === 'Ya' ? '('.($anamnesa['merokok_jumlah'] ?? '-').' btg/hari)' : '' }}</td>
                    <td class="label-col" style="padding-left:10px;">Minum Alkohol</td>
                    <td class="value-col-last">: {{ $anamnesa['minum_alkohol'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Olahraga</td>
                    <td class="value-col" colspan="3">: {{ $anamnesa['olahraga'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="page-break-avoid">
            <div class="section-title">2. TANDA VITAL & ANTROPOMETRI</div>
            <table class="grid-table" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                <tr>
                    <td class="label-col">Tinggi Badan</td><td class="value-col">: {{ $tv['tinggi_badan'] ?? '-' }} cm</td>
                    <td class="label-col" style="padding-left:10px;">Tekanan Sistol</td><td class="value-col-last">: {{ $tv['tekanan_darah_sistol'] ?? '-' }} mmHg</td>
                </tr>
                <tr>
                    <td class="label-col">Berat Badan</td><td class="value-col">: {{ $tv['berat_badan'] ?? '-' }} kg</td>
                    <td class="label-col" style="padding-left:10px;">Tekanan Diastol</td><td class="value-col-last">: {{ $tv['tekanan_darah_diastol'] ?? '-' }} mmHg</td>
                </tr>
                <tr>
                    <td class="label-col">BMI (Kategori)</td><td class="value-col">: {{ $tv['bmi'] ?? '-' }} ({{ $tv['kategori_bmi'] ?? '-' }})</td>
                    <td class="label-col" style="padding-left:10px;">Nadi / Nafas</td><td class="value-col-last">: {{ $tv['nadi'] ?? '-' }} x/mnt / {{ $tv['pernafasan'] ?? '-' }} x/mnt</td>
                </tr>
                <tr>
                    <td class="label-col">Suhu Tubuh</td><td class="value-col">: {{ $tv['suhu'] ?? '-' }} °C</td>
                    <td class="label-col" style="padding-left:10px;">SpO2 (Oksigen)</td><td class="value-col-last">: {{ $tv['spo2'] ?? '-' }} %</td>
                </tr>
            </table>
        </div>

        <div class="section-title">3. PEMERIKSAAN FISIK PER SISTEM</div>
        <div class="page-break-avoid">
            <div class="sub-title">A. Area Kepala & Leher</div>
            <table class="grid-table">
                <tr>
                    <td class="label-col">Anemi</td><td class="value-col">: {{ ($kepala['anemi'] ?? '') === 'Lainnya' ? ($kepala['anemi_lainnya'] ?? '-') : ($kepala['anemi'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Ikterus</td><td class="value-col-last">: {{ ($kepala['ikterus'] ?? '') === 'Lainnya' ? ($kepala['ikterus_lainnya'] ?? '-') : ($kepala['ikterus'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Dyspnoe</td><td class="value-col">: {{ ($kepala['dyspnoe'] ?? '') === 'Lainnya' ? ($kepala['dyspnoe_lainnya'] ?? '-') : ($kepala['dyspnoe'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Cyanosis</td><td class="value-col-last">: {{ ($kepala['cyanosis'] ?? '') === 'Lainnya' ? ($kepala['cyanosis_lainnya'] ?? '-') : ($kepala['cyanosis'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Refleks Pupil</td><td class="value-col">: {{ ($kepala['refleks_pupil'] ?? '') === 'Lainnya' ? ($kepala['refleks_pupil_lainnya'] ?? '-') : ($kepala['refleks_pupil'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Hidung</td><td class="value-col-last">: {{ ($kepala['hidung'] ?? '') === 'Lainnya' ? ($kepala['hidung_lainnya'] ?? '-') : ($kepala['hidung'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tonsil Kanan / Kiri</td><td class="value-col">: {{ ($kepala['tonsil_kanan'] ?? '') === 'Lainnya' ? ($kepala['tonsil_kanan_lainnya'] ?? '-') : ($kepala['tonsil_kanan'] ?? '-') }} / {{ ($kepala['tonsil_kiri'] ?? '') === 'Lainnya' ? ($kepala['tonsil_kiri_lainnya'] ?? '-') : ($kepala['tonsil_kiri'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Serumen</td><td class="value-col-last">: {{ ($kepala['serumen'] ?? '') === 'Lainnya' ? ($kepala['serumen_lainnya'] ?? '-') : ($kepala['serumen'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Membran Timpani</td><td class="value-col">: {{ ($kepala['membran_timpani'] ?? '') === 'Lainnya' ? ($kepala['membran_timpani_lainnya'] ?? '-') : ($kepala['membran_timpani'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">JVP (Leher)</td><td class="value-col-last">: {{ ($leher['jvp'] ?? '') === 'Lainnya' ? ($leher['jvp_lainnya'] ?? '-') : ($leher['jvp'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tiroid</td><td class="value-col">: {{ ($leher['tiroid'] ?? '') === 'Lainnya' ? ($leher['tiroid_lainnya'] ?? '-') : ($leher['tiroid'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">KGB</td><td class="value-col-last">: {{ ($leher['kelenjar_getah_bening'] ?? '') === 'Lainnya' ? ($leher['kelenjar_getah_bening_lainnya'] ?? '-') : ($leher['kelenjar_getah_bening'] ?? '-') }}</td>
                </tr>
            </table>
        </div>

        <div class="page-break-avoid">
            <div class="sub-title">B. Area Dada, Paru & Abdomen</div>
            <table class="grid-table">
                <tr>
                    <td class="label-col">Bunyi Jantung I</td>
                    <td class="value-col">: {{ ($dada['bunyi_jantung_1_a'] ?? '') === 'Lainnya' ? ($dada['bunyi_jantung_1_a_lainnya'] ?? '-') : ($dada['bunyi_jantung_1_a'] ?? '-') }}, 
                        {{ ($dada['bunyi_jantung_1_b'] ?? '') === 'Lainnya' ? ($dada['bunyi_jantung_1_b_lainnya'] ?? '-') : ($dada['bunyi_jantung_1_b'] ?? '-') }}
                    </td>
                    <td class="label-col" style="padding-left:10px;">Bunyi Jantung II</td>
                    <td class="value-col-last">: {{ ($dada['bunyi_jantung_2_a'] ?? '') === 'Lainnya' ? ($dada['bunyi_jantung_2_a_lainnya'] ?? '-') : ($dada['bunyi_jantung_2_a'] ?? '-') }}, 
                        {{ ($dada['bunyi_jantung_2_b'] ?? '') === 'Lainnya' ? ($dada['bunyi_jantung_2_b_lainnya'] ?? '-') : ($dada['bunyi_jantung_2_b'] ?? '-') }}
                    </td>
                </tr>
                <tr>
                    <td class="label-col">Bunyi Nafas Dasar</td><td class="value-col">: {{ ($paru['bunyi_nafas'] ?? '') === 'Lainnya' ? ($paru['bunyi_nafas_lainnya'] ?? '-') : ($paru['bunyi_nafas'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Bunyi Tambahan</td><td class="value-col-last">: {{ ($paru['bunyi_nafas_tambahan'] ?? '') === 'Lainnya' ? ($paru['bunyi_nafas_tambahan_lainnya'] ?? '-') : ($paru['bunyi_nafas_tambahan'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Peristaltik</td><td class="value-col">: {{ ($abdomen['peristaltik'] ?? '') === 'Lainnya' ? ($abdomen['peristaltik_lainnya'] ?? '-') : ($abdomen['peristaltik'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Nyeri Tekan</td><td class="value-col-last">: {{ ($abdomen['nyeri_tekan'] ?? '') === 'Lainnya' ? ($abdomen['nyeri_tekan_lainnya'] ?? '-') : ($abdomen['nyeri_tekan'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Hati</td><td class="value-col">: {{ ($abdomen['hati'] ?? '') === 'Lainnya' ? ($abdomen['hati_lainnya'] ?? '-') : ($abdomen['hati'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Limpa / Massa</td><td class="value-col-last">: {{ ($abdomen['limpa'] ?? '') === 'Lainnya' ? ($abdomen['limpa_lainnya'] ?? '-') : ($abdomen['limpa'] ?? '-') }} / {{ ($abdomen['massa'] ?? '') === 'Lainnya' ? ($abdomen['massa_lainnya'] ?? '-') : ($abdomen['massa'] ?? '-') }}</td>
                </tr>
            </table>
        </div>

        <div class="page-break-avoid">
            <div class="sub-title">C. Ekstremitas & Refleks</div>
            <table class="grid-table">
                <tr>
                    <td class="label-col">Kondisi Ekstremitas</td><td class="value-col" colspan="3">: {{ ($eks['ekstremitas'] ?? '') === 'Lainnya' ? ($eks['ekstremitas_lainnya'] ?? '-') : ($eks['ekstremitas'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Refleks Fis (Ka/Ki)</td><td class="value-col">: {{ ($eks['refleks_fisiologis_kanan'] ?? '') === 'Lainnya' ? ($eks['refleks_fisiologis_kanan_lainnya'] ?? '-') : ($eks['refleks_fisiologis_kanan'] ?? '-') }} / {{ ($eks['refleks_fisiologis_kiri'] ?? '') === 'Lainnya' ? ($eks['refleks_fisiologis_kiri_lainnya'] ?? '-') : ($eks['refleks_fisiologis_kiri'] ?? '-') }}</td>
                    <td class="label-col" style="padding-left:10px;">Refleks Pat (Ka/Ki)</td><td class="value-col-last">: {{ ($eks['refleks_patologis_kanan'] ?? '') === 'Lainnya' ? ($eks['refleks_patologis_kanan_lainnya'] ?? '-') : ($eks['refleks_patologis_kanan'] ?? '-') }} / {{ ($eks['refleks_patologis_kiri'] ?? '') === 'Lainnya' ? ($eks['refleks_patologis_kiri_lainnya'] ?? '-') : ($eks['refleks_patologis_kiri'] ?? '-') }}</td>
                </tr>
            </table>
        </div>

        <div class="page-break-avoid">
            <div class="section-title">4. RIWAYAT PAJANAN PEKERJAAN (HAZARD)</div>
            <table class="pajanan-container">
                <tr>
                    <td style="padding-right: 10px;">
                        <table class="pajanan-table">
                            <tr><td colspan="2" class="pajanan-category-header">A. BAHAYA FISIK</td></tr>
                            @foreach ($pajanan['fisik'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                            
                            <tr><td colspan="2" class="pajanan-category-header" style="border-top: 15px solid white;">B. BAHAYA KIMIA</td></tr>
                            @foreach ($pajanan['kimia'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                        </table>
                    </td>
                    <td style="padding-left: 10px;">
                        <table class="pajanan-table">
                            <tr><td colspan="2" class="pajanan-category-header">C. BAHAYA BIOLOGI</td></tr>
                            @foreach ($pajanan['biologi'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                            
                            <tr><td colspan="2" class="pajanan-category-header" style="border-top: 15px solid white;">D. BAHAYA PSIKOLOGI</td></tr>
                            @foreach ($pajanan['psikologi'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                            
                            <tr><td colspan="2" class="pajanan-category-header" style="border-top: 15px solid white;">E. BAHAYA ERGONOMIS</td></tr>
                            @foreach ($pajanan['ergonomis'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="page-break-avoid">
            <div class="section-title">5. KESIMPULAN & SARAN</div>
            <div class="result-box">
                <strong>Kesimpulan Diagnosa:</strong><br>
                {{ $fisikResult->kesimpulan ?? 'Belum ada kesimpulan medis yang dicatat.' }}
                <br><br>
                <strong>Tindak Lanjut / Keterangan Tambahan:</strong><br>
                {{ $fisikResult->keterangan ?? 'Tidak ada keterangan tambahan.' }}
            </div>
            
            <table class="signature-section">
                <tr>
                    <td></td>
                    <td>
                        <p style="margin: 0;">Pangkep, {{ now()->format('d F Y') }}</p>
                        <p style="margin: 0; color: #64748b;">Dokter Pemeriksa,</p>
                        <div class="sign-area"></div>
                        <p style="font-weight: bold; text-decoration: underline; color: #0f4a7b; margin:0;">{{ $dokter->nama_lengkap ?? 'Nama Dokter' }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>