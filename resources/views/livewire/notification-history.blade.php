{{-- Container utama dibuat lebih padat dengan mengurangi padding vertikal (py-4) agar memangkas ruang kosong di atas/bawah layar --}}
<div class="max-w-7xl mx-auto py-4 px-3 md:px-6">
    @section('title', 'Riwayat Pengiriman Notifikasi')
    
    {{-- Navigasi tab dengan overflow-x-auto agar bisa digeser horizontal jika layar HP sangat kecil, menyembunyikan scrollbar bawaan --}}
    <div class="mb-4 flex space-x-2 bg-white p-1.5 rounded-xl shadow-sm border border-slate-100 overflow-x-auto hide-scrollbar">
        <button @click="activeTab = 'broadcast'" :class="activeTab === 'broadcast' ? 'bg-red-50 text-red-600 font-bold shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="px-5 py-2.5 rounded-lg transition-colors flex items-center whitespace-nowrap text-xs md:text-sm">
            <i class="fas fa-bullhorn mr-2"></i> Riwayat Notifikasi
        </button>
    </div>

    {{-- Kotak utama riwayat dikurangi padding-nya (p-4 md:p-5) agar lebih rapat --}}
    <div class="bg-white rounded-xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] p-4 md:p-5 border border-slate-100">
        <h3 class="text-base md:text-lg font-black text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center">
            <div class="w-7 h-7 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center mr-2"><i class="fas fa-history text-xs"></i></div>
            Riwayat Pengiriman
        </h3>

        {{-- Tabel khusus mode desktop, disembunyikan di layar HP (hidden md:block) --}}
        <div class="hidden md:block overflow-x-auto border border-slate-100 rounded-xl">
            <table class="min-w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-2.5 text-[10px] font-bold text-slate-500 uppercase">Tgl. Kirim</th>
                        <th class="px-4 py-2.5 text-[10px] font-bold text-slate-500 uppercase">Target Jadwal</th>
                        <th class="px-4 py-2.5 text-[10px] font-bold text-slate-500 uppercase">Mode</th>
                        <th class="px-4 py-2.5 text-[10px] font-bold text-slate-500 uppercase text-center">Total</th>
                        <th class="px-4 py-2.5 text-[10px] font-bold text-slate-500 uppercase text-center">Sukses Email</th>
                        <th class="px-4 py-2.5 text-[10px] font-bold text-slate-500 uppercase text-center">Sukses FCM</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-2.5 text-xs font-bold text-slate-800">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-2.5 text-xs font-medium text-slate-600">
                                @if($log->mode === 'broadcast' || empty($log->scheduled_date))
                                    <span class="text-slate-400 italic">Instan</span>
                                @else
                                    {{ \Carbon\Carbon::parse($log->scheduled_date)->format('d F Y') }}
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded border 
                                    @if($log->mode === 'automatic') bg-indigo-50 border-indigo-100 text-indigo-600 
                                    @elseif($log->mode === 'broadcast') bg-purple-50 border-purple-100 text-purple-600 
                                    @else bg-amber-50 border-amber-100 text-amber-600 @endif">
                                    {{ $log->mode }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-center text-xs font-black text-slate-700">{{ $log->total_targets }}</td>
                            <td class="px-4 py-2.5 text-center text-xs font-black {{ $log->email_success > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $log->email_success }}</td>
                            <td class="px-4 py-2.5 text-center text-xs font-black {{ $log->fcm_success > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $log->fcm_success }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400 text-xs">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Tampilan kartu compact (padat) khusus layar HP untuk mengurangi scroll vertikal berlebihan --}}
        <div class="md:hidden flex flex-col gap-3">
            @forelse ($logs as $log)
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-black text-slate-800 text-[11px]"><i class="fas fa-clock text-slate-400 mr-1"></i> {{ $log->created_at->format('d/m/Y H:i') }}</span>
                        <span class="px-1.5 py-0.5 text-[8px] font-black uppercase rounded border @if($log->mode === 'automatic') bg-indigo-50 border-indigo-100 text-indigo-600 @elseif($log->mode === 'broadcast') bg-purple-50 border-purple-100 text-purple-600 @else bg-amber-50 border-amber-100 text-amber-600 @endif">{{ $log->mode }}</span>
                    </div>
                    <div class="text-[10px] text-slate-600 mb-2 truncate">
                        <span class="font-bold">Target:</span> 
                        @if($log->mode === 'broadcast' || empty($log->scheduled_date)) <span class="italic text-slate-400">Instan</span> @else {{ \Carbon\Carbon::parse($log->scheduled_date)->format('d/m/Y') }} @endif
                    </div>
                    <div class="grid grid-cols-3 gap-1 border-t border-slate-100 pt-2">
                        <div class="bg-slate-50 rounded py-1 text-center"><span class="block text-[8px] font-bold text-slate-400 uppercase">Total</span><span class="text-xs font-black text-slate-800">{{ $log->total_targets }}</span></div>
                        <div class="bg-slate-50 rounded py-1 text-center"><span class="block text-[8px] font-bold text-slate-400 uppercase">Email</span><span class="text-xs font-black {{ $log->email_success > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $log->email_success }}</span></div>
                        <div class="bg-slate-50 rounded py-1 text-center"><span class="block text-[8px] font-bold text-slate-400 uppercase">FCM</span><span class="text-xs font-black {{ $log->fcm_success > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $log->fcm_success }}</span></div>
                    </div>
                </div>
            @empty
                <div class="bg-slate-50 border border-slate-100 text-slate-500 p-4 text-center rounded-xl text-xs font-bold">Belum ada riwayat.</div>
            @endforelse
        </div>
        
        <div class="mt-4 border-t border-slate-100 pt-3">{{ $logs->links() }}</div>
    </div>
</div>