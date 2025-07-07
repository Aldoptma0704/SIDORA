@extends('layouts.app')

@section('content')
{{-- Notifikasi Sukses --}}
@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif

<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Status Surat Anda</h1>
</div>

{{-- Kartu Statistik --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    {{-- (Konten kartu statistik tidak berubah) --}}
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="bg-blue-100 text-blue-600 p-3 rounded-full"><i class="fas fa-envelope text-xl"></i></div>
        <div>
            <p class="text-sm text-gray-600">Surat Menunggu</p>
            <p class="text-2xl font-bold">{{ $jumlahMenunggu }}</p>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="bg-green-100 text-green-600 p-3 rounded-full"><i class="fas fa-check-circle text-xl"></i></div>
        <div>
            <p class="text-sm text-gray-600">Surat Disetujui</p>
            <p class="text-2xl font-bold">{{ $jumlahDisetujui }}</p>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
        <div class="bg-red-100 text-red-600 p-3 rounded-full"><i class="fas fa-times-circle text-xl"></i></div>
        <div>
            <p class="text-sm text-gray-600">Surat Ditolak</p>
            <p class="text-2xl font-bold">{{ $jumlahDitolak }}</p>
        </div>
    </div>
</div>

{{-- Aksi Tabel & Pencarian --}}
<div class="mb-4 bg-white p-4 rounded-lg shadow flex justify-between items-center" x-data="{ open: false }">
    <div><h3 class="text-lg font-semibold text-gray-700">Daftar Surat</h3></div>
    <div class="relative">
        <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fas fa-ellipsis-v text-gray-600"></i>
        </button>
        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-xl z-10 origin-top-right" x-cloak>
            {{-- MODIFIED: Search input dibungkus form --}}
            <form action="{{ route('surat.status_surat') }}" method="GET" class="p-4">
                <label for="search" class="text-sm font-medium text-gray-700">Search Surat</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="search" name="search" placeholder="Ketik judul lalu Enter..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </form>
            <div class="border-t border-gray-100"></div>
            {{-- MODIFIED: Tombol hapus menjadi button type="submit" untuk form di bawah --}}
            <button type="submit" form="bulkDeleteForm" class="w-full text-left block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <i class="fas fa-trash-alt mr-2 text-red-500"></i> Hapus Surat Terpilih
            </button>
        </div>
    </div>
</div>

{{-- MODIFIED: Seluruh tabel dibungkus dalam form untuk bulk delete --}}
<form action="{{ route('surat.bulk_delete') }}" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat yang dipilih?');">
    @csrf
    @method('DELETE')
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded shadow">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                <tr>
                    {{-- MODIFIED: Tambah kolom checkbox --}}
                    <th class="px-4 py-2 border-b w-12 text-center">
                        <input type="checkbox" id="selectAll" class="rounded">
                    </th>
                    <th class="px-4 py-2 border-b">No</th>
                    <th class="px-4 py-2 border-b">Judul Surat</th>
                    <th class="px-4 py-2 border-b">Jenis Surat</th>
                    <th class="px-4 py-2 border-b">Status</th>
                    <th class="px-4 py-2 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @forelse ($surats as $index => $surat)
                    <tr class="hover:bg-blue-50 transition">
                        {{-- MODIFIED: Tambah kolom checkbox --}}
                        <td class="px-4 py-2 border-b text-center">
                            <input type="checkbox" name="ids[]" value="{{ $surat->id }}" class="rounded surat-checkbox">
                        </td>
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
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($surat->status) }}</span>
                        </td>
                        <td class="px-4 py-2 border-b text-center">
                            {{-- MODIFIED: Tambahkan parameter 'view' => 'status' pada route --}}
                            <a href="{{ route('surat.preview', ['id' => $surat->id, 'view' => 'status']) }}" class="inline-flex items-center px-3 py-1 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow">
                                <i class="fas fa-eye mr-1"></i> Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">
                            Tidak ada data surat yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

{{-- ADDED: Script untuk fungsionalitas checkbox "Pilih Semua" --}}
@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.surat-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });
</script>
@endpush

@endsection