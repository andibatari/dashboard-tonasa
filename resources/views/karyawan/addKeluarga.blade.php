@extends('layouts.app')
@section('title', 'Registrasi Pasien')

@section('content')
<div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
    <div class="mb-4">
        @if ($karyawan)
            {{-- Mengembalikan ke detail karyawan spesifik jika penambahan dilakukan dari halaman keluarga --}}
            <a href="{{ route('karyawan.show', $karyawan->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left mr-1.5"></i> Kembali ke {{ $karyawan->nama_karyawan }}
            </a>
        @else
            {{-- Native history fallback --}}
            <a href="javascript:history.back()" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left mr-1.5"></i> Kembali
            </a>
        @endif
    </div>

    <div class="bg-white rounded-xl md:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden px-3 py-4 md:p-8 w-full max-w-7xl mx-auto"> 
        <div class="mb-4 md:mb-8 border-b border-slate-100 pb-3 md:pb-4 flex items-center">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center mr-3 shrink-0">
                <i class="fas {{ $karyawan ? 'fa-users' : 'fa-user-injured' }} text-sm md:text-xl"></i>
            </div>
            <div>
                {{-- Ukuran teks diubah dari text-lg menjadi text-base untuk HP --}}
                <h1 class="text-base md:text-2xl font-black text-slate-800 leading-tight">
                    {{ $karyawan ? 'Tambah Keluarga' : 'Pasien Umum' }} 
                </h1>
                <p class="text-[9px] md:text-xs font-medium text-slate-500 mt-0.5">
                    {{ $karyawan ? "Input data tanggungan medis." : 'Pendaftaran non-karyawan PTST.' }}
                </p>
            </div>
        </div>
        
        {{-- Mount form logic --}}
        <livewire:add-keluarga-karyawan :karyawan_id="$karyawan ? $karyawan->id : null" />
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Kalkulasi umur client-side sebelum dikirim ke backend
        const tanggalLahirInput = document.getElementById('tanggal_lahir');
        if (tanggalLahirInput) {
            tanggalLahirInput.addEventListener('change', function() {
                const birthDate = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
                
                // Melempar update value ke public parameter di component Livewire
                Livewire.dispatch('updateUmur', { age: age });
            });
        }
    });

    Livewire.on('updateUmur', (age) => {
        const umurInput = document.getElementById('umur');
        if (umurInput) { umurInput.value = age.age; }
    });

    Livewire.on('show-success-popup', (event) => {
        let data = event[0]; 
        Swal.fire({
            title: data.title, 
            text: data.message, 
            icon: 'success', 
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#dc2626', 
            allowOutsideClick: false,
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            // Logika Redirect: Pindah ke halaman daftar pasien setelah diklik
            if (result.isConfirmed && data.redirect) {
                window.location.href = data.redirect;
            }
        });
    });

    Livewire.on('show-error-popup', (event) => {
        Swal.fire({
            title: event[0].title, 
            text: event[0].message, 
            icon: 'error', 
            confirmButtonColor: '#dc2626', 
            customClass: { popup: 'rounded-2xl' }
        });
    });
</script>
@endpush