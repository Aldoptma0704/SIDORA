@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8" x-data="suratMasukAdmin()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0 text-primary-600">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Surat Masuk</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Kelola arsip surat masuk dari instansi lain dan kirimkan ke karyawan/pegawai Dinas Tenaga Kerja
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button x-show="selectedItems.length > 0" 
                            @click="showDeleteBulkConfirm = true" 
                            type="button" 
                            class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-md transition" x-cloak>
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Terpilih (<span x-text="selectedItems.length"></span>)
                    </button>
                    <button @click="openUploadModal = true" 
                            type="button"
                            class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-cloud-upload-alt mr-2 text-base"></i>
                        Upload Surat Masuk
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-3 text-green-800">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    <div>
                        <p class="font-semibold text-sm">Berhasil!</p>
                        <p class="text-xs text-green-700 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
                <div class="flex items-start space-x-3 text-red-800">
                    <i class="fas fa-exclamation-circle text-red-600 text-lg mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-sm">Terdapat kesalahan:</p>
                        <ul class="text-xs list-disc list-inside mt-1 text-red-700 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Overview Cards -->
        @php
            $totalCount = $surats->count();
            $terkirimCount = $surats->whereNotNull('disposisi_user_id')->count();
            $belumCount = $surats->whereNull('disposisi_user_id')->count();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Surat Masuk</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCount }}</p>
                </div>
                <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-lg"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Terkirim ke Pengguna</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $terkirimCount }}</p>
                </div>
                <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-paper-plane text-lg"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Belum Ditugaskan / Dikirim</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $belumCount }}</p>
                </div>
                <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 max-w-md relative">
                    <input type="text" 
                           x-model="search" 
                           placeholder="Cari perihal, nomor surat, instansi, atau nama/email tujuan..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                <div class="flex items-center space-x-3">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Status:</label>
                    <select x-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="all">Semua Status</option>
                        <option value="terkirim">Terkirim ke Pengguna</option>
                        <option value="belum">Belum Dikirim</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Surat Masuk -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @if($surats->isEmpty())
                <div class="text-center py-16 px-6">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class="fas fa-inbox text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Surat Masuk</h3>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                        Belum ada dokumen surat masuk dari instansi lain yang diunggah ke dalam sistem.
                    </p>
                    <button @click="openUploadModal = true" 
                            type="button" 
                            class="inline-flex items-center px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-xl shadow transition">
                        <i class="fas fa-plus mr-1.5"></i>
                        Upload Surat Pertama
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-4 text-left w-10">
                                    <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Asal Instansi & No. Surat</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perihal / Judul Surat</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Berkas</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tujuan Pengguna (Pegawai)</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($surats as $index => $surat)
                                @php
                                    $isSent = !empty($surat->disposisi_user_id);
                                    $penerimaName = $surat->disposisiKepada->name ?? '';
                                    $penerimaEmail = $surat->disposisiKepada->email ?? '';
                                    $penerimaBagian = $surat->disposisiKepada->bagian ?? '';
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors"
                                    x-show="(statusFilter === 'all' || (statusFilter === 'terkirim' && '{{ $isSent ? '1' : '0' }}' === '1') || (statusFilter === 'belum' && '{{ $isSent ? '1' : '0' }}' === '0')) &&
                                            (search === '' || 
                                             '{{ strtolower(addslashes($surat->judul)) }}'.includes(search.toLowerCase()) || 
                                             '{{ strtolower(addslashes($surat->nama_instansi ?? '')) }}'.includes(search.toLowerCase()) || 
                                             '{{ strtolower(addslashes($surat->nomor_surat ?? '')) }}'.includes(search.toLowerCase()) || 
                                             '{{ strtolower(addslashes($penerimaName)) }}'.includes(search.toLowerCase()) || 
                                             '{{ strtolower(addslashes($penerimaEmail)) }}'.includes(search.toLowerCase()))">
                                    
                                    <!-- Checkbox -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $surat->id }}"
                                            class="masuk-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            :checked="selectedItems.includes('{{ $surat->id }}')"
                                            @change="updateSelection()">
                                    </td>

                                    <!-- No -->
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-gray-500">
                                        {{ $index + 1 }}
                                    </td>

                                    <!-- Asal Instansi & No. Surat -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $surat->nama_instansi ?: 'Instansi Luar' }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1.5">
                                            <i class="fas fa-barcode text-gray-400"></i>
                                            <span>{{ $surat->nomor_surat ?: '-' }}</span>
                                        </div>
                                    </td>

                                    <!-- Perihal / Judul Surat -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-800 line-clamp-2">
                                            {{ $surat->judul }}
                                        </div>
                                        @if($surat->sifat)
                                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-600 rounded">
                                                Sifat: {{ $surat->sifat }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Berkas -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($surat->file_path)
                                            <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition">
                                                <i class="fas fa-file-pdf mr-1 text-red-500"></i>
                                                Lihat Berkas
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada berkas</span>
                                        @endif
                                    </td>

                                    <!-- Tujuan Pengguna (Pegawai) -->
                                    <td class="px-6 py-4">
                                        @if($surat->disposisiKepada)
                                            <div class="flex items-start space-x-2.5">
                                                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ strtoupper(substr($surat->disposisiKepada->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-gray-900 leading-tight">
                                                        {{ $surat->disposisiKepada->name }}
                                                    </p>
                                                    <p class="text-[11px] text-gray-500 leading-tight mt-0.5">
                                                        {{ $surat->disposisiKepada->email }}
                                                    </p>
                                                    @if($surat->disposisiKepada->bagian)
                                                        <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                                                            {{ $surat->disposisiKepada->bagian }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-start gap-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-full">
                                                    <i class="fas fa-clock mr-1 text-[10px]"></i>
                                                    Belum Dikirim
                                                </span>
                                                <button @click="openKirimModal = true; selectedSuratId = {{ $surat->id }}; selectedSuratJudul = '{{ addslashes($surat->judul) }}'" 
                                                        class="text-[11px] text-primary-600 hover:text-primary-800 font-bold hover:underline mt-0.5">
                                                    + Kirim ke Karyawan
                                                </button>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Tanggal Masuk -->
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                        <div class="font-medium text-gray-900">
                                            {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d F Y') : '-' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $surat->created_at ? $surat->created_at->format('H:i') . ' WIB' : '' }}
                                        </div>
                                    </td>

                                    <!-- Aksi (Lihat, Kirim, Hapus - Tak Ada Edit) -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <!-- Lihat Detail -->
                                            <a href="{{ route('admin.surat.masuk.lihat', $surat->id) }}" 
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                               title="Lihat Detail Surat">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>

                                            <!-- Kirim / Teruskan ke Pegawai -->
                                            <button @click="openKirimModal = true; selectedSuratId = {{ $surat->id }}; selectedSuratJudul = '{{ addslashes($surat->judul) }}'" 
                                                    type="button" 
                                                    class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" 
                                                    title="{{ $surat->disposisiKepada ? 'Teruskan / Ganti Penerima' : 'Kirim ke Karyawan' }}">
                                                <i class="fas fa-paper-plane text-sm"></i>
                                            </button>

                                            <!-- Hapus Surat -->
                                            <form action="{{ route('admin.surat.masuk.destroy', $surat->id) }}" 
                                                  method="POST" 
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        @click="confirmSingleDelete($el.closest('form'))"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" 
                                                        title="Hapus Surat Masuk">
                                                    <i class="fas fa-trash-alt text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- Standalone Bulk Delete Form --}}
    <form action="{{ route('admin.surat.masuk.bulk-destroy') }}" method="POST" id="bulkDeleteMasukForm">
        @csrf
        @method('DELETE')
        <template x-for="id in selectedItems" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    {{-- Bulk Delete Confirmation Modal --}}
    <div x-show="showDeleteBulkConfirm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showDeleteBulkConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Hapus Surat Masuk Terpilih?</h3>
            <p class="text-sm text-gray-600 mt-2 mb-1">Akan menghapus <span class="font-bold text-red-600" x-text="selectedItems.length"></span> surat masuk.</p>
            <p class="text-xs text-gray-500 mb-6">Berkas arsip fisik dan data akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button @click="showDeleteBulkConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                <button @click="submitBulkDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow transition">Hapus Semua</button>
            </div>
        </div>
    </div>

    {{-- Single Delete Confirmation Modal --}}
    <div x-show="showDeleteSingleConfirm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showDeleteSingleConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Hapus Surat Masuk Ini?</h3>
            <p class="text-sm text-gray-600 mt-2 mb-6">Berkas arsip fisik dan data akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="showDeleteSingleConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                <button @click="submitSingleDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow transition">Hapus</button>
            </div>
        </div>
    </div>

    <!-- MODAL 1: UPLOAD SURAT MASUK DARI INSTANSI LAIN -->
    <div x-show="openUploadModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto" 
         x-cloak>
        
        <div @click.away="openUploadModal = false" 
             class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 sm:p-8 my-8 text-left border border-gray-100 transform transition-all">
            
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center font-bold">
                        <i class="fas fa-file-import text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Upload Surat Masuk</h3>
                        <p class="text-xs text-gray-500">Arsipkan berkas surat dari instansi lain dan tentukan penerimanya</p>
                    </div>
                </div>
                <button @click="openUploadModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.surat.masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    
                    <!-- Asal Instansi Pengirim -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Asal Instansi / Pengirim <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_instansi" 
                               placeholder="Contoh: Kementerian Ketenagakerjaan RI, BPJS Ketenagakerjaan, PT XYZ" 
                               class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition" 
                               required>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nomor Surat -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nomor Surat
                            </label>
                            <input type="text" 
                                   name="nomor_surat" 
                                   placeholder="Contoh: 005/124/DISNAKER/2026" 
                                   class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition">
                        </div>

                        <!-- Sifat Surat -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Sifat Surat
                            </label>
                            <select name="sifat" class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition">
                                <option value="Biasa">Biasa</option>
                                <option value="Penting">Penting</option>
                                <option value="Segera">Segera</option>
                                <option value="Rahasia">Rahasia</option>
                            </select>
                        </div>
                    </div>

                    <!-- Perihal / Judul Surat -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Perihal / Judul Surat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               placeholder="Contoh: Undangan Rapat Koordinasi Pengawasan Ketenagakerjaan Wilayah" 
                               class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition" 
                               required>
                    </div>

                    <!-- Berkas File Surat -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Upload Berkas Surat (PDF/Dokumen) <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               name="file_surat" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                               class="w-full px-3.5 py-2 border-2 border-dashed border-gray-300 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition" 
                               required>
                        <p class="text-[11px] text-gray-500 mt-1">Mendukung format PDF, DOCX, JPG, PNG (Maksimal 10MB).</p>
                    </div>

                    <!-- Tujuan Pengguna (Pegawai, Pimpinan, Kadis) -->
                    <div class="pt-2 border-t border-gray-100">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Kirim ke Tujuan Pengguna (Pegawai / Pimpinan / Kadis)
                        </label>
                        <select name="pegawai_id" class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition">
                            <option value="">-- Pilih Penerima (Pegawai / Pimpinan / Kadis - Dapat dikirim nanti) --</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}">
                                    {{ $pegawai->name }} — {{ $pegawai->email }} ({{ $pegawai->position ?: ($pegawai->bagian ?: ucfirst($pegawai->role)) }} - {{ ucfirst($pegawai->role) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                            <i class="fas fa-info-circle text-primary-500"></i>
                            Pilihan mencakup seluruh Pegawai, Kepala Bidang/UPTD, Kepala Sekretariat, dan Kepala Dinas.
                        </p>
                    </div>

                    <!-- Keterangan Tambahan / Isi Ringkas -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Keterangan / Catatan Tambahan (Opsional)
                        </label>
                        <textarea name="isi" 
                                  rows="3" 
                                  placeholder="Catatan ringkas atau instruksi khusus terkait surat ini..." 
                                  class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition resize-none"></textarea>
                    </div>

                </div>

                <div class="flex justify-end items-center gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button @click="openUploadModal = false" 
                            type="button" 
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                        <i class="fas fa-upload mr-1.5"></i> Simpan & Unggah
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- MODAL 2: KIRIM / TERUSKAN SURAT KE TUJUAN PENGGUNA -->
    <div x-show="openKirimModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
         x-cloak>
        
        <div @click.away="openKirimModal = false" 
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left border border-gray-100 transform transition-all">
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                        <i class="fas fa-paper-plane text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Kirim Surat ke Pengguna</h3>
                        <p class="text-xs text-gray-500">Pilih tujuan (Pegawai, Pimpinan, atau Kepala Dinas)</p>
                    </div>
                </div>
                <button @click="openKirimModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-xs text-gray-500 font-medium">Perihal Surat:</p>
                <p class="text-sm font-bold text-gray-800 mt-0.5 line-clamp-2" x-text="selectedSuratJudul"></p>
            </div>

            <form :action="'/admin/surat/masuk/' + selectedSuratId + '/kirim'" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Pilih Tujuan Pengguna: <span class="text-red-500">*</span>
                    </label>
                    <select name="pegawai_id" class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition" required>
                        <option value="">-- Pilih Tujuan (Pegawai / Pimpinan / Kadis) --</option>
                        @foreach($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}">
                                {{ $pegawai->name }} — {{ $pegawai->email }} ({{ $pegawai->position ?: ($pegawai->bagian ?: ucfirst($pegawai->role)) }} - {{ ucfirst($pegawai->role) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-500 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle text-emerald-500"></i>
                        Surat akan langsung muncul di halaman menu <strong>Surat Masuk</strong> pada akun pengguna terkait.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button @click="openKirimModal = false" type="button" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md">
                        <i class="fas fa-paper-plane mr-1.5"></i> Kirim Surat
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

@push('scripts')
<script>
    function suratMasukAdmin() {
        return {
            openUploadModal: false, 
            openKirimModal: false, 
            selectedSuratId: null, 
            selectedSuratJudul: '',
            search: '',
            statusFilter: 'all',
            selectedItems: [],
            selectAll: false,
            showDeleteBulkConfirm: false,
            showDeleteSingleConfirm: false,
            deleteSingleForm: null,
            toggleSelectAll() {
                this.selectAll = !this.selectAll;
                const checkboxes = document.querySelectorAll('.masuk-checkbox');
                if (this.selectAll) {
                    this.selectedItems = Array.from(checkboxes).map(function(cb) { return cb.value; });
                    checkboxes.forEach(function(cb) { cb.checked = true; });
                } else {
                    this.selectedItems = [];
                    checkboxes.forEach(function(cb) { cb.checked = false; });
                }
            },
            updateSelection() {
                const checked = document.querySelectorAll('.masuk-checkbox:checked');
                const all = document.querySelectorAll('.masuk-checkbox');
                this.selectedItems = Array.from(checked).map(function(cb) { return cb.value; });
                this.selectAll = (all.length > 0 && checked.length === all.length);
            },
            confirmSingleDelete(formEl) {
                this.deleteSingleForm = formEl;
                this.showDeleteSingleConfirm = true;
            },
            submitSingleDelete() {
                if (this.deleteSingleForm) {
                    this.deleteSingleForm.submit();
                }
            },
            submitBulkDelete() {
                const form = document.getElementById('bulkDeleteMasukForm');
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
