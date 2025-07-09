@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6 p-4 bg-blue-100 border border-blue-300 text-blue-800 rounded shadow">
    📬 Daftar Disposisi Surat yang Telah Disetujui
</h1>


{{-- Flash message --}}
@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif

@php
    // Group surat berdasarkan disposisi tujuannya
    $grouped = $surats->groupBy('disposisi');
@endphp

@forelse ($grouped as $tujuan => $listSurat)
    <div class="mb-10">
        <h2 class="mb-4 p-4 bg-blue-100 border border-blue-400 text-black rounded text-xl font-semibold">📌 Tujuan: {{ $tujuan ?? 'Tidak Diketahui' }}</h2>

        <table class="w-full bg-white shadow-md rounded mb-4">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Judul Surat</th>
                    <th class="px-4 py-2 text-left">Tanggal Disetujui</th>
                    <th class="px-4 py-2 text-left">Isi Ringkas</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listSurat as $surat)
                    <tr class="border-b">
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $surat->judul }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ \Carbon\Carbon::parse($surat->signed_at)->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ Str::limit(strip_tags($surat->isi), 80) }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('pimpinan.surat.preview', $surat->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded shadow">
                                👁️ Preview
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@empty
    <p class="text-gray-500">Belum ada surat yang didisposisikan.</p>
@endforelse
@endsection
