@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Status Surat Balasan</h1>

    <div class="mb-6">
        <a href="/pimpinan/balasansurat" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded shadow">
            + Buat Surat Balasan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    @if($drafts->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 shadow text-sm rounded">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3 border">No</th>
                        <th class="px-4 py-3 border text-left">Judul</th>
                        <th class="px-4 py-3 border text-left">Isi</th>
                        <th class="px-4 py-3 border text-center">Status</th>
                        <th class="px-4 py-3 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    @foreach($drafts as $index => $surat)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border text-center">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 border">{{ $surat->judul }}</td>
                            <td class="px-4 py-3 border">{{ Str::limit(strip_tags($surat->isi), 100) }}</td>
                            <td class="px-4 py-3 border text-center capitalize">
                                {{ $surat->status_balasan ?? '-' }}
                            </td>
                                <td class="px-4 py-3 border text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Tombol View Surat --}}
                                        <a href="{{ route('pimpinan.surat.view', $surat->id) }}"
                                        class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded shadow text-sm">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>

                                        {{-- Tombol Kirim ke Admin --}}
                                        <form action="{{ route('pimpinan.kirim-surat', $surat->id) }}" method="POST" onsubmit="return confirm('Kirim surat ini ke Admin?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded shadow text-sm">
                                                <i class="fas fa-paper-plane mr-1"></i> Kirim
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-600">Tidak ada surat draft yang ditemukan.</p>
    @endif
@endsection
