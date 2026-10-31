@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-800 p-6 mb-8 rounded-xl shadow-lg animate-fade-in">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 text-red-800 p-6 mb-8 rounded-xl shadow-lg animate-fade-in">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-bold text-lg mb-2">Terjadi Kesalahan</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div x-data="suratIndexPegawai()">
            {{-- Header Section --}}
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 mb-8 border border-gray-100">
                @php
                    $title = match($jenis ?? null) {
                        'masuk'   => 'Daftar Surat Masuk',
                        'keluar'  => 'Daftar Surat Keluar',
                        default   => 'Daftar Semua Surat',
                    };

                    $subtitle = match($jenis ?? null) {
                        'masuk'   => 'Kelola dan pantau surat masuk serta disposisi untuk Anda',
                        'keluar'  => 'Kelola dan buat draf atau permohonan surat keluar Anda',
                        default   => 'Kelola dan pantau seluruh arsip surat masuk dan keluar Anda',
                    };
                    
                    $iconClass = match($jenis ?? null) {
                        'masuk'   => 'fas fa-inbox text-blue-600',
                        'keluar'  => 'fas fa-paper-plane text-emerald-600',
                        default   => 'fas fa-layer-group text-purple-600',
                    };

                    $iconBg = match($jenis ?? null) {
                        'masuk'   => 'from-blue-100 to-indigo-100',
                        'keluar'  => 'from-emerald-100 to-teal-100',
                        default   => 'from-purple-100 to-blue-100',
                    };
                @endphp

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3.5 bg-gradient-to-br {{ $iconBg }} rounded-2xl shadow-sm">
                            <i class="{{ $iconClass }} text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                                {{ $title }}
                            </h1>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $subtitle }}</p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-3">
                        @if($jenis === 'masuk')
                            {{-- Hanya Tombol Upload PDF untuk Surat Masuk --}}
                            <button @click="showModal = true" type="button" 
                                    class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium text-sm rounded-xl shadow-md hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-file-upload mr-2 group-hover:rotate-12 transition-transform"></i> 
                                Upload PDF
                            </button>
                        @elseif($jenis === 'keluar')
                            {{-- Hanya Tombol Buat Surat Baru untuk Surat Keluar --}}
                            <a href="{{ route('pegawai.surat.create') }}" 
                               class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium text-sm rounded-xl shadow-md hover:from-emerald-700 hover:to-teal-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i> 
                                Buat Surat Baru
                            </a>
                        @else
                            {{-- Menu Semua Surat: Muncul KEDUA Tombol --}}
                            <button @click="showModal = true" type="button" 
                                    class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium text-sm rounded-xl shadow-md hover:from-purple-700 hover:to-indigo-700 transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-file-upload mr-2 group-hover:rotate-12 transition-transform"></i> 
                                Upload PDF
                            </button>
                            <a href="{{ route('pegawai.surat.create') }}" 
                               class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium text-sm rounded-xl shadow-md hover:from-blue-700 hover:to-blue-800 transform hover:-translate-y-0.5 transition-all duration-200">
                                <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i> 
                                Buat Surat Baru
                            </a>
                        @endif
                        {{-- Hapus Terpilih --}}
                        <button x-show="selectedItems.length > 0" @click="showDeleteBulkConfirm = true" type="button"
                                class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 text-white font-medium text-sm rounded-xl shadow-md hover:from-red-700 hover:to-rose-700 transform hover:-translate-y-0.5 transition-all duration-200" x-cloak>
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Terpilih (<span x-text="selectedItems.length"></span>)
                        </button>
                    </div>
                </div>

                {{-- Tab Navigation --}}
                <div class="mt-6 flex flex-wrap gap-2 p-1.5 bg-gray-100/80 rounded-xl w-fit">
                    <a href="{{ route('pegawai.surat.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ empty($jenis) ? 'bg-white text-purple-700 shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200/60' }}">
                        <i class="fas fa-list text-xs"></i>
                        <span>Semua Surat</span>
                        @if(isset($countSemua))
                            <span class="ml-1 text-xs px-2 py-0.5 rounded-full {{ empty($jenis) ? 'bg-purple-100 text-purple-700 font-bold' : 'bg-gray-200 text-gray-700' }}">{{ $countSemua }}</span>
                        @endif
                    </a>

                    <a href="{{ route('pegawai.surat.index', ['jenis' => 'masuk']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ $jenis === 'masuk' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200/60' }}">
                        <i class="fas fa-inbox text-xs"></i>
                        <span>Surat Masuk</span>
                        @if(isset($countMasuk))
                            <span class="ml-1 text-xs px-2 py-0.5 rounded-full {{ $jenis === 'masuk' ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-200 text-gray-700' }}">{{ $countMasuk }}</span>
                        @endif
                    </a>

                    <a href="{{ route('pegawai.surat.index', ['jenis' => 'keluar']) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ $jenis === 'keluar' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200/60' }}">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Surat Keluar</span>
                        @if(isset($countKeluar))
                            <span class="ml-1 text-xs px-2 py-0.5 rounded-full {{ $jenis === 'keluar' ? 'bg-emerald-100 text-emerald-700 font-bold' : 'bg-gray-200 text-gray-700' }}">{{ $countKeluar }}</span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- Modal Upload PDF --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
                <div @click.away="showModal = false" class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-purple-100 to-blue-100 rounded-xl mr-3">
                                <i class="fas fa-file-upload text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Upload Dokumen PDF</h3>
                                <p class="text-xs text-gray-500">Unggah berkas arsip surat digital</p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <form action="{{ route('pegawai.surat.upload-pdf') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if(empty($jenis))
                            {{-- Jika di menu Semua Surat, beri opsi jenis dokumen --}}
                            <div class="mb-4">
                                <label for="upload_jenis" class="block text-sm font-semibold text-gray-700 mb-2">Kategori Surat</label>
                                <select name="jenis" id="upload_jenis" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                    <option value="masuk" selected>📥 Surat Masuk</option>
                                    <option value="keluar">📤 Surat Keluar</option>
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="jenis" value="{{ $jenis }}">
                        @endif
                        
                        <div class="mb-6">
                            <label for="file_surat" class="block text-sm font-semibold text-gray-700 mb-2">Pilih File PDF</label>
                            <div class="relative">
                                <input type="file" name="file_surat" id="file_surat" accept=".pdf" required 
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:transition-all border-2 border-dashed border-gray-300 rounded-xl p-4 hover:border-blue-400 transition-colors">
                            </div>
                            <div class="flex items-center mt-2.5 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1.5 text-blue-500"></i>
                                <span>Format file: <strong>.PDF</strong> • Ukuran maksimal: <strong>5MB</strong></span>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showModal = false" 
                                    class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-md">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-4 py-4 text-left w-10">
                                    <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Judul Surat</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal Surat</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis</th>
                                @if($jenis !== 'masuk')
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                @endif
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($surats as $index => $surat)
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 transition-all duration-200 group">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($surat->status !== 'menunggu')
                                            <input type="checkbox" value="{{ $surat->id }}"
                                                class="surat-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                :checked="selectedItems.includes('{{ $surat->id }}')"
                                                @change="updateSelection()">
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full text-xs font-bold text-gray-700 group-hover:bg-blue-100 group-hover:text-blue-700 transition-colors">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                {{ $surat->judul }}
                                            </div>
                                            @if($surat->nomor_surat)
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    No: {{ $surat->nomor_surat }}
                                                </div>
                                            @endif
                                            @if($surat->disposisi_user_id && $surat->disposisi_user_id == auth()->id())
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                                                        <i class="fas fa-user-check mr-1 text-[10px]"></i>
                                                        Disposisi untuk Anda
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="font-medium text-gray-800">
                                            {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d F Y') : '-' }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $surat->created_at ? $surat->created_at->format('H:i') . ' WIB' : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $jenisDisplay = match($surat->jenis) {
                                                'masuk' => 'Surat Masuk',
                                                'keluar' => 'Keluar (Template)',
                                                'keluar_full' => 'Keluar (Kop Surat)',
                                                default => ucfirst($surat->jenis)
                                            };
                                            
                                            $jenisIcon = match($surat->jenis) {
                                                'masuk' => 'fas fa-inbox text-blue-500',
                                                'keluar' => 'fas fa-paper-plane text-emerald-500',
                                                'keluar_full' => 'fas fa-file-signature text-purple-500',
                                                default => 'fas fa-file-alt text-gray-400'
                                            };
                                        @endphp
                                        <div class="flex items-center">
                                            <i class="{{ $jenisIcon }} mr-2"></i>
                                            <span class="text-sm text-gray-700 font-medium">{{ $jenisDisplay }}</span>
                                        </div>
                                    </td>
                                    @if($jenis !== 'masuk')
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($surat->jenis === 'masuk')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                    <i class="fas fa-check-double mr-1"></i>
                                                    Resmi
                                                </span>
                                            @else
                                                @php
                                                    $statusConfig = match($surat->status) {
                                                        'disetujui' => [
                                                            'class' => 'from-green-100 to-emerald-100 text-green-800 border-green-200',
                                                            'icon' => 'fas fa-check-circle',
                                                            'label' => 'Disetujui'
                                                        ],
                                                        'ditolak' => [
                                                            'class' => 'from-red-100 to-pink-100 text-red-800 border-red-200',
                                                            'icon' => 'fas fa-times-circle',
                                                            'label' => 'Ditolak'
                                                        ],
                                                        'draft' => [
                                                            'class' => 'from-gray-100 to-slate-100 text-gray-800 border-gray-200',
                                                            'icon' => 'fas fa-edit',
                                                            'label' => 'Draft'
                                                        ],
                                                        default => [
                                                            'class' => 'from-yellow-100 to-orange-100 text-yellow-800 border-yellow-200',
                                                            'icon' => 'fas fa-clock',
                                                            'label' => 'Menunggu'
                                                        ]
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r {{ $statusConfig['class'] }} border">
                                                    <i class="{{ $statusConfig['icon'] }} mr-1"></i>
                                                    {{ $statusConfig['label'] }}
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- Tombol Lihat/Preview --}}
                                            @if ($surat->file_path)
                                                <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" 
                                                   class="group inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 rounded-lg shadow-sm hover:shadow transform hover:scale-105 transition-all duration-200">
                                                    <i class="fas fa-file-pdf mr-1.5 text-red-300"></i> 
                                                    Lihat PDF
                                                </a>
                                            @else
                                                <a href="{{ route('pegawai.surat.preview', $surat->id) }}" 
                                                   class="group inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-lg shadow-sm hover:shadow transform hover:scale-105 transition-all duration-200">
                                                    <i class="fas fa-eye mr-1.5 group-hover:scale-110 transition-transform"></i> 
                                                    Pratinjau
                                                </a>
                                            @endif

                                            {{-- Tombol Edit hanya untuk draft --}}
                                            @if ($surat->status == 'draft' && $surat->user_id == auth()->id())
                                                <a href="{{ route('pegawai.surat.edit', $surat->id) }}" 
                                                   title="Edit Surat"
                                                   class="group inline-flex items-center px-2.5 py-1.5 text-xs text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 rounded-lg shadow-sm hover:shadow transform hover:scale-105 transition-all duration-200">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            @endif

                                            {{-- Tombol Hapus untuk surat selain menunggu --}}
                                            @if ($surat->status !== 'menunggu')
                                                <form action="{{ route('pegawai.surat.destroy', $surat->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            title="Hapus Surat"
                                                            @click="confirmSingleDelete($el.closest('form'))"
                                                            class="group inline-flex items-center px-2.5 py-1.5 text-xs text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 rounded-lg shadow-sm hover:shadow transform hover:scale-105 transition-all duration-200">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $jenis === 'masuk' ? 6 : 7 }}" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-700 mb-1">
                                                Belum Ada {{ $jenis === 'masuk' ? 'Surat Masuk' : ($jenis === 'keluar' ? 'Surat Keluar' : 'Surat') }}
                                            </h3>
                                            <p class="text-gray-500 text-sm max-w-sm mb-4">
                                                @if($jenis === 'masuk')
                                                    Belum ada surat masuk atau disposisi yang terdaftar untuk akun Anda.
                                                @elseif($jenis === 'keluar')
                                                    Mulai buat permohonan surat keluar baru menggunakan template resmi.
                                                @else
                                                    Mulai dengan membuat surat baru atau mengunggah file surat PDF.
                                                @endif
                                            </p>
                                            @if($jenis === 'masuk')
                                                <button @click="showModal = true" type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                                    <i class="fas fa-file-upload mr-2"></i> Upload PDF Masuk
                                                </button>
                                            @elseif($jenis === 'keluar')
                                                <a href="{{ route('pegawai.surat.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                                                    <i class="fas fa-plus mr-2"></i> Buat Surat Baru
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Hidden Bulk Delete Form --}}
            <form action="{{ route('pegawai.surat.bulk-delete') }}" method="POST" id="bulkDeleteSuratIndexForm">
                @csrf
                @method('DELETE')
                <template x-for="id in selectedItems" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
            </form>

            {{-- Single Delete Confirm Modal --}}
            <div x-show="showDeleteSingleConfirm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
                <div @click.away="showDeleteSingleConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                    <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Hapus Surat Ini?</h3>
                    <p class="text-sm text-gray-600 mt-2 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex gap-3">
                        <button @click="showDeleteSingleConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                        <button @click="submitSingleDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow transition">Hapus</button>
                    </div>
                </div>
            </div>

            {{-- Bulk Delete Confirm Modal --}}
            <div x-show="showDeleteBulkConfirm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
                <div @click.away="showDeleteBulkConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                    <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Hapus Surat Terpilih?</h3>
                    <p class="text-sm text-gray-600 mt-2 mb-1">Akan menghapus <span class="font-bold text-red-600" x-text="selectedItems.length"></span> surat.</p>
                    <p class="text-xs text-gray-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex gap-3">
                        <button @click="showDeleteBulkConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                        <button @click="submitBulkDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow transition">Hapus Semua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>

@push('scripts')
<script>
    function suratIndexPegawai() {
        return {
            showModal: false,
            selectedItems: [],
            selectAll: false,
            showDeleteBulkConfirm: false,
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
                const form = document.getElementById('bulkDeleteSuratIndexForm');
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