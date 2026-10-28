<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen Rekam Medis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white max-w-md w-full rounded-[2rem] shadow-xl overflow-hidden">
        {{-- Header Tanda Lulus Verifikasi --}}
        <div class="bg-emerald-500 p-6 text-center text-white">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                <i class="fas fa-check text-3xl text-emerald-500"></i>
            </div>
            <h1 class="text-xl font-black tracking-wide">DOKUMEN VALID</h1>
            <p class="text-xs font-medium opacity-90 mt-1">Semen Tonasa Medical Centre</p>
        </div>

        {{-- Detail Informasi --}}
        <div class="p-6 space-y-4">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Diterbitkan Oleh</p>
                <p class="text-sm font-black text-slate-800"><i class="fas fa-user-md text-blue-500 mr-1.5"></i> {{ $jadwal->dokter->nama_lengkap ?? 'Klinik STMC' }}</p>
            </div>
            
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal Pemeriksaan</p>
                <p class="text-sm font-black text-slate-800"><i class="fas fa-calendar-check text-red-500 mr-1.5"></i> {{ \Carbon\Carbon::parse($jadwal->tanggal_mcu)->format('d F Y') }}</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Atas Nama Pasien</p>
                <p class="text-sm font-black text-slate-800"><i class="fas fa-user text-emerald-500 mr-1.5"></i> {{ $namaPasien }}</p>
            </div>
        </div>

        <div class="p-4 bg-slate-50 text-center border-t border-slate-100">
            <p class="text-[10px] font-medium text-slate-400 font-mono">ID Ref: {{ $jadwal->qr_code_id }}</p>
        </div>
    </div>

</body>
</html>