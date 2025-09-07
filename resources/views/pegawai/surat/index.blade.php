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

        <div x-data="{ showModal: false }">
            {{-- Header Section --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    @php
                        $title = match($jenis ?? null) {
                            'masuk'   => 'Daftar Surat Masuk',
                            'keluar'  => 'Daftar Surat Keluar',
                            default   => 'Daftar Semua Surat Anda',
                        };
                        
                        $iconClass = match($jenis ?? null) {
                            'masuk'   => 'fas fa-inbox text-blue-500',
                            'keluar'  => 'fas fa-paper-plane text-green-500',
                            default   => 'fas fa-file-alt text-purple-500',
                        };
                    @endphp
                    
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl">
                            <i class="{{ $iconClass }} text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                                {{ $title }}
                            </h1>
                            <p class="text-gray-500 mt-1">Kelola dan pantau semua surat Anda</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="showModal = true" type="button" 
                                class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-purple-800 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-upload mr-2 group-hover:rotate-12 transition-transform"></i> 
                            Upload PDF
                        </button>
                        <a href="{{ route('pegawai.surat.create') }}" 
                           class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i> 
                            Buat Surat Baru
                        </a>
                    </div>
                </div>
            </div>

            {{-- Modal Upload PDF --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-cloak>
                <div @click.away="showModal = false" class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform">
                    <div class="flex items-center mb-6">
                        <div class="p-3 bg-gradient-to-br from-purple-100 to-blue-100 rounded-xl mr-4">
                            <i class="fas fa-file-upload text-purple-600 text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Upload File Surat</h3>
                    </div>
                    
                    <form action="{{ route('pegawai.surat.upload-pdf') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="jenis" value="{{ $jenis ?? 'masuk' }}">
                        
                        <div class="mb-6">
                            <label for="file_surat" class="block text-sm font-semibold text-gray-700 mb-3">Pilih File PDF</label>
                            <div class="relative">
                                <input type="file" name="file_surat" id="file_surat" accept=".pdf" required 
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-50 file:to-purple-50 file:text-blue-700 hover:file:from-blue-100 hover:file:to-purple-100 file:transition-all file:duration-200 border-2 border-dashed border-gray-300 rounded-xl p-4 hover:border-blue-400 transition-colors">
                            </div>
                            <div class="flex items-center mt-3 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Format: PDF • Maksimal: 5MB</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-4">
                            <button type="button" @click="showModal = false" 
                                    class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <i class="fas fa-upload mr-2"></i>
                                Upload
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
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Judul Surat</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($surats as $index => $surat)
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full text-sm font-bold text-gray-700 group-hover:from-blue-200 group-hover:to-purple-200 transition-colors">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                {{ $surat->judul }}
                                            </div>
                                            @if($surat->disposisi_user_id && $surat->disposisi_user_id == auth()->id())
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-blue-700 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full">
                                                        <i class="fas fa-user-check mr-1"></i>
                                                        Disposisi untuk Anda
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $jenisDisplay = match($surat->jenis) {
                                                'masuk' => 'Masuk',
                                                'keluar' => 'Keluar (Template)',
                                                'keluar_full' => 'Keluar (Kop Surat)',
                                                default => ucfirst($surat->jenis)
                                            };
                                            
                                            $jenisIcon = match($surat->jenis) {
                                                'masuk' => 'fas fa-inbox',
                                                'keluar' => 'fas fa-paper-plane',
                                                'keluar_full' => 'fas fa-file-signature',
                                                default => 'fas fa-file-alt'
                                            };
                                        @endphp
                                        <div class="flex items-center">
                                            <i class="{{ $jenisIcon }} text-gray-400 mr-2"></i>
                                            <span class="text-sm text-gray-700 font-medium">{{ $jenisDisplay }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = match($surat->status) {
                                                'disetujui' => [
                                                    'class' => 'from-green-100 to-emerald-100 text-green-800 border-green-200',
                                                    'icon' => 'fas fa-check-circle'
                                                ],
                                                'ditolak' => [
                                                    'class' => 'from-red-100 to-pink-100 text-red-800 border-red-200',
                                                    'icon' => 'fas fa-times-circle'
                                                ],
                                                'draft' => [
                                                    'class' => 'from-gray-100 to-slate-100 text-gray-800 border-gray-200',
                                                    'icon' => 'fas fa-edit'
                                                ],
                                                default => [
                                                    'class' => 'from-yellow-100 to-orange-100 text-yellow-800 border-yellow-200',
                                                    'icon' => 'fas fa-clock'
                                                ]
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r {{ $statusConfig['class'] }} border">
                                            <i class="{{ $statusConfig['icon'] }} mr-1"></i>
                                            {{ ucfirst($surat->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- Tombol Lihat/Preview --}}
                                            @if ($surat->file_path)
                                                <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" 
                                                   class="group inline-flex items-center px-3 py-2 text-sm text-white bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                                                    <i class="fas fa-file-pdf mr-1 group-hover:rotate-12 transition-transform"></i> 
                                                    PDF
                                                </a>
                                            @else
                                                <a href="{{ route('pegawai.surat.preview', $surat->id) }}" 
                                                   class="group inline-flex items-center px-3 py-2 text-sm text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                                                    <i class="fas fa-eye mr-1 group-hover:scale-110 transition-transform"></i> 
                                                    Lihat
                                                </a>
                                            @endif

                                            {{-- Tombol Edit & Hapus hanya untuk draft --}}
                                            @if ($surat->status == 'draft' && $surat->user_id == auth()->id())
                                                <a href="{{ route('pegawai.surat.edit', $surat->id) }}" 
                                                   class="group inline-flex items-center px-3 py-2 text-sm text-white bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                                                    <i class="fas fa-pencil-alt group-hover:rotate-12 transition-transform"></i>
                                                </a>
                                                <form action="{{ route('pegawai.surat.destroy', $surat->id) }}" method="POST" class="inline-block" 
                                                      onsubmit="return confirm('Anda yakin ingin menghapus draft surat ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="group inline-flex items-center px-3 py-2 text-sm text-white bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                                                        <i class="fas fa-trash-alt group-hover:rotate-12 transition-transform"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Surat</h3>
                                            <p class="text-gray-500">Mulai dengan membuat surat baru atau upload file PDF</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
@endsection