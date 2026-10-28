@extends('layouts.app')
@section('title', 'Edit Data Karyawan')

@section('content')
<div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
    {{-- Melewatkan objek Eloquent Karyawan ke komponen Livewire Edit untuk inisialisasi state mount() --}}
    @livewire('edit-karyawan-form', ['karyawan' => $karyawan])
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Event pendengar khusus pembaruan data (Update)
    Livewire.on('karyawanUpdated', () => {
        Swal.fire({
            title: 'Tersimpan!',
            text: 'Data profil karyawan berhasil diperbarui.',
            icon: 'success',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#2563eb', // Warna biru pembeda antara aksi Edit dan Tambah
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('karyawan.index') }}";
            }
        });
    });
</script>
@endpush
@endsection