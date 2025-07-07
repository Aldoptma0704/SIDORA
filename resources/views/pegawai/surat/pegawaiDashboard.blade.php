@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex gap-2">
        <a href="{{ route('surat.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded shadow hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Buat Surat Baru
        </a>
        <a href="#ajukan-persetujuan" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded shadow hover:bg-green-700 transition">
            <i class="fas fa-paper-plane mr-2"></i> Ajukan Persetujuan
        </a>
    </div>
</div>

<h1 class="text-2xl font-bold mb-4 text-gray-800">Statistik Surat Anda</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow text-center">
        <div class="text-yellow-500 text-4xl mb-2"><i class="fas fa-hourglass-half"></i></div>
        <div class="text-sm text-gray-600">Menunggu</div>
        <div class="text-2xl font-bold">{{ $countMenunggu }}</div>
    </div>
    <div class="bg-white p-6 rounded shadow text-center">
        <div class="text-green-500 text-4xl mb-2"><i class="fas fa-check-circle"></i></div>
        <div class="text-sm text-gray-600">Disetujui</div>
        <div class="text-2xl font-bold">{{ $countDisetujui }}</div>
    </div>
    <div class="bg-white p-6 rounded shadow text-center">
        <div class="text-red-500 text-4xl mb-2"><i class="fas fa-times-circle"></i></div>
        <div class="text-sm text-gray-600">Ditolak</div>
        <div class="text-2xl font-bold">{{ $countDitolak }}</div>
    </div>
</div>
@endsection
