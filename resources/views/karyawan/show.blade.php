@extends('layouts.app')
@section('content')
<div class="px-2 md:px-4 lg:px-6 py-4 min-h-screen">
    <livewire:employee-detail-manager :karyawan="$karyawan" />
</div>
@endsection