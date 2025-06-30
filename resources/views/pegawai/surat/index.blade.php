@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Daftar Surat Anda</h1>
    <div class="flex gap-2">
        <a href="{{ route('surat.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded shadow hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Buat Surat Baru
        </a>
        <a href="#ajukan-persetujuan" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded shadow hover:bg-green-700 transition">
            <i class="fas fa-paper-plane mr-2"></i> Ajukan Persetujuan
        </a>
    </div>
</div>

<table class="min-w-full bg-white border border-gray-200 rounded shadow overflow-hidden">
    <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
        <tr>
            <th class="px-4 py-2 border-b">No</th>
            <th class="px-4 py-2 border-b">Judul Surat</th>
            <th class="px-4 py-2 border-b">Jenis Surat</th>
            <th class="px-4 py-2 border-b">Status</th>
            <th class="px-4 py-2 border-b text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-sm text-gray-700">
        @foreach ($surats as $index => $surat)
            <tr class="hover:bg-blue-50 transition">
                <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                <td class="px-4 py-2 border-b font-medium">{{ $surat->judul }}</td>
                <td class="px-4 py-2 border-b capitalize">{{ $surat->jenis }}</td>
                <td class="px-4 py-2 border-b">
                    @php
                        $statusClass = match($surat->status) {
                            'disetujui' => 'bg-green-100 text-green-800',
                            'ditolak' => 'bg-red-100 text-red-800',
                            default => 'bg-yellow-100 text-yellow-800'
                        };
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                        {{ ucfirst($surat->status) }}
                    </span>
                </td>
                <td class="px-4 py-2 border-b text-center space-x-2">
                    @if($surat->status === 'menunggu')
                        <a href="{{ route('surat.edit', $surat->id) }}" class="inline-flex items-center px-3 py-1 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <form action="{{ url('pegawai/surat/' . $surat->id . '/ajukan') }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1 text-sm text-white bg-green-600 hover:bg-green-700 rounded-md shadow">
                                <i class="fas fa-paper-plane mr-1"></i> Ajukan
                            </button>
                        </form>
                    @else
                        <button class="inline-flex items-center px-3 py-1 text-sm text-white bg-gray-400 rounded-md shadow cursor-not-allowed" disabled>
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        <button class="inline-flex items-center px-3 py-1 text-sm text-white bg-gray-400 rounded-md shadow cursor-not-allowed" disabled>
                            <i class="fas fa-paper-plane mr-1"></i> Ajukan
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection