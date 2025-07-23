@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-xl font-semibold mb-4">Daftar Surat Masuk dari Pimpinan</h2>

    @if($surats->isEmpty())
        <div class="text-gray-500">Belum ada surat yang dikirim oleh pimpinan.</div>
    @else
        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-2 px-4">Judul</th>
                        <th class="py-2 px-4">Nomor Surat</th>
                        <th class="py-2 px-4">Pengirim</th>
                        <th class="py-2 px-4">Tanggal Dikirim</th>
                        <th class="py-2 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surats as $surat)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4">{{ $surat->judul }}</td>
                            <td class="py-2 px-4">{{ $surat->nomor_surat ?? '-' }}</td>
                            <td class="py-2 px-4">{{ $surat->pengirim->name ?? 'Tidak diketahui' }}</td>
                            <td class="py-2 px-4">{{ $surat->updated_at->format('d M Y') }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('admin.surat.dari-pimpinan.lihat', $surat->id) }}"
                                   class="text-blue-600 hover:underline">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
