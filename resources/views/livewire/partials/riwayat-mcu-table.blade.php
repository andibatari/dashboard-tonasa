@php
    // Memastikan data riwayat tersedia
    $riwayatMcu = $user->jadwalMcu ?? collect(); 
@endphp

{{-- DESKTOP VIEW --}}
<div class="overflow-x-auto border border-slate-100 rounded-2xl hidden md:block">
    <table class="min-w-full text-left bg-white">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="py-4 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">No</th>
                <th class="py-4 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal MCU</th>
                <th class="py-4 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Dokter Pemeriksa</th>
                <th class="py-4 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="py-4 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Hasil Lab</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse ($riwayatMcu as $index => $jadwalMcu)
            <tr class="hover:bg-slate-50/50">
                <td class="py-4 px-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                <td class="py-4 px-4 font-bold text-slate-700">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d M Y') }}</td>
                <td class="py-4 px-4 font-medium text-slate-600"><i class="fas fa-user-md mr-1 opacity-50"></i> {{ $jadwalMcu->dokter->nama_lengkap ?? 'N/A' }}</td>
                <td class="py-4 px-4">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold border @if($jadwalMcu->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 @elseif($jadwalMcu->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 @else bg-slate-50 text-slate-600 border-slate-200 @endif">
                        {{ $jadwalMcu->status ?? 'N/A' }}
                    </span>
                </td>
                <td class="py-4 px-4 text-center">
                    <a href="{{ route('qr-patient-detail', $jadwalMcu->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">Buka Laporan</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-10 text-center text-slate-400">
                    <i class="fas fa-folder-open text-2xl mb-2 text-slate-200"></i><br>
                    Belum ada riwayat Medical Check-Up.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE VIEW --}}
<div class="md:hidden space-y-4">
    @forelse ($riwayatMcu as $index => $jadwalMcu)
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
            <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-black uppercase text-slate-400">Tgl Periksa</span>
                    <p class="font-bold text-slate-800 text-sm mt-0.5">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d M Y') }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border @if($jadwalMcu->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 @elseif($jadwalMcu->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 @else bg-slate-50 text-slate-600 border-slate-200 @endif">
                    {{ $jadwalMcu->status ?? 'N/A' }}
                </span>
            </div>
            <div class="mb-4">
                <span class="text-[10px] font-black uppercase text-slate-400">Dokter</span>
                <p class="font-medium text-slate-600 text-sm mt-0.5 truncate">{{ $jadwalMcu->dokter->nama_lengkap ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('qr-patient-detail', $jadwalMcu->id) }}" class="block w-full py-2.5 text-center text-xs font-bold text-white bg-slate-800 rounded-xl">Lihat Laporan</a>
        </div>
    @empty
        <div class="text-center text-slate-400 p-8 bg-slate-50 rounded-2xl border border-slate-100">
            <p class="text-sm font-bold">Tidak ada riwayat MCU.</p>
        </div>
    @endforelse
</div>