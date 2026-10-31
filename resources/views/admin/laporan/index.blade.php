@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="laporanSuratAdmin()">
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-lg"></i>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-chart-line mr-3 text-blue-600"></i>
                    Laporan Aktivitas Surat &amp; Penomoran
                </h1>
                <p class="mt-2 text-sm text-gray-600">Monitor surat yang disetujui pimpinan dan generate nomor surat resmi (Pergub Lampung No. 14 &amp; 16 Th 2023)</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center space-x-4 text-sm">
                    <div class="bg-blue-50 px-3 py-1.5 rounded-full border border-blue-200">
                        <i class="fas fa-file-alt text-blue-600 mr-1.5"></i>
                        <span class="text-blue-800 font-semibold">Total Surat Masuk: {{ $surats->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    @php
        $jenisCounts = $surats->groupBy('jenis')->map->count();
        $bernomorCount = $surats->whereNotNull('nomor_surat')->where('nomor_surat', '!=', '')->count();
        $belumBernomorCount = $surats->count() - $bernomorCount;
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card 1: Total Surat --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Surat Disetujui</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $surats->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Sudah Bernomor --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-emerald-200 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-semibold text-emerald-600 uppercase">Telah Bernomor Resmi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $bernomorCount }}</p>
                </div>
            </div>
            <span class="text-xs px-2 py-1 rounded bg-emerald-50 text-emerald-700 font-semibold">Selesai</span>
        </div>

        {{-- Card 3: Menunggu Penomoran --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-amber-200 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-amber-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-semibold text-amber-600 uppercase">Menunggu Nomor Admin</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $belumBernomorCount }}</p>
                </div>
            </div>
            @if($belumBernomorCount > 0)
                <span class="text-xs px-2 py-1 rounded bg-amber-50 text-amber-700 font-semibold animate-pulse">Perlu Aksi</span>
            @endif
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div x-show="selectedItems.length > 0" class="mb-4 flex items-center gap-3" x-cloak>
            <button @click="showDeleteBulkConfirm = true" type="button"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow transition">
                <i class="fas fa-trash mr-2"></i>
                Hapus Terpilih (<span x-text="selectedItems.length"></span>)
            </button>
            <span class="text-sm text-gray-500" x-text="selectedItems.length + ' surat dipilih'"></span>
        </div>
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari judul, nomor surat, atau pembuat..." 
                           class="pl-10 pr-4 py-2 w-72 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Jenis Filter -->
                <select id="jenisFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisCounts->keys() as $jenis)
                        <option value="{{ $jenis }}">{{ ucfirst($jenis) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="flex items-center space-x-2">
                <input type="date" id="dateFrom" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <span class="text-gray-500 text-sm">s/d</span>
                <input type="date" id="dateTo" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <button onclick="clearFilters()" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm" title="Reset Filter">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($surats->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left w-10">
                                <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(0)">
                                <div class="flex items-center space-x-1">
                                    <span>No</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(1)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-file-alt text-gray-400"></i>
                                    <span>Judul Surat</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-barcode text-gray-400"></i>
                                    <span>Nomor Surat Resmi</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(3)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-tag text-gray-400"></i>
                                    <span>Jenis</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(4)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    <span>Status</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(5)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-user text-gray-400"></i>
                                    <span>Dibuat Oleh</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(6)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    <span>Tanggal</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <!-- Kolom Aksi -->
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-cogs text-gray-400"></i>
                                    <span>Aksi</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="reportTableBody">
                        @foreach ($surats as $index => $surat)
                            <tr class="hover:bg-gray-50 transition-colors duration-150" 
                                data-judul="{{ strtolower($surat->judul) }}" 
                                data-nomor="{{ strtolower($surat->nomor_surat ?? '') }}" 
                                data-user="{{ strtolower($surat->user->name ?? '') }}" 
                                data-status="{{ strtolower($surat->status) }}" 
                                data-jenis="{{ strtolower($surat->jenis) }}"
                                data-date="{{ $surat->created_at->format('Y-m-d') }}">
                                
                                <!-- Checkbox -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $surat->id }}"
                                        class="laporan-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :checked="selectedItems.includes('{{ $surat->id }}')"
                                        @change="updateSelection()">
                                </td>

                                <!-- Number -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Title -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $surat->judul }}</div>
                                    @if($surat->deskripsi)
                                        <div class="text-sm text-gray-500 truncate max-w-xs" title="{{ $surat->deskripsi }}">
                                            {{ Str::limit($surat->deskripsi, 50) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Nomor Surat Resmi -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($surat->nomor_surat)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm">
                                            <i class="fas fa-check-circle text-emerald-600 mr-1.5"></i>
                                            {{ $surat->nomor_surat }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            <i class="fas fa-clock text-amber-600 mr-1.5"></i>
                                            Belum Bernomor
                                        </span>
                                    @endif
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $jenisColors = [
                                            'masuk' => 'bg-blue-100 text-blue-800',
                                            'keluar' => 'bg-purple-100 text-purple-800',
                                            'keluar_full' => 'bg-indigo-100 text-indigo-800',
                                            'internal' => 'bg-indigo-100 text-indigo-800'
                                        ];
                                        $jenisIcons = [
                                            'masuk' => 'fas fa-arrow-down',
                                            'keluar' => 'fas fa-arrow-up',
                                            'keluar_full' => 'fas fa-building',
                                            'internal' => 'fas fa-exchange-alt'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jenisColors[$surat->jenis] ?? 'bg-gray-100 text-gray-800' }}">
                                        <i class="{{ $jenisIcons[$surat->jenis] ?? 'fas fa-file' }} mr-1"></i>
                                        {{ ucfirst($surat->jenis) }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'disetujui' => ['bg-green-100 text-green-800', 'fas fa-check-circle'],
                                            'ditolak' => ['bg-red-100 text-red-800', 'fas fa-times-circle'],
                                            'menunggu' => ['bg-yellow-100 text-yellow-800', 'fas fa-clock'],
                                            'pending' => ['bg-yellow-100 text-yellow-800', 'fas fa-clock']
                                        ];
                                        $config = $statusConfig[$surat->status] ?? ['bg-gray-100 text-gray-800', 'fas fa-question-circle'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }}">
                                        <i class="{{ $config[1] }} mr-1"></i>
                                        {{ ucfirst($surat->status) }}
                                    </span>
                                </td>

                                <!-- Creator -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($surat->user)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-xs">
                                                    {{ strtoupper(substr($surat->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $surat->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $surat->user->bagian ?? 'Pegawai' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Tidak diketahui</span>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        <div>
                                            <div class="font-medium">{{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d F Y') : '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $surat->created_at->format('H:i') }} WIB</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">
                                    <!-- Tombol Generate / Edit Nomor -->
                                    <button type="button" 
                                            onclick="openModalGenerateNomor('{{ $surat->id }}', '{{ addslashes($surat->judul) }}', '{{ $surat->nomor_surat ?? '' }}')"
                                            class="inline-flex items-center px-3 py-1.5 {{ $surat->nomor_surat ? 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200' : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-sm' }} text-xs font-semibold rounded-lg transition">
                                        <i class="fas {{ $surat->nomor_surat ? 'fa-edit' : 'fa-magic' }} mr-1.5"></i>
                                        {{ $surat->nomor_surat ? 'Ubah Nomor' : 'Generate Nomor' }}
                                    </button>

                                    <!-- Tombol Lihat Detail -->
                                    <a href="{{ route('admin.surat.dari-pimpinan.lihat', $surat->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-xs font-semibold border border-blue-200">
                                        <i class="fas fa-eye mr-1.5"></i> Detail
                                    </a>

                                    <!-- Tombol Hapus Single -->
                                    <form action="{{ route('admin.surat.destroy', $surat->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                @click="confirmSingleDelete($el.closest('form'))"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-xs font-semibold border border-red-200"
                                                title="Hapus Surat">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination placeholder -->
            @if(method_exists($surats, 'links'))
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $surats->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada surat masuk</h3>
                <p class="text-gray-500 text-sm">Surat dari pegawai yang telah disetujui pimpinan akan muncul di sini untuk penomoran resmi.</p>
            </div>
        @endif
    </div>

    {{-- Hidden Bulk Delete Form --}}
    <form action="{{ route('admin.surat.bulk-destroy') }}" method="POST" id="bulkDeleteLaporanForm">
        @csrf
        @method('DELETE')
    </form>

    {{-- Bulk Delete Confirm Modal --}}
    <div x-show="showDeleteBulkConfirm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showDeleteBulkConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Hapus Surat Terpilih?</h3>
            <p class="text-sm text-gray-600 mt-2 mb-1">Akan menghapus <span class="font-bold text-red-600" x-text="selectedItems.length"></span> surat dari laporan.</p>
            <p class="text-xs text-gray-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="showDeleteBulkConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                <button @click="submitBulkDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow transition">Hapus Semua</button>
            </div>
        </div>
    </div>

    {{-- Single Delete Confirm Modal --}}
    <div x-show="showDeleteSingleConfirm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showDeleteSingleConfirm = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Hapus Surat Ini?</h3>
            <p class="text-sm text-gray-600 mt-2 mb-6">Apakah Anda yakin ingin menghapus surat ini dari sistem? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="showDeleteSingleConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                <button @click="submitSingleDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow transition">Hapus</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL GENERATE NOMOR SURAT RESMI (Pergub Lampung No. 14 & 16 Th 2023) --}}
<div id="modalGenerateNomor" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background Backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" onclick="closeModalGenerateNomor()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:min-h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Box --}}
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-200">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 px-6 py-5 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                            <i class="fas fa-barcode text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold leading-6">Generate Nomor Surat Resmi</h3>
                            <p class="text-xs text-blue-100 mt-0.5">Sesuai Pergub Lampung No. 14 &amp; 16 Tahun 2023</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalGenerateNomor()" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Form Penomoran --}}
            <form id="formGenerateNomor" method="POST" action="">
                @csrf
                <div class="p-6 space-y-5">
                    {{-- Judul Surat Info --}}
                    <div class="bg-blue-50/80 border border-blue-200 rounded-xl p-3.5">
                        <p class="text-xs text-blue-700 font-medium">Surat yang sedang diproses:</p>
                        <p id="modalSuratJudul" class="text-sm font-bold text-gray-900 mt-0.5 truncate"></p>
                    </div>

                    {{-- Live Preview Box --}}
                    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-xl p-4 text-white shadow-inner border border-indigo-800">
                        <div class="flex items-center justify-between text-xs text-indigo-300 mb-1">
                            <span class="font-medium uppercase tracking-wider flex items-center">
                                <i class="fas fa-eye mr-1.5 text-indigo-400"></i> Preview Nomor Surat Resmi
                            </span>
                            <span class="text-[11px] bg-indigo-500/30 px-2 py-0.5 rounded text-indigo-200">SIDORA Standar</span>
                        </div>
                        <div id="previewNomorFull" class="text-lg sm:text-xl font-mono font-extrabold tracking-wide text-amber-300 mt-1 select-all break-all">
                            500.15.6.1/0001/N.08/10/2026
                        </div>
                    </div>

                    {{-- Komponen Penomoran --}}
                    <div class="space-y-4">
                        {{-- 1. Kode Klasifikasi Arsip --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                1. Kode Klasifikasi Arsip (Pergub No. 16/2023)
                            </label>
                            <select id="selectKlasifikasi" onchange="syncKlasifikasi(this.value)" class="w-full text-xs sm:text-sm border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 mb-2 bg-gray-50">
                                @if(isset($klasifikasiList))
                                    @foreach($klasifikasiList as $klas)
                                        <option value="{{ $klas['kode'] }}" {{ $klas['kode'] === '500.15.6.1' ? 'selected' : '' }}>
                                            {{ $klas['kode'] }} — {{ $klas['nama'] }}
                                        </option>
                                    @endforeach
                                @endif
                                <option value="custom">✏️ Ketik Kode Kustom / Manual...</option>
                            </select>
                            <input type="text" name="kode_klasifikasi" id="inputKodeKlasifikasi" value="500.15.6.1" required
                                   oninput="updatePreviewNomor()"
                                   placeholder="Contoh: 500.15.6.1"
                                   class="w-full text-sm font-mono border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                            <span class="text-[11px] text-gray-500 mt-1 block">Kode urusan arsip ketenagakerjaan Pemprov Lampung.</span>
                        </div>

                        {{-- Grid 4 Kolom: Urut, Unit, Bulan, Tahun --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            {{-- 2. Nomor Urut --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    2. No. Urut
                                </label>
                                <input type="text" name="nomor_urut" id="inputNomorUrut" value="{{ $nextNomorUrut ?? '0001' }}" required
                                       oninput="updatePreviewNomor()"
                                       maxlength="6"
                                       class="w-full text-sm font-mono font-bold text-center border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                <span class="text-[10px] text-gray-500 mt-1 block text-center">4 digit (0001)</span>
                            </div>

                            {{-- 3. Kode Unit --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    3. Kode Unit
                                </label>
                                <input type="text" name="kode_unit" id="inputKodeUnit" value="N.08" required
                                       oninput="updatePreviewNomor()"
                                       class="w-full text-sm font-mono font-bold text-center border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                <span class="text-[10px] text-gray-500 mt-1 block text-center">Disnaker (N.08)</span>
                            </div>

                            {{-- 4. Bulan --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    4. Bulan
                                </label>
                                <input type="text" name="bulan" id="inputBulan" value="{{ date('m') }}" required
                                       oninput="updatePreviewNomor()"
                                       maxlength="2"
                                       class="w-full text-sm font-mono font-bold text-center border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                <span class="text-[10px] text-gray-500 mt-1 block text-center">2 digit ({{ date('m') }})</span>
                            </div>

                            {{-- 5. Tahun --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    5. Tahun
                                </label>
                                <input type="text" name="tahun" id="inputTahun" value="{{ date('Y') }}" required
                                       oninput="updatePreviewNomor()"
                                       maxlength="4"
                                       class="w-full text-sm font-mono font-bold text-center border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                <span class="text-[10px] text-gray-500 mt-1 block text-center">4 digit ({{ date('Y') }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl border-t border-gray-100">
                    <button type="button" onclick="closeModalGenerateNomor()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 font-semibold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center justify-center">
                        <i class="fas fa-check-circle mr-2"></i> Simpan &amp; Terapkan Nomor Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Filter and Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const jenisFilter = document.getElementById('jenisFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');

    if (searchInput) searchInput.addEventListener('input', filterData);
    if (jenisFilter) jenisFilter.addEventListener('change', filterData);
    if (dateFrom) dateFrom.addEventListener('change', filterData);
    if (dateTo) dateTo.addEventListener('change', filterData);
});

function filterData() {
    const searchTerm = document.getElementById('searchInput') ? document.getElementById('searchInput').value.toLowerCase() : '';
    const selectedJenis = document.getElementById('jenisFilter') ? document.getElementById('jenisFilter').value.toLowerCase() : '';
    const fromDate = document.getElementById('dateFrom') ? document.getElementById('dateFrom').value : '';
    const toDate = document.getElementById('dateTo') ? document.getElementById('dateTo').value : '';

    const rows = document.querySelectorAll('#reportTableBody tr');

    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Lewati baris kosong

        const judul = row.getAttribute('data-judul') || '';
        const nomor = row.getAttribute('data-nomor') || '';
        const user = row.getAttribute('data-user') || '';
        const jenis = row.getAttribute('data-jenis') || '';
        const rowDate = row.getAttribute('data-date') || '';

        const matchesSearch = judul.includes(searchTerm) || nomor.includes(searchTerm) || user.includes(searchTerm);
        const matchesJenis = !selectedJenis || jenis === selectedJenis;
        
        let matchesDate = true;
        if (fromDate && toDate) {
            matchesDate = rowDate >= fromDate && rowDate <= toDate;
        } else if (fromDate) {
            matchesDate = rowDate >= fromDate;
        } else if (toDate) {
            matchesDate = rowDate <= toDate;
        }

        if (matchesSearch && matchesJenis && matchesDate) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearFilters() {
    if (document.getElementById('searchInput')) document.getElementById('searchInput').value = '';
    if (document.getElementById('jenisFilter')) document.getElementById('jenisFilter').value = '';
    if (document.getElementById('dateFrom')) document.getElementById('dateFrom').value = '';
    if (document.getElementById('dateTo')) document.getElementById('dateTo').value = '';
    filterData();
}

// Sort functionality
let sortDirection = {};

function sortTable(columnIndex) {
    const tbody = document.getElementById('reportTableBody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
    
    const direction = sortDirection[columnIndex] === 'asc' ? 'desc' : 'asc';
    sortDirection[columnIndex] = direction;

    rows.sort((a, b) => {
        let aVal = a.children[columnIndex].textContent.trim();
        let bVal = b.children[columnIndex].textContent.trim();

        if (columnIndex === 0) {
            aVal = parseInt(aVal) || 0;
            bVal = parseInt(bVal) || 0;
        }

        if (direction === 'asc') {
            return aVal > bVal ? 1 : -1;
        } else {
            return aVal < bVal ? 1 : -1;
        }
    });

    tbody.innerHTML = '';
    rows.forEach((row, index) => {
        row.children[0].textContent = index + 1;
        tbody.appendChild(row);
    });
}

// Logic Modal & Live Preview Nomor Surat
function syncKlasifikasi(val) {
    const input = document.getElementById('inputKodeKlasifikasi');
    if (val !== 'custom') {
        input.value = val;
    } else {
        input.value = '';
        input.focus();
    }
    updatePreviewNomor();
}

function updatePreviewNomor() {
    const klas  = document.getElementById('inputKodeKlasifikasi').value.trim() || '500.15.6.1';
    let urut    = document.getElementById('inputNomorUrut').value.trim() || '0001';
    if (/^\d+$/.test(urut) && urut.length < 4) {
        urut = urut.padStart(4, '0');
    }
    const unit  = document.getElementById('inputKodeUnit').value.trim() || 'N.08';
    let bln     = document.getElementById('inputBulan').value.trim() || '{{ date('m') }}';
    if (/^\d+$/.test(bln) && bln.length < 2) {
        bln = bln.padStart(2, '0');
    }
    const thn   = document.getElementById('inputTahun').value.trim() || '{{ date('Y') }}';

    const fullNomor = `${klas}/${urut}/${unit}/${bln}/${thn}`;
    document.getElementById('previewNomorFull').textContent = fullNomor;
}

function openModalGenerateNomor(id, judul, currentNomor) {
    const modal = document.getElementById('modalGenerateNomor');
    const form  = document.getElementById('formGenerateNomor');
    form.action = `/admin/surat/${id}/generate-nomor`;

    document.getElementById('modalSuratJudul').textContent = judul;

    if (currentNomor && currentNomor.includes('/')) {
        const parts = currentNomor.split('/');
        if (parts.length >= 5) {
            document.getElementById('inputKodeKlasifikasi').value = parts[0];
            document.getElementById('inputNomorUrut').value = parts[1];
            document.getElementById('inputKodeUnit').value = parts[2];
            document.getElementById('inputBulan').value = parts[3];
            document.getElementById('inputTahun').value = parts[4];
            
            const select = document.getElementById('selectKlasifikasi');
            let found = false;
            for (let opt of select.options) {
                if (opt.value === parts[0]) {
                    select.value = parts[0];
                    found = true;
                    break;
                }
            }
            if (!found) select.value = 'custom';
        }
    } else {
        // Default baru
        document.getElementById('inputKodeKlasifikasi').value = '500.15.6.1';
        document.getElementById('selectKlasifikasi').value = '500.15.6.1';
        document.getElementById('inputNomorUrut').value = '{{ $nextNomorUrut ?? '0001' }}';
        document.getElementById('inputKodeUnit').value = 'N.08';
        document.getElementById('inputBulan').value = '{{ date('m') }}';
        document.getElementById('inputTahun').value = '{{ date('Y') }}';
    }

    updatePreviewNomor();
    modal.classList.remove('hidden');
}

function closeModalGenerateNomor() {
    document.getElementById('modalGenerateNomor').classList.add('hidden');
}

function laporanSuratAdmin() {
    return {
        selectedItems: [],
        selectAll: false,
        showDeleteBulkConfirm: false,
        showDeleteSingleConfirm: false,
        deleteSingleForm: null,
        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            const checkboxes = document.querySelectorAll('.laporan-checkbox');
            if (this.selectAll) {
                this.selectedItems = Array.from(checkboxes).map(function(cb) { return cb.value; });
                checkboxes.forEach(function(cb) { cb.checked = true; });
            } else {
                this.selectedItems = [];
                checkboxes.forEach(function(cb) { cb.checked = false; });
            }
        },
        updateSelection() {
            const checked = document.querySelectorAll('.laporan-checkbox:checked');
            const all = document.querySelectorAll('.laporan-checkbox');
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
            const form = document.getElementById('bulkDeleteLaporanForm');
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
@endsection
