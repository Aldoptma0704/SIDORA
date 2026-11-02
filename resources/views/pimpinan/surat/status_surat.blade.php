@extends('layouts.app')

@section('content')
<div x-data="statusSuratPimpinan()" class="space-y-6">

    {{-- Alert Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                    <p class="font-medium text-sm">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                    <p class="font-medium text-sm">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Status Surat Balasan</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola dan pantau seluruh status surat balasan yang Anda buat</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                <i class="fas fa-layer-group mr-1.5"></i> Total Surat: <strong class="ml-1 text-blue-900">{{ $totalSurat }}</strong>
            </span>
        </div>
    </div>

    {{-- Enhanced Statistics Cards (4 Status) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- 1. Draft --}}
        <a href="{{ route('pimpinan.status-surat', ['tab' => 'draft']) }}" class="block relative overflow-hidden bg-gradient-to-br from-amber-50 to-yellow-100 rounded-2xl shadow-sm border border-amber-200 hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Draft</p>
                        <p class="text-3xl font-extrabold text-amber-900 mt-1">{{ $jumlahDraft }}</p>
                        <p class="text-[11px] text-amber-600 mt-1">Belum diajukan</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-500 text-white rounded-xl shadow-md flex items-center justify-center">
                        <i class="fas fa-file-edit text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-amber-500"></div>
        </a>

        {{-- 2. Menunggu --}}
        <a href="{{ route('pimpinan.status-surat', ['tab' => 'menunggu']) }}" class="block relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-sm border border-blue-200 hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Menunggu</p>
                        <p class="text-3xl font-extrabold text-blue-900 mt-1">{{ $jumlahMenunggu }}</p>
                        <p class="text-[11px] text-blue-600 mt-1">Dalam proses verifikasi</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-xl shadow-md flex items-center justify-center">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-blue-600"></div>
        </a>

        {{-- 3. Disetujui --}}
        <a href="{{ route('pimpinan.status-surat', ['tab' => 'disetujui']) }}" class="block relative overflow-hidden bg-gradient-to-br from-emerald-50 to-green-100 rounded-2xl shadow-sm border border-emerald-200 hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Disetujui</p>
                        <p class="text-3xl font-extrabold text-emerald-900 mt-1">{{ $jumlahDisetujui }}</p>
                        <p class="text-[11px] text-emerald-600 mt-1">Disetujui sepenuhnya</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-600 text-white rounded-xl shadow-md flex items-center justify-center">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-emerald-600"></div>
        </a>

        {{-- 4. Ditolak --}}
        <a href="{{ route('pimpinan.status-surat', ['tab' => 'ditolak']) }}" class="block relative overflow-hidden bg-gradient-to-br from-rose-50 to-red-100 rounded-2xl shadow-sm border border-rose-200 hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">Ditolak</p>
                        <p class="text-3xl font-extrabold text-rose-900 mt-1">{{ $jumlahDitolak }}</p>
                        <p class="text-[11px] text-rose-600 mt-1">Perlu perbaikan</p>
                    </div>
                    <div class="w-12 h-12 bg-rose-600 text-white rounded-xl shadow-md flex items-center justify-center">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-rose-600"></div>
        </a>
    </div>

    {{-- Action Bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Tombol Buat Surat Balasan Baru --}}
                <a href="{{ route('pimpinan.balasan') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-sm rounded-xl shadow-md hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Surat Balasan Baru
                </a>

                {{-- Tombol Pengajuan (sama seperti pegawai) --}}
                <button @click="showModal = true" type="button" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white font-semibold text-sm rounded-xl shadow-md hover:from-emerald-700 hover:to-green-700 transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Pengajuan
                    @if($drafts->count() > 0)
                        <span class="ml-2 px-2 py-0.5 bg-white text-emerald-800 text-xs font-bold rounded-full">{{ $drafts->count() }}</span>
                    @endif
                </button>
                
                {{-- Tombol Hapus Terpilih --}}
                <button x-show="selectedItems.length > 0" @click="showDeleteConfirm = true" type="button" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 text-white font-semibold text-sm rounded-xl shadow-md hover:from-red-700 hover:to-rose-700 transition-all duration-200" x-cloak>
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Terpilih (<span x-text="selectedItems.length"></span>)
                </button>
            </div>

            {{-- Form Pencarian --}}
            <form action="{{ route('pimpinan.status-surat') }}" method="GET" class="relative">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari surat..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab Filter Navigasi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-2">
        <nav class="flex flex-wrap gap-2">
            <a href="{{ route('pimpinan.status-surat', ['tab' => 'semua']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab === 'semua' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-layer-group mr-1.5"></i> Semua Surat
                <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full {{ $tab === 'semua' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $totalSurat }}</span>
            </a>
            <a href="{{ route('pimpinan.status-surat', ['tab' => 'draft']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab === 'draft' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-file-edit mr-1.5"></i> Draft
                <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full {{ $tab === 'draft' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $jumlahDraft }}</span>
            </a>
            <a href="{{ route('pimpinan.status-surat', ['tab' => 'menunggu']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab === 'menunggu' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-clock mr-1.5"></i> Menunggu
                <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full {{ $tab === 'menunggu' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $jumlahMenunggu }}</span>
            </a>
            <a href="{{ route('pimpinan.status-surat', ['tab' => 'disetujui']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab === 'disetujui' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-check-circle mr-1.5"></i> Disetujui
                <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full {{ $tab === 'disetujui' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $jumlahDisetujui }}</span>
            </a>
            <a href="{{ route('pimpinan.status-surat', ['tab' => 'ditolak']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab === 'ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-times-circle mr-1.5"></i> Ditolak
                <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full {{ $tab === 'ditolak' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $jumlahDitolak }}</span>
            </a>
        </nav>
    </div>

    {{-- Enhanced Modal Pengajuan (Pilih Draft untuk Diajukan) --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Pengajuan Surat Balasan</h3>
                        <p class="text-xs text-gray-600 mt-0.5">Pilih satu atau beberapa draft surat untuk diajukan ke tahap verifikasi penandatanganan</p>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <form action="{{ route('pimpinan.surat.ajukan') }}" method="POST">
                @csrf
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="space-y-3">
                        @forelse ($drafts as $draft)
                            <label class="flex items-center p-4 rounded-xl border-2 border-gray-200 hover:border-blue-400 hover:bg-blue-50/50 cursor-pointer transition-all duration-200 group">
                                <input type="checkbox" name="ids[]" value="{{ $draft->id }}" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-gray-900 group-hover:text-blue-900">{{ $draft->judul }}</span>
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ $draft->created_at ? $draft->created_at->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <span class="font-medium text-gray-700">Tujuan:</span> {{ Str::limit($draft->tujuan, 50) }}
                                        &bull; <span class="text-purple-600 font-semibold">{{ $draft->penandatangans->count() }} Penandatangan</span>
                                    </p>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-gray-400 mb-3">
                                    <i class="fas fa-file-alt text-2xl"></i>
                                </div>
                                <p class="text-base font-semibold text-gray-700">Tidak ada draft surat yang tersedia</p>
                                <p class="text-xs text-gray-500 mt-1">Buat surat balasan baru terlebih dahulu untuk mengajukan permohonan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <div class="p-5 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 bg-white text-gray-700 text-xs font-semibold border border-gray-300 rounded-xl hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit" :disabled="{{ $drafts->isEmpty() ? 'true' : 'false' }}" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-bold rounded-xl hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 transition shadow-md">
                        <i class="fas fa-paper-plane mr-1.5"></i> Ajukan Surat Terpilih
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteConfirm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showDeleteConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="text-center">
                <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Konfirmasi Penghapusan</h3>
                <p class="text-xs text-gray-600 mt-2">Apakah Anda yakin ingin menghapus surat terpilih? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="mt-6 flex gap-3">
                    <button @click="showDeleteConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button @click="submitBulkDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 shadow-md transition">
                        Hapus Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Single Delete Confirmation Modal --}}
    <div x-show="showDeleteSingleConfirm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showDeleteSingleConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="text-center">
                <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Hapus Draft Ini?</h3>
                <p class="text-xs text-gray-600 mt-2">Apakah Anda yakin ingin menghapus draft surat ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="mt-6 flex gap-3">
                    <button @click="showDeleteSingleConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button @click="submitSingleDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 shadow-md transition">
                        Hapus Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Standalone Bulk Delete Form --}}
    <form action="{{ route('pimpinan.surat.bulk-delete') }}" method="POST" id="bulkDeleteForm">
        @csrf
        @method('DELETE')
        <template x-for="id in selectedItems" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    {{-- Table Surat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3.5 text-left w-12">
                            <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Judul &amp; Perihal</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($surats as $index => $surat)
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="px-6 py-4">
                                @if ($surat->status !== 'menunggu')
                                    <input type="checkbox" value="{{ $surat->id }}"
                                           class="surat-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           :checked="selectedItems.includes('{{ $surat->id }}')"
                                           @change="updateSelection()">
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                                <td class="px-6 py-4 text-xs font-semibold text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-gray-900">{{ $surat->judul }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $surat->nomor_surat ? 'No: ' . $surat->nomor_surat : 'Nomor belum di-generate' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-700">
                                    {{ Str::limit($surat->tujuan, 35) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                        {{ $surat->jenis === 'keluar_full' ? 'Kop Lengkap' : 'Template' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($surat->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <i class="fas fa-file-edit mr-1"></i> Draft
                                        </span>
                                    @elseif ($surat->status === 'menunggu')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            <i class="fas fa-clock mr-1"></i> Menunggu
                                        </span>
                                    @elseif ($surat->status === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fas fa-check-circle mr-1"></i> Disetujui
                                        </span>
                                    @elseif ($surat->status === 'ditolak')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <i class="fas fa-times-circle mr-1"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($surat->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Lihat Detail / Preview --}}
                                        <a href="{{ route('pimpinan.surat.preview', $surat->id) }}?from=status" 
                                           class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100/60 rounded-lg transition" title="Lihat Detail Surat">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>

                                        {{-- Unduh PDF (jika disetujui) --}}
                                        @if ($surat->status === 'disetujui')
                                            <a href="{{ route('pimpinan.surat.download', $surat->id) }}" 
                                               class="p-2 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100/60 rounded-lg transition" title="Unduh Berkas PDF">
                                                <i class="fas fa-file-pdf text-sm"></i>
                                            </a>
                                        @endif

                                        {{-- Edit jika Draft --}}
                                        @if ($surat->status === 'draft')
                                            <a href="{{ route('pimpinan.surat.edit', $surat->id) }}" 
                                               class="p-2 text-amber-600 hover:text-amber-800 hover:bg-amber-100/60 rounded-lg transition" title="Edit Draft">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                        @endif

                                        {{-- Hapus jika bukan menunggu --}}
                                        @if ($surat->status !== 'menunggu')
                                            <form action="{{ route('pimpinan.surat.destroy', $surat->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="confirmSingleDelete($el.closest('form'))" class="p-2 text-rose-600 hover:text-rose-800 hover:bg-rose-100/60 rounded-lg transition" title="Hapus Surat">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-gray-400 mb-3">
                                        <i class="fas fa-inbox text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">Tidak ada surat balasan pada kategori ini</p>
                                    <p class="text-xs text-gray-500 mt-1">Gunakan tombol "Buat Surat Balasan Baru" untuk membuat surat</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function statusSuratPimpinan() {
            return {
                showModal: false,
                showSearch: false,
                searchTerm: '',
                selectedItems: [],
                selectAll: false,
                showDeleteConfirm: false,
                showDeleteSingleConfirm: false,
                deleteSingleForm: null,
                toggleSelectAll() {
                    this.selectAll = !this.selectAll;
                    const checkboxes = document.querySelectorAll('.surat-checkbox');
                    if (this.selectAll) {
                        this.selectedItems = Array.from(checkboxes).map(function(cb) { return cb.value; });
                        checkboxes.forEach(function(cb) { cb.checked = true; });
                    } else {
                        this.selectedItems = [];
                        checkboxes.forEach(function(cb) { cb.checked = false; });
                    }
                },
                updateSelection() {
                    const checked = document.querySelectorAll('.surat-checkbox:checked');
                    const all = document.querySelectorAll('.surat-checkbox');
                    this.selectedItems = Array.from(checked).map(function(cb) { return cb.value; });
                    this.selectAll = (all.length > 0 && checked.length === all.length);
                },
                confirmSingleDelete(formEl) {
                    this.deleteSingleForm = formEl;
                    this.showDeleteSingleConfirm = true;
                },
                submitSingleDelete() {
                    if (this.deleteSingleForm) this.deleteSingleForm.submit();
                },
                submitBulkDelete() {
                    const form = document.getElementById('bulkDeleteForm');
                    form.querySelectorAll('input[name="ids[]"]').forEach(function(el) { el.remove(); });
                    this.selectedItems.forEach(function(id) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    form.submit();
                }
            };
        }
    </script>
    @endpush
@endsection
