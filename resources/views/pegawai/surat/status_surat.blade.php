@extends('layouts.app')

@section('content')
{{-- Notifikasi Sukses --}}
@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif

<div x-data="{ showModal: false }">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Status Pengajuan Surat</h1>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
        <div>
            <button @click="showModal = true" class="px-4 py-2 bg-green-600 text-white font-semibold rounded shadow hover:bg-green-700 transition">
                <i class="fas fa-paper-plane mr-2"></i> Ajukan Permohonan
            </button>
        </div>
        <div class="relative">
            {{-- Tombol menu ... (tidak berubah) --}}
        </div>
    </div>

    {{-- MODAL UNTUK MENGAJUKAN SURAT DRAFT --}}
    <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-cloak>
        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Pilih Draft Surat untuk Diajukan</h3>
            <form action="{{ route('surat.ajukan') }}" method="POST">
                @csrf
                <div class="max-h-96 overflow-y-auto border rounded-md p-4 space-y-3">
                    @forelse ($drafts as $draft)
                        <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="ids[]" value="{{ $draft->id }}" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-4 text-sm font-medium text-gray-800">{{ $draft->judul }}</span>
                            <span class="ml-auto text-xs text-gray-500">Dibuat: {{ $draft->created_at->format('d M Y') }}</span>
                        </label>
                    @empty
                        <p class="text-center text-gray-500 py-4">Tidak ada draft surat yang tersedia untuk diajukan.</p>
                    @endforelse
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Ajukan Surat Terpilih</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel untuk surat yang sudah diajukan --}}
    <form action="{{ route('surat.bulk_delete') }}" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat yang dipilih?');">
        @csrf
        @method('DELETE')
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded shadow">
                {{-- FIXED: Menambahkan kembali header dan body tabel --}}
                <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                    <tr>
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
                            <td class="px-4 py-2 border-b text-center">
                                <input type="checkbox" name="ids[]" value="{{ $surat->id }}" class="rounded surat-checkbox">
                            </td>
                            <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border-b font-medium">{{ $surat->judul }}</td>
                            <td class="px-4 py-2 border-b capitalize">{{ str_replace('_', ' ', $surat->jenis) }}</td>
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
                                <a href="{{ route('surat.preview', ['id' => $surat->id, 'view' => 'status']) }}" class="inline-flex items-center px-3 py-1 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                Tidak ada data surat yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>

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
