@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Laporan Aktivitas Surat</h1>

    <table class="min-w-full bg-white border border-gray-200 rounded shadow overflow-hidden">
        <thead class="bg-gray-100 text-sm font-semibold text-gray-700">
            <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Judul</th>
                <th class="px-4 py-2">Jenis</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Dibuat Oleh</th>
                <th class="px-4 py-2">Tanggal</th>
            </tr>
        </thead>
        <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
            @forelse ($surats as $index => $surat)
                <tr>
                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                    <td class="px-4 py-2">{{ $surat->judul }}</td>
                    <td class="px-4 py-2 capitalize">{{ $surat->jenis }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $surat->status === 'disetujui' ? 'bg-green-100 text-green-800' :
                               ($surat->status === 'ditolak' ? 'bg-red-100 text-red-800' :
                               'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($surat->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $surat->user->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $surat->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">Belum ada data surat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
