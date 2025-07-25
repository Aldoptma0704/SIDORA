@extends('layouts.app')

@section('content')
<div x-data="{ 
    showModal: false, 
    showSearch: false,
    searchTerm: '',
    selectedItems: [],
    selectAll: false,
    showDeleteConfirm: false
}" class="space-y-6">

    {{-- Success Notification --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Status Pengajuan Surat</h1>
            <p class="mt-2 text-sm text-gray-600">Kelola dan pantau status pengajuan surat Anda</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="text-sm text-gray-500">Total Surat: <span class="font-semibold text-gray-900">{{ $surats->count() }}</span></span>
        </div>
    </div>

    {{-- Enhanced Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 hover:shadow-md transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Menunggu Persetujuan</p>
                        <p class="text-3xl font-bold text-blue-900 mt-2">{{ $jumlahMenunggu }}</p>
                        <p class="text-xs text-blue-600 mt-1">Sedang dalam proses review</p>
                    </div>
                    <div class="bg-blue-500 text-white p-4 rounded-full shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
        </div>

        <div class="relative overflow-hidden bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl shadow-sm border border-green-200 hover:shadow-md transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Disetujui</p>
                        <p class="text-3xl font-bold text-green-900 mt-2">{{ $jumlahDisetujui }}</p>
                        <p class="text-xs text-green-600 mt-1">Surat telah diapprove</p>
                    </div>
                    <div class="bg-green-500 text-white p-4 rounded-full shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-600"></div>
        </div>

        <div class="relative overflow-hidden bg-gradient-to-br from-red-50 to-rose-100 rounded-xl shadow-sm border border-red-200 hover:shadow-md transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600 uppercase tracking-wide">Ditolak</p>
                        <p class="text-3xl font-bold text-red-900 mt-2">{{ $jumlahDitolak }}</p>
                        <p class="text-xs text-red-600 mt-1">Perlu perbaikan atau revisi</p>
                    </div>
                    <div class="bg-red-500 text-white p-4 rounded-full shadow-lg">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-rose-600"></div>
        </div>
    </div>

    {{-- Enhanced Action Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <button @click="showModal = true" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg shadow-md hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan Permohonan
                </button>
                
                <button x-show="selectedItems.length > 0" @click="showDeleteConfirm = true" class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg shadow-md hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Terpilih
                </button>
            </div>
            

    {{-- Enhanced Modal --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Pilih Draft Surat</h3>
                        <p class="text-sm text-gray-600 mt-1">Pilih satu atau lebih draft untuk diajukan</p>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <form action="{{ route('surat.ajukan') }}" method="POST">
                @csrf
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="space-y-3">
                        @forelse ($drafts as $draft)
                            <label class="flex items-center p-4 rounded-xl border-2 border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200 group">
                                <input type="checkbox" name="ids[]" value="{{ $draft->id }}" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-semibold text-gray-900 group-hover:text-blue-900">{{ $draft->judul }}</span>
                                        <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ $draft->created_at->format('d M Y') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">Jenis: {{ str_replace('_', ' ', ucfirst($draft->jenis ?? 'Umum')) }}</p>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-12">
                                <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                                <p class="text-lg text-gray-500 font-medium">Tidak ada draft tersedia</p>
                                <p class="text-sm text-gray-400">Buat draft surat terlebih dahulu sebelum mengajukan permohonan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-6 py-3 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Ajukan Surat Terpilih
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteConfirm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.away="showDeleteConfirm = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
                <p class="text-center text-gray-600 mb-6">Apakah Anda yakin ingin menghapus surat yang dipilih? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3">
                    <button @click="showDeleteConfirm = false" class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-all duration-200">
                        Batal
                    </button>
                    <button @click="document.getElementById('bulkDeleteForm').submit()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('surat.bulk_delete') }}" method="POST" id="bulkDeleteForm">
            @csrf
            @method('DELETE')
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <input type="checkbox" @change="selectAll = !selectAll; selectedItems = selectAll ? Array.from(document.querySelectorAll('.surat-checkbox')).map(cb => cb.value) : []" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul Surat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis Surat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($surats as $index => $surat)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 group">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="ids[]" value="{{ $surat->id }}" class="surat-checkbox h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" @change="selectedItems = Array.from(document.querySelectorAll('.surat-checkbox:checked')).map(cb => cb.value)">
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-900">{{ $surat->judul }}</div>
                                    <div class="text-xs text-gray-500 mt-1">ID: #{{ $surat->id }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ str_replace('_', ' ', ucfirst($surat->jenis)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = match($surat->status) {
                                            'disetujui' => ['bg-green-100 text-green-800', 'fas fa-check-circle', 'Disetujui'],
                                            'ditolak' => ['bg-red-100 text-red-800', 'fas fa-times-circle', 'Ditolak'],
                                            default => ['bg-yellow-100 text-yellow-800', 'fas fa-clock', 'Menunggu']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusConfig[0] }}">
                                        <i class="{{ $statusConfig[1] }} mr-1"></i>
                                        {{ $statusConfig[2] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div>{{ $surat->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $surat->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('surat.preview', ['id' => $surat->id, 'view' => 'status']) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-blue-700 hover:to-indigo-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                                            <i class="fas fa-eye mr-1"></i>
                                            Lihat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data surat</h3>
                                        <p class="text-sm text-gray-500">Belum ada surat yang diajukan. Mulai dengan mengajukan permohonan surat baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    {{-- Pagination (if needed) --}}
    @if(method_exists($surats, 'links'))
        <div class="flex justify-center">
            {{ $surats->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Enhanced JavaScript for better UX
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide success messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[x-data*="show: true"]');
            alerts.forEach(alert => {
                if (alert.__x) {
                    alert.__x.$data.show = false;
                }
            });
        }, 5000);

        // Enhanced select all functionality
        const selectAllCheckbox = document.querySelector('input[type="checkbox"]:not(.surat-checkbox)');
        const itemCheckboxes = document.querySelectorAll('.surat-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        // Update select all based on individual selections
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.surat-checkbox:checked').length;
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = checkedCount === itemCheckboxes.length;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
                }
            });
        });
    });
</script>
@endpush
@endsection