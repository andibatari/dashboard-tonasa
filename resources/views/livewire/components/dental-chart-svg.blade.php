<style>
    /* Desain Peta Gigi (Odontogram) Khusus PDF */
    .odontogram-wrapper {
        width: 100%;
        margin: 0 auto;
        border: 2px solid #0f4a7b; /* Bingkai luar tebal (Biru Tua) */
        border-radius: 4px;
        overflow: hidden;
        background-color: #ffffff;
    }
    
    .odonto-table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        table-layout: fixed;
    }
    
    .odonto-table td {
        border: 1px solid #cbd5e1; /* Garis grid tipis per gigi */
        padding: 8px 2px;
        width: 6.25%; /* 16 gigi per baris (100% / 16) */
        vertical-align: middle;
    }
    
    /* Garis Kuadran (Crosshair) Pembatas Tengah */
    .odonto-table .quadrant-line {
        border-right: 3px solid #0f4a7b; /* Garis vertikal pembatas Kanan / Kiri */
    }
    .odonto-table .jaw-line td {
        border-bottom: 3px solid #0f4a7b; /* Garis horizontal pembatas Atas / Bawah */
    }
    
    /* Kotak Gigi */
    .tooth-box {
        display: inline-block;
        width: 24px;
        height: 24px;
        line-height: 24px;
        border-radius: 4px;
        border: 1px solid #94a3b8; /* Border kotak gigi */
        background-color: #f8fafc;
        font-size: 8pt;
        font-weight: bold;
        color: #334155;
        margin: 0 auto;
        text-align: center;
    }
    
    /* Angka Nomor Gigi */
    .tooth-num {
        font-size: 7.5pt;
        color: #64748b;
        font-weight: bold;
    }
    .num-top { margin-bottom: 5px; }
    .num-bottom { margin-top: 5px; }
</style>

<div class="odontogram-wrapper">
    <table class="odonto-table">
        <!-- RAHANG ATAS (MAXILLARY) -->
        <tr class="jaw-line">
            {{-- Gabungan Kuadran 1 (Kanan) & Kuadran 2 (Kiri) --}}
            @foreach([18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28] as $t)
                <td class="{{ $t == 11 ? 'quadrant-line' : '' }}">
                    <div class="tooth-num num-top">{{ $t }}</div>
                    
                    {{-- Logika Simbol Huruf (O, X, T) berdasarkan Array gigiKlinis --}}
                    @php
                        $simbol = $t; // Default: Tampilkan Angka Gigi
                        if(isset($gigiKlinis[$t])) {
                            if($gigiKlinis[$t] == 'Missing') $simbol = 'X';
                            elseif($gigiKlinis[$t] == 'Tambal') $simbol = 'T';
                            elseif($gigiKlinis[$t] == 'Caries') $simbol = 'O';
                        }
                    @endphp
                    
                    {{-- ID wajib format 'gigi-xx' agar diwarnai oleh $dynamicCss dari Livewire --}}
                    <div id="gigi-{{ $t }}" class="tooth-box">{{ $simbol }}</div>
                </td>
            @endforeach
        </tr>

        <!-- RAHANG BAWAH (MANDIBULAR) -->
        <tr>
            {{-- Gabungan Kuadran 4 (Kanan) & Kuadran 3 (Kiri) --}}
            @foreach([48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38] as $t)
                <td class="{{ $t == 41 ? 'quadrant-line' : '' }}">
                    @php
                        $simbol = $t;
                        if(isset($gigiKlinis[$t])) {
                            if($gigiKlinis[$t] == 'Missing') $simbol = 'X';
                            elseif($gigiKlinis[$t] == 'Tambal') $simbol = 'T';
                            elseif($gigiKlinis[$t] == 'Caries') $simbol = 'O';
                        }
                    @endphp
                    
                    <div id="gigi-{{ $t }}" class="tooth-box">{{ $simbol }}</div>
                    <div class="tooth-num num-bottom">{{ $t }}</div>
                </td>
            @endforeach
        </tr>
    </table>
</div>