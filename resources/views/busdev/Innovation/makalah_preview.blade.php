<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $formattedFilename }} - Pratinjau Makalah KMST</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kmst-gold.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col justify-between selection:bg-[#F5333F]/20 selection:text-[#F5333F]">

    {{-- Top Navbar --}}
    <header class="bg-white border-b border-slate-200 shadow-xs sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-kmst-gold.png') }}" alt="Logo Semen Tonasa KMST" class="h-9 w-auto object-contain">
                <div class="border-l border-slate-200 pl-3">
                    <span class="block text-xs font-black uppercase tracking-wider text-slate-900">
                        Portal Manajemen Inovasi
                    </span>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        Konvensi Mutu Semen Tonasa (KMST)
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('busdev.innovation.makalah.download', $reg->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs font-black text-white bg-gradient-to-r from-[#F5333F] to-rose-600 hover:from-red-600 hover:to-rose-700 rounded-xl shadow-md shadow-red-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>
                    <span>Unduh Dokumen Resmi</span>
                </a>
                <button type="button" onclick="window.close()" 
                        class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">
                    Tutup Tab
                </button>
            </div>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full space-y-6">

        {{-- Hero Document Card --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                
                {{-- Left: Icon & File Meta --}}
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 17.5h-1l-1.5-6h1.2l.9 4.2 1-4.2h1.1l1 4.2.9-4.2h1.2l-1.5 6h-1l-1.1-4.4-1.2 4.4z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 text-[10px] font-black uppercase rounded-md bg-red-100 text-[#F5333F]">
                                {{ $reg->kategori }}
                            </span>
                            <span class="px-2.5 py-0.5 text-[10px] font-mono font-bold rounded-md bg-blue-50 text-blue-700">
                                DOCX &bull; {{ $fileSize }}
                            </span>
                            <span class="text-xs text-slate-400">
                                Terdaftar: {{ $reg->created_at ? $reg->created_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight font-mono break-all">
                            {{ $formattedFilename }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Tim Inovator: <strong class="text-slate-800">{{ $reg->nama_tim }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Right: Direct Download Button --}}
                <div class="flex flex-col sm:flex-row md:flex-col gap-2.5 flex-shrink-0">
                    <a href="{{ route('busdev.innovation.makalah.download', $reg->id) }}" 
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-black text-white bg-[#F5333F] hover:bg-red-700 rounded-2xl shadow-lg shadow-red-500/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path></svg>
                        <span>Unduh Dokumen (.docx)</span>
                    </a>
                </div>

            </div>

            {{-- Info Notice --}}
            <div class="mt-6 p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-3">
                <span class="text-base mt-0.5">ℹ️</span>
                <div class="space-y-0.5">
                    <p class="font-extrabold">Informasi Format Dokumen Microsoft Word (.docx)</p>
                    <p class="text-amber-800 leading-relaxed">
                        Browser Google Chrome tidak memiliki pemutar internal untuk dokumen Word (.docx). Klik tombol <strong>"Unduh Dokumen (.docx)"</strong> di atas untuk menyimpan berkas dengan nama resmi <code>{{ $formattedFilename }}</code> dan membukanya di Microsoft Word Anda.
                    </p>
                </div>
            </div>

            {{-- Team Metadata Details --}}
            @php
                $rawPeserta = $reg->data_peserta;
                if (is_string($rawPeserta)) {
                    $decoded = json_decode($rawPeserta, true);
                    if (is_string($decoded)) $decoded = json_decode($decoded, true);
                    $peserta = is_array($decoded) ? $decoded : [];
                } elseif (is_array($rawPeserta)) {
                    $peserta = $rawPeserta;
                } else {
                    $peserta = [];
                }

                $rawBenefit = $reg->bidang_benefit;
                if (is_string($rawBenefit)) {
                    $decodedBenefit = json_decode($rawBenefit, true);
                    $benefits = is_array($decodedBenefit) ? $decodedBenefit : [$rawBenefit];
                } elseif (is_array($rawBenefit)) {
                    $benefits = $rawBenefit;
                } else {
                    $benefits = [];
                }
            @endphp

            <div class="mt-8 space-y-6">
                
                {{-- Info Pendaftar & Benefit --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Akun Pengirim</span>
                        <p class="text-sm font-extrabold text-slate-900 mt-1 flex items-center gap-1.5">
                            <span>👤</span>
                            <span>{{ $reg->user?->name ?? 'User #' . $reg->user_id }}</span>
                        </p>
                        <p class="text-xs text-slate-500 font-mono">{{ $reg->user?->email ?? '-' }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Bidang Manfaat (Benefit)</span>
                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                            @forelse($benefits as $b)
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ $b }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400 italic">Tidak ada bidang manfaat terpilih</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Susunan Anggota Tim --}}
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>👥</span>
                        Susunan Personel Tim
                    </h3>
                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-400 border-b border-slate-200 tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Peran</th>
                                    <th class="px-4 py-3">Nama Lengkap</th>
                                    <th class="px-4 py-3">No. SAP</th>
                                    <th class="px-4 py-3">Departemen</th>
                                    <th class="px-4 py-3">Unit Kerja</th>
                                    <th class="px-4 py-3">Kontak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($peserta as $roleKey => $item)
                                    @if(is_array($item) && !empty($item['nama']))
                                        @php
                                            $badgeClass = match($roleKey) {
                                                'ketua'       => 'bg-red-50 text-red-700 border-red-200 font-bold',
                                                'fasilitator' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'sekretaris'  => 'bg-purple-50 text-purple-700 border-purple-200',
                                                default       => 'bg-slate-50 text-slate-600 border-slate-200',
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded-md border {{ $badgeClass }}">
                                                    {{ ucfirst($roleKey) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 font-bold text-slate-900">{{ $item['nama'] ?? '-' }}</td>
                                            <td class="px-4 py-3 font-mono text-slate-500">{{ $item['sap'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-slate-600">{{ $item['dept'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $item['uk'] ?? '-' }}</td>
                                            <td class="px-4 py-3 font-mono text-slate-600">{{ $item['hp'] ?? '-' }}</td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-slate-400 italic">
                                            Data susunan tim tidak tersedia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </main>

    {{-- Footer --}}
    <footer class="py-4 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
        &copy; {{ date('Y') }} PT Semen Tonasa &bull; Konvensi Mutu Semen Tonasa (KMST)
    </footer>

</body>
</html>

