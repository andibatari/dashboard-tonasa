@extends('layouts.app')
@section('title', 'Edit Data Pasien Umum')

@section('content')
<div class="px-2 md:px-6 py-6 min-h-screen">
    @livewire('keluarga-edit', ['keluarga' => $keluarga])
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Livewire.on('keluargaUpdated', () => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data pasien berhasil diperbarui.',
            icon: 'success',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#dc2626',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('karyawan.index', $keluarga->karyawan_id) }}";
            }
        });
    });
</script>
@endpush
@endsection.