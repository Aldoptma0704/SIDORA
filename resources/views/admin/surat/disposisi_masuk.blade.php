@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Daftar Surat untuk Disposisi</h1>

<table class="min-w-full bg-white rounded shadow border">
    <thead>
        <tr>
            <th class="px-4 py-2 border">No</th>
            <th class="px-4 py-2 border">Judul</th>
            <th class="px-4 py-2 border">Disposisi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($surats as $index => $surat)
            <tr class="hover:bg-gray-100">
                <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                <td class="px-4 py-2 border">{{ $surat->judul }}</td>
                <td class="px-4 py-2 border">{{ $surat->disposisi }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-4">Tidak ada surat untuk didisposisi.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
