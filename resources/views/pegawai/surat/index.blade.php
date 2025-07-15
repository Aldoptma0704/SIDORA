@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif
@if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow" role="alert">
        <p class="font-bold">Terjadi Kesalahan</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="{ showModal: false }">
    <div class="flex justify-between items-center mb-6">
        @php
            $title = match($jenis ?? null) {
                'masuk'   => 'Daftar Surat Masuk',
                'keluar'  => 'Daftar Surat Keluar',
                default   => 'Daftar Semua Surat Anda',
            };
        @endphp
        <h1 class="text-2xl font-semibold text-gray-800">{{ $title }}</h1>
        
        <div class="flex gap-2">
            <button @click="showModal = true" type="button" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-semibold rounded shadow hover:bg-purple-700 transition">
                <i class="fas fa-upload mr-2"></i> Upload PDF
            </button>
            <a href="{{ route('surat.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded shadow hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Buat Surat Baru
            </a>
        </div>
    </div>

    {{-- Modal Upload PDF --}}
    <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-cloak>
        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Upload File Surat (PDF)</h3>
            <form action="{{ route('surat.upload_pdf') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jenis" value="{{ $jenis ?? 'masuk' }}">
                <div>
                    <label for="file_surat" class="block text-sm font-medium text-gray-700">Pilih File</label>
                    <input type="file" name="file_surat" id="file_surat" accept=".pdf" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Hanya file PDF. Maksimal 5MB.</p>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <table class="min-w-full bg-white border border-gray-200 rounded shadow overflow-hidden">
        <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
            <tr>
                <th class="px-4 py-2 border-b">No</th>
                <th class="px-4 py-2 border-b">Judul Surat</th>
                <th class="px-4 py-2 border-b">Jenis</th>
                <th class="px-4 py-2 border-b">Status</th>
                <th class="px-4 py-2 border-b text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm text-gray-700">
            @forelse ($surats as $index => $surat)
                <tr class="hover:bg-blue-50 transition">
                    <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                    <td class="px-4 py-2 border-b font-medium">
                        {{ $surat->judul }}
                        @if($surat->disposisi_user_id && $surat->disposisi_user_id == auth()->id())
                            <span class="ml-2 text-xs text-blue-600 bg-blue-50 font-semibold px-2 py-0.5 rounded-full">Disposisi untuk Anda</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border-b">
                        @php
                            $jenisDisplay = match($surat->jenis) {
                                'masuk' => 'Masuk',
                                'keluar' => 'Keluar (Template)',
                                'keluar_full' => 'Keluar (Kop Surat)',
                                default => ucfirst($surat->jenis)
                            };
                        @endphp
                        {{ $jenisDisplay }}
                    </td>
                    <td class="px-4 py-2 border-b">
                        @php
                            $statusClass = match($surat->status) {
                                'disetujui' => 'bg-green-100 text-green-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'draft' => 'bg-gray-100 text-gray-800',
                                default => 'bg-yellow-100 text-yellow-800'
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                            {{ ucfirst($surat->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 border-b text-center">
                        {{-- Tombol Lihat/Preview --}}
                        @if ($surat->file_path)
                            <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="inline-flex items-center px-3 py-1 text-sm text-white bg-gray-600 hover:bg-gray-700 rounded-md shadow">
                                <i class="fas fa-file-pdf mr-1"></i> Lihat PDF
                            </a>
                        @else
                            <a href="{{ route('surat.preview', $surat->id) }}" class="inline-flex items-center px-3 py-1 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow">
                                <i class="fas fa-eye mr-1"></i> Lihat
                            </a>
                        @endif

                        {{-- ADDED: Tombol Edit & Hapus hanya untuk draft --}}
                        @if ($surat->status == 'draft' && $surat->user_id == auth()->id())
                            <a href="{{ route('surat.edit', $surat->id) }}" class="inline-flex items-center px-3 py-1 text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-md shadow ml-2">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('surat.destroy', $surat->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus draft surat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1 text-sm text-white bg-red-600 hover:bg-red-700 rounded-md shadow ml-1">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500">
                        Belum ada surat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
