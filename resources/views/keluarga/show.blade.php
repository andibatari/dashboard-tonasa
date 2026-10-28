@extends('layouts.app')
@section('title', 'Profil & Riwayat MCU Pasien')

@section('content')
<div class="px-2 md:px-4 lg:px-6 py-4 min-h-screen">
    <livewire:peserta-mcu-detail-manager :pesertaMcu="$pesertaMcu" />
</div>
@endsection