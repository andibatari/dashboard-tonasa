<x-app-layout>
    <x-slot name="header">
        {{ __('Audit Trail & Log Aktivitas') }}
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Jejak Aktivitas Sistem</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau seluruh perubahan data (Audit Trail) yang terjadi di dalam aplikasi.</p>
            </div>

            <!-- Tabel Log -->
            <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4">Pelaku (User)</th>
                                <th class="px-6 py-4">Aktivitas</th>
                                <th class="px-6 py-4">Detail Perubahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                            
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20 transition-colors">
                                
                                <!-- Waktu -->
                                <td class="px-6 py-4 align-top">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }} WITA</p>
                                </td>
                                
                                <!-- Pelaku -->
                                <td class="px-6 py-4 align-top">
                                    @if($log->causer)
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-gray-800 rounded-lg dark:bg-gray-700">
                                                {{ substr($log->causer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->causer->name }}</p>
                                                <p class="text-xs text-[#F5333F]">{{ $log->causer->roles->first()->name ?? 'User' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm italic text-gray-500">Sistem (Otomatis)</span>
                                    @endif
                                </td>
                                
                                <!-- Aktivitas -->
                                <td class="px-6 py-4 align-top">
                                    @php
                                        $color = match($log->event) {
                                            'created' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'updated' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                            'deleted' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400'
                                        };
                                        $modul = class_basename($log->subject_type);
                                    @endphp
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-md {{ $color }} uppercase">
                                        {{ $log->event }}
                                    </span>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 font-medium">
                                        Modul: <span class="font-bold text-gray-900 dark:text-gray-200">{{ $modul }}</span> (ID: {{ $log->subject_id }})
                                    </p>
                                </td>
                                
                                <!-- Detail Perubahan -->
                                <td class="px-6 py-4 align-top whitespace-normal min-w-[300px]">
                                    @if($log->properties->has('old') || $log->properties->has('attributes'))
                                        <div class="text-xs bg-gray-50 dark:bg-[#111111] p-3 rounded-lg border border-gray-100 dark:border-gray-800 space-y-2">
                                            @if($log->properties->has('old'))
                                                <div>
                                                    <span class="font-bold text-red-500 uppercase tracking-wider text-[10px] mb-1 block">Sebelumnya:</span>
                                                    <pre class="font-mono text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ json_encode($log->properties['old'], JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                                <div class="h-px bg-gray-200 dark:bg-gray-800 my-2"></div>
                                            @endif
                                            @if($log->properties->has('attributes'))
                                                <div>
                                                    <span class="font-bold text-emerald-500 uppercase tracking-wider text-[10px] mb-1 block">Menjadi / Baru:</span>
                                                    <pre class="font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ json_encode($log->properties['attributes'], JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs italic text-gray-400">{{ $log->description }}</span>
                                    @endif
                                </td>
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-sm text-center text-gray-500">
                                    Belum ada aktivitas yang terekam.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>