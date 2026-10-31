@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50" x-data="suratMasukPimpinan()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-800 p-6 mb-8 rounded-xl shadow-lg animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="font-medium text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 text-red-800 p-6 mb-8 rounded-xl shadow-lg animate-fade-in">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-sm mb-1">Terjadi Kesalahan</p>
                        <ul class="list-disc list-inside text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 mb-8 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="p-3.5 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl shadow-sm text-blue-600">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                            Daftar Surat Masuk
                        </h1>
                        <p class="text-gray-500 text-sm mt-0.5">Kelola dan pantau surat masuk serta arsip berkas PDF untuk pimpinan</p>
                    </div>
                </div>

                {{-- Action Button: Upload PDF --}}
                <div class="flex flex-wrap items-center gap-3">
                    <button @click="showUploadModal = true" type="button" 
                            class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium text-sm rounded-xl shadow-md hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-file-upload mr-2 group-hover:rotate-12 transition-transform"></i> 
                        Upload PDF
                    </button>
                    <button x-show="selectedItems.length > 0" @click="showDeleteBulkConfirm = true" type="button"
                            class="group inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 text-white font-medium text-sm rounded-xl shadow-md hover:from-red-700 hover:to-rose-700 transform hover:-translate-y-0.5 transition-all duration-200" x-cloak>
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Terpilih (<span x-text="selectedItems.length"></span>)
                    </button>
                </div>
            </div>

            {{-- Filter & Search Bar --}}
            <div class="pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="text-xs text-gray-500 font-medium">
                    Menampilkan <span class="font-bold text-gray-800">{{ $surats->count() }}</span> surat masuk
                </div>

                <form action="{{ route('pimpinan.surat-masuk') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, asal instansi..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Surat Masuk --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-4 text-left w-10">
                                <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Judul &amp; Asal Surat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nomor Surat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Format Berkas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($surats as $index => $surat)
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="px-4 py-4">
                                    <input type="checkbox" value="{{ $surat->id }}"
                                        class="masuk-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :checked="selectedItems.includes('{{ $surat->id }}')"
                                        @change="updateSelection()">
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-gray-900">{{ $surat->judul }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 flex items-center">
                                        <i class="fas fa-building text-gray-400 mr-1.5"></i>
                                        {{ $surat->nama_instansi ?: 'Instansi / Internal' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-gray-700">
                                    {{ $surat->nomor_surat ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($surat->file_path)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <i class="fas fa-file-pdf mr-1"></i> PDF Unggahan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            <i class="fas fa-file-alt mr-1"></i> Dokumen Sistem
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($surat->file_path)
                                            <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                                <i class="fas fa-external-link-alt mr-1"></i> Buka PDF
                                            </a>
                                        @else
                                            <a href="{{ route('pimpinan.surat.preview', $surat->id) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                                            </a>
                                        @endif
                                        <form action="{{ route('pimpinan.surat-masuk.destroy', $surat->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmSingleDelete($el.closest('form'))"
                                                    class="inline-flex items-center px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium shadow-sm transition" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-gray-400 mb-3">
                                        <i class="fas fa-inbox text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">Belum ada surat masuk</p>
                                    <p class="text-xs text-gray-500 mt-1">Gunakan tombol "Upload PDF" untuk mengunggah berkas surat masuk</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Hidden Bulk Delete Form (Surat Masuk) --}}
    <form action="{{ route('pimpinan.surat-masuk.bulk-delete') }}" method="POST" id="bulkDeleteMasukForm">
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
            <h3 class="text-lg font-bold text-gray-900">Hapus Surat Masuk Ini?</h3>
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
            <h3 class="text-lg font-bold text-gray-900">Hapus Surat Masuk Terpilih?</h3>
            <p class="text-sm text-gray-600 mt-2 mb-1">Akan menghapus <span class="font-bold text-red-600" x-text="selectedItems.length"></span> surat.</p>
            <p class="text-xs text-gray-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="showDeleteBulkConfirm = false" class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                <button @click="submitBulkDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow transition">Hapus Semua</button>
            </div>
        </div>
    </div>

    {{-- Modal Upload PDF --}}
    <div x-show="showUploadModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showUploadModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 bg-blue-100 text-blue-600 rounded-xl">
                            <i class="fas fa-file-pdf text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Upload Berkas PDF Surat Masuk</h3>
                            <p class="text-xs text-gray-500">Unggah salinan surat fisik dalam format PDF</p>
                        </div>
                    </div>
                    <button @click="showUploadModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('pimpinan.surat-masuk.upload-pdf') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="jenis" value="masuk">

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Pilih Berkas PDF <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="file_surat" accept="application/pdf" required 
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border-2 border-dashed border-gray-300 rounded-xl p-3 cursor-pointer">
                    <p class="text-[11px] text-gray-500 mt-1">Maksimal ukuran berkas: 5MB (Format PDF)</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Asal Instansi Pengirim (Opsional)
                    </label>
                    <input type="text" name="nama_instansi" placeholder="Contoh: Kementerian Ketenagakerjaan RI" 
                           class="w-full p-2.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Tujuan / Keterangan (Opsional)
                    </label>
                    <input type="text" name="tujuan" placeholder="Contoh: Kepala Dinas Tenaga Kerja" 
                           class="w-full p-2.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showUploadModal = false" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                        <i class="fas fa-upload mr-1.5"></i> Unggah Berkas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function suratMasukPimpinan() {
        return {
            showUploadModal: false,
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
                if (this.deleteSingleForm) this.deleteSingleForm.submit();
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
