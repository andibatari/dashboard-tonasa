@extends('layouts.app') 

@section('content')

    <div class="bg-gray-100 min-h-screen p-4 lg:p-6">
        {{-- max-w-7xl akan mencegah layar terlalu melar di monitor PC yang lebar --}}
        <div class="container mx-auto max-w-7xl"> 
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-6 lg:mb-8">Dashboard Admin</h1>

            <div class="mb-2 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-700">Operasional Klinik (Hari Ini)</h2>
            </div>
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
                {{-- KARTU: Pasien Hari Ini --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between transition-transform hover:-translate-y-1">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Pasien Hadir</p>
                        <p class="text-3xl font-black text-indigo-700">{{$totalPasienHariIni}}</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <svg class="h-7 w-7 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                </div>

                {{-- KARTU: Menunggu Resume --}}
                <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-5 flex items-center justify-between transition-transform hover:-translate-y-1">
                    <div>
                        <p class="text-sm font-medium text-red-600 mb-1">Menunggu Resume Dokter</p>
                        <p class="text-3xl font-black text-red-800">{{ $pasienMenungguResume }} <span class="text-sm font-normal text-red-600">Pasien</span></p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                {{-- KARTU: Resume Selesai --}}
                <div class="bg-green-50 rounded-xl shadow-sm border border-green-200 p-5 flex items-center justify-between transition-transform hover:-translate-y-1">
                    <div>
                        <p class="text-sm font-medium text-green-600 mb-1">Resume Selesai</p>
                        <p class="text-3xl font-black text-green-800">{{ $resumeSelesaiHariIni }} <span class="text-sm font-normal text-green-600">Selesai</span></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        {{-- Ikon Clipboard Check yang sudah diperbaiki --}}
                        <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex items-center justify-between mt-8">
                <h2 class="text-lg font-bold text-gray-700">Analitik & Tren Karyawan</h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                {{-- Grafik Kiri: Kelayakan Kerja (Donut dipindahkan ke sini agar kecil & ramping) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 border-t-4 border-t-indigo-500 flex flex-col">
                    
                    {{-- Header Grafik & Filter Tahun --}}
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wide">Kelayakan Kerja</h3>
                        
                        {{-- Form Filter Tahun Otomatis Submit --}}
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="m-0 p-0">
                            <select name="tahun_kelayakan" onchange="this.form.submit()" class="text-xs py-1 px-2 pr-6 font-bold text-indigo-700 bg-indigo-50 border-indigo-200 rounded-lg cursor-pointer focus:ring-indigo-500 focus:border-indigo-500">
                                @if(empty($years))
                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                @else
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $tahunFilter == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </form>
                    </div>

                    {{-- Tinggi dibatasi 250px agar proporsional --}}
                    <div class="relative grow flex items-center justify-center" style="height: 250px; min-height: 250px;">
                        {{-- Cek jika data kelayakan semuanya 0 (kosong) --}}
                        @if(array_sum($dataKelayakan) == 0)
                            <div class="text-center text-gray-400">
                                <i class="fas fa-chart-pie text-4xl mb-2 opacity-50"></i>
                                <p class="text-xs font-medium">Belum ada data resume kelayakan<br>di tahun {{ $tahunFilter }}</p>
                            </div>
                        @else
                            <canvas id="kelayakanChart" class="w-full h-full"></canvas>
                        @endif
                    </div>
                </div>

                {{-- Grafik Tengah: Total Seluruh Pasien MCU --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col">
                    <h3 class="text-sm font-bold text-gray-700 mb-4 text-center uppercase tracking-wide">Tren Pasien MCU Tahunan</h3>
                    <div class="relative flex-grow" style="height: 250px; min-height: 250px;">
                        <canvas id="totalMcuChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                {{-- Grafik Kanan: Perbandingan Karyawan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col">
                    <h3 class="text-sm font-bold text-gray-700 mb-4 text-center uppercase tracking-wide">Karyawan vs Non PTST</h3>
                    <div class="relative flex-grow" style="height: 250px; min-height: 250px;">
                        <canvas id="categoryMcuChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex items-center justify-between mt-8">
                <h2 class="text-lg font-bold text-gray-700">Status MCU & Lingkungan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
                
                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Karyawan Belum MCU</p>
                    <p class="text-3xl font-black text-red-600">{{ $karyawanBelumMcu }}</p>
                    <p class="text-xs text-gray-400 mt-1">Dalam 1 tahun terakhir</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Karyawan Sudah MCU</p>
                    <p class="text-3xl font-black text-green-600">{{ $karyawanSudahMcu }}</p>
                    <p class="text-xs text-gray-400 mt-1">Progress: {{ $persenSelesai }}%</p>
                </div>

                <a href="{{ route('pemantauan.index') }}" id="lingkungan-card" 
                   class="bg-white rounded-xl shadow-sm p-5 border-l-4 transition-colors duration-300 {{ $areaBermasalah > 0 ? 'border-orange-500 bg-orange-50' : 'border-gray-300' }}">
                    <p class="text-xs font-bold uppercase tracking-wider mb-1 {{ $areaBermasalah > 0 ? 'text-orange-600' : 'text-gray-500' }}">Area Bermasalah (NAB)</p>
                    <p class="text-3xl font-black {{ $areaBermasalah > 0 ? 'text-orange-700' : 'text-gray-700' }}" id="area-count-display">{{ $areaBermasalah }}</p>
                    <p class="text-xs mt-1 font-medium {{ $areaBermasalah > 0 ? 'text-orange-600' : 'text-gray-400' }}" id="area-name-display">
                        @if ($areaBermasalah > 0) Memuat detail... @else Semua aman @endif
                    </p>
                </a>

                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Karyawan</p>
                    <p class="text-3xl font-black text-blue-600">{{ $totalKaryawan }}</p>
                    <p class="text-xs text-gray-400 mt-1">Semen Tonasa</p>
                </div>
            </div>

            <div class="mb-2 flex items-center justify-between mt-8">
                <h2 class="text-lg font-bold text-gray-700">Rekap Data Master</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 lg:gap-4">
                
                <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{$totalJadwalMcu}}</p>
                    <p class="text-xs text-gray-500 uppercase mt-1">Total Jadwal</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{ $totalPesertaMcu }}</p>
                    <p class="text-xs text-gray-500 uppercase mt-1">Non PTST</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{$totalUnitKerja}}</p>
                    <p class="text-xs text-gray-500 uppercase mt-1">Unit Kerja</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{$totalDepartemen}}</p>
                    <p class="text-xs text-gray-500 uppercase mt-1">Departemen</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800">{{$totalDokter}}</p>
                    <p class="text-xs text-gray-500 uppercase mt-1">Dokter</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Memuat Library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<script>
    const labels = @json($years);
    const totalData = @json($mcuCountsByYear);
    const karyawanData = @json($karyawanCounts);
    const nonKaryawanData = @json($nonKaryawanCounts);
    const kelayakanData = @json(array_values($dataKelayakan));

    let currentAreaNames = @json($lingkunganStatus['areaNames'] ?? []); 
    let areaIndex = 0;

    // --- Grafik 1: Total Pasien MCU Tahunan (Line Chart) ---
    const totalMcuCtx = document.getElementById('totalMcuChart');
    if (totalMcuCtx) {
        new Chart(totalMcuCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pasien',
                    data: totalData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, border: {display: false} }, x: { grid: {display: false} } },
                plugins: { legend: { display: false } }
            }
        });
    }

    // --- Grafik 2: Perbandingan Karyawan vs Non-Karyawan (Bar Chart) ---
    const categoryMcuCtx = document.getElementById('categoryMcuChart');
    if (categoryMcuCtx) {
        new Chart(categoryMcuCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Karyawan', data: karyawanData, backgroundColor: 'rgb(220, 38, 38)', borderRadius: 4 },
                    { label: 'Non-Karyawan', data: nonKaryawanData, backgroundColor: 'rgb(16, 185, 129)', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { stacked: true, grid: {display: false} }, y: { stacked: true, beginAtZero: true, border: {display: false} } },
                plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: {size: 10} } } }
            }
        });
    }
    
    // --- Grafik 3: Kelayakan Kerja (Donut Chart Skala K1-K5) ---
    const kelayakanCtx = document.getElementById('kelayakanChart');
    if (kelayakanCtx) {
        new Chart(kelayakanCtx, {
            type: 'doughnut',
            data: {
                // Label sekarang ada 5 kategori
                labels: [
                    'Fit To Work (K1)', 
                    'Fit With Note (K2)', 
                    'Fit With Restrictive (K3)', 
                    'Temporary Unfit (K4)', 
                    'Unfit (K5)'
                ],
                datasets: [{
                    data: kelayakanData,
                    backgroundColor: [
                        'rgb(34, 197, 94)',   // Hijau (K1)
                        'rgb(234, 179, 8)',   // Kuning (K2)
                        'rgb(249, 115, 22)',  // Orange (K3)
                        'rgb(239, 68, 68)',   // Merah Terang (K4)
                        'rgb(31, 41, 55)'     // Hitam/Abu Tua (K5)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', 
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            usePointStyle: true, 
                            padding: 15, 
                            font: { size: 10, weight: '500' } // Ukuran font diperkecil sedikit agar muat 5 item
                        }
                    }
                }
            }
        });
    }

    // --- Logika Refresh Lingkungan (Sama persis) ---
    function rotateAreaNames() {
        const nameDisplay = document.getElementById('area-name-display');
        if (nameDisplay && currentAreaNames.length > 0) {
            if (areaIndex >= currentAreaNames.length) areaIndex = 0;
            nameDisplay.textContent = `⚠️ Area: ${currentAreaNames[areaIndex]}`; 
            areaIndex++;
        } else if (nameDisplay) {
            nameDisplay.textContent = "Semua area aman dari NAB";
        }
    }
    setInterval(rotateAreaNames, 3000); 

    function autoRefreshCard() {
        const monitoringCard = document.getElementById('lingkungan-card');
        const nameDisplay = document.getElementById('area-name-display');
        const countDisplay = document.getElementById('area-count-display');

        if (monitoringCard) {
            fetch('{{ route('dashboard.data_lingkungan') }}') 
                .then(response => response.json())
                .then(data => {
                    currentAreaNames = data.areaNames; 
                    countDisplay.textContent = data.areaBermasalah; 
                    
                    if(data.areaBermasalah > 0) {
                        monitoringCard.classList.replace('border-gray-300', 'border-orange-500');
                        monitoringCard.classList.add('bg-orange-50');
                        countDisplay.classList.replace('text-gray-700', 'text-orange-700');
                    } else {
                        monitoringCard.classList.replace('border-orange-500', 'border-gray-300');
                        monitoringCard.classList.remove('bg-orange-50');
                        countDisplay.classList.replace('text-orange-700', 'text-gray-700');
                    }

                    areaIndex = 0;
                    rotateAreaNames();
                })
                .catch(error => console.error('Error fetching environment data:', error));
        }
    }
    setInterval(autoRefreshCard, 5000); 
    rotateAreaNames();
</script>
@endsection