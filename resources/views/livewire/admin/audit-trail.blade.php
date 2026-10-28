<div>
    {{-- Header Halaman --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 flex items-center">
                <i class="fas fa-user-secret text-emerald-500 mr-2.5"></i> Audit Trail & Keamanan
            </h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Rekam jejak aktivitas operasional sistem. Data ini bersifat permanen dan tidak dapat dihapus.</p>
        </div>
        
        {{-- Kotak Pencarian --}}
        <div class="w-full md:w-72 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400 text-xs"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari aktivitas atau modul..." class="block w-full pl-9 pr-3 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
    </div>

    {{-- Tabel Rekam Jejak --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest">Waktu</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest">Pelaku (User)</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest">Aktivitas</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest">Target Modul</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="block text-xs font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] font-bold text-slate-400"><i class="far fa-clock"></i> {{ $log->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center bg-slate-100 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700">
                                    <i class="fas fa-user-circle text-slate-400 mr-1.5"></i> 
                                    {{ $log->causer->name ?? 'Sistem / Otomatis' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border
                                    @if($log->description === 'created') bg-emerald-50 text-emerald-600 border-emerald-200
                                    @elseif($log->description === 'updated') bg-blue-50 text-blue-600 border-blue-200
                                    @elseif($log->description === 'deleted') bg-red-50 text-red-600 border-red-200
                                    @else bg-slate-50 text-slate-600 border-slate-200 @endif">
                                    {{ $log->description }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[10px] font-mono font-bold text-slate-500">
                                {{ class_basename($log->subject_type) }} <span class="text-slate-300">#{{ $log->subject_id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                {{-- Jika ada perubahan data (Update), tampilkan Data Lama vs Data Baru --}}
                                @if($log->description === 'updated' && isset($log->properties['old']) && isset($log->properties['attributes']))
                                    <div class="flex flex-col gap-1.5 text-[10px]">
                                        @foreach($log->properties['attributes'] as $key => $newValue)
                                            @if(isset($log->properties['old'][$key]) && $log->properties['old'][$key] != $newValue)
                                                <div class="flex items-center gap-2 bg-white px-2 py-1 border border-slate-100 rounded">
                                                    <span class="font-bold text-slate-500 w-24 truncate">{{ $key }}</span>
                                                    <span class="line-through text-red-400 truncate max-w-[100px]">{{ $log->properties['old'][$key] }}</span>
                                                    <i class="fas fa-arrow-right text-[8px] text-slate-300"></i>
                                                    <span class="font-bold text-emerald-600 truncate max-w-[100px]">{{ $newValue }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] font-medium italic text-slate-400">Data terekam di sistem.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center">
                                <i class="fas fa-shield-alt text-4xl text-slate-200 mb-3"></i>
                                <p class="text-xs font-bold text-slate-400">Belum ada aktivitas terekam di sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>