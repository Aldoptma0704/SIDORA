@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-alt text-primary-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Daftar Surat</h1>
                <p class="text-gray-600 mt-1">Kelola persetujuan dan disposisi surat yang masuk</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div class="text-green-800">
                <p class="font-medium">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    @php
        $menungguCount = $surats->where('status', 'menunggu')->count();
        $disetujuiCount = $surats->where('status', 'disetujui')->count();
        $ditolakCount = $surats->where('status', 'ditolak')->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Menunggu Persetujuan</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $menungguCount }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $disetujuiCount }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $ditolakCount }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="flex border-b border-gray-200">
            <a href="?filter=menunggu" 
               class="flex-1 px-6 py-4 text-center font-medium transition-colors {{ request('filter') === 'menunggu' || !request('filter') ? 'bg-yellow-50 text-yellow-700 border-b-2 border-yellow-500' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas fa-clock mr-2"></i>
                Menunggu ({{ $menungguCount }})
            </a>
            <a href="?filter=disetujui" 
               class="flex-1 px-6 py-4 text-center font-medium transition-colors {{ request('filter') === 'disetujui' ? 'bg-green-50 text-green-700 border-b-2 border-green-500' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas fa-check-circle mr-2"></i>
                Disetujui ({{ $disetujuiCount }})
            </a>
            <a href="?filter=ditolak" 
               class="flex-1 px-6 py-4 text-center font-medium transition-colors {{ request('filter') === 'ditolak' ? 'bg-red-50 text-red-700 border-b-2 border-red-500' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas fa-times-circle mr-2"></i>
                Ditolak ({{ $ditolakCount }})
            </a>
        </div>
    </div>

    <!-- Surat List -->
    @php
        $filter = request('filter') ?? 'menunggu';
        $filteredSurats = $surats->filter(fn($s) => strtolower($s->status) === strtolower($filter));
    @endphp

    <div class="space-y-4">
        @forelse ($filteredSurats as $surat)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <!-- Letter Info -->
                        <div class="flex-1">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 
                                    {{ $surat->status === 'disetujui' ? 'bg-green-100' : ($surat->status === 'ditolak' ? 'bg-red-100' : 'bg-yellow-100') }}">
                                    @if($surat->status === 'disetujui')
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    @elseif($surat->status === 'ditolak')
                                        <i class="fas fa-times-circle text-red-600"></i>
                                    @else
                                        <i class="fas fa-clock text-yellow-600"></i>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $surat->judul }}</h3>
                                    
                                    <div class="flex items-center gap-6 text-sm text-gray-600 mb-3">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                            <span>{{ $surat->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-tag text-gray-400"></i>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ $surat->status === 'disetujui' ? 'bg-green-100 text-green-800' : ($surat->status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($surat->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-gray-600 text-sm leading-relaxed">
                                        {!! Str::limit(strip_tags($surat->isi), 150) !!}
                                    </div>

                                    <!-- Additional Info -->
                                    @if($surat->status === 'ditolak' && $surat->alasan_penolakan)
                                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                                                <div>
                                                    <p class="text-red-800 font-medium text-sm">Alasan Penolakan:</p>
                                                    <p class="text-red-700 text-sm">{{ $surat->alasan_penolakan }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($surat->status === 'disetujui' && $surat->disposisi)
                                        <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-share text-green-500 mt-0.5"></i>
                                                <div>
                                                    <p class="text-green-800 font-medium text-sm">Disposisi ke:</p>
                                                    <p class="text-green-700 text-sm">{{ $surat->disposisi }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-2 ml-6">
                            <!-- View Button -->
                            <a href="{{ route('pimpinan.surat.preview', $surat->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                                <i class="fas fa-eye mr-2"></i>
                                Lihat Detail
                            </a>

                            @if($surat->status === 'menunggu')
                                <!-- Approve & Disposition Button -->
                                <div x-data="{ open: false }" x-cloak>
                                    <button @click="open = true"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm font-medium">
                                        <i class="fas fa-check mr-2"></i>
                                        Setujui & Disposisi
                                    </button>

                                    <!-- Approval Modal -->
                                    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                                        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                                            <div class="p-6">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-share text-green-600"></i>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-900">Disposisi ke Bidang</h3>
                                                </div>

                                                <form method="POST" action="{{ route('pimpinan.surat.setujui', $surat->id) }}">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Pilih Bidang/Subbidang:
                                                        </label>
                                                        <select name="disposisi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                                            <option value="">-- Pilih Bidang --</option>
                                                            <optgroup label="Sekretariat">
                                                                <option value="Sub Bagian Umum dan Kepegawaian">Sub Bagian Umum dan Kepegawaian</option>
                                                                <option value="Substansi Keuangan dan Aset">Substansi Keuangan dan Aset</option>
                                                                <option value="Substansi Perencanaan">Substansi Perencanaan</option>
                                                            </optgroup>
                                                            <optgroup label="Penempatan dan Perluasan Kesempatan Kerja">
                                                                <option value="Seksi Penempatan Tenaga Kerja Dalam Negeri">Seksi Penempatan Tenaga Kerja Dalam Negeri</option>
                                                                <option value="Substansi Penempatan Tenaga Kerja Luar Negeri">Substansi Penempatan Tenaga Kerja Luar Negeri</option>
                                                                <option value="Seksi Pengembangan Informasi Pasar Kerja">Seksi Pengembangan Informasi Pasar Kerja</option>
                                                            </optgroup>
                                                            <optgroup label="Pelatihan dan Produktivitas Tenaga Kerja">
                                                                <option value="Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja">Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja</option>
                                                                <option value="Seksi Pengembangan Produktivitas Tenaga Kerja">Seksi Pengembangan Produktivitas Tenaga Kerja</option>
                                                                <option value="Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja">Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja</option>
                                                            </optgroup>
                                                            <optgroup label="Bidang Pengawasan Ketenagakerjaan">
                                                                <option value="Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan">Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan</option>
                                                                <option value="Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja">Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja</option>
                                                            </optgroup>
                                                            <optgroup label="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja">
                                                                <option value="Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial">Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial</option>
                                                                <option value="Seksi Pembinaan Syarat Kerja dan Jamsostek">Seksi Pembinaan Syarat Kerja dan Jamsostek</option>
                                                                <option value="Substansi Penyelesaian Perselisihan Hubungan Industrial">Substansi Penyelesaian Perselisihan Hubungan Industrial</option>
                                                            </optgroup>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" @click="open = false" 
                                                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                                            Batal
                                                        </button>
                                                        <button type="submit" 
                                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                                                            <i class="fas fa-paper-plane mr-2"></i>
                                                            Kirim Disposisi
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reject Button -->
                                <div x-data="{ openTolak: false }" x-cloak>
                                    <button @click="openTolak = true"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm font-medium">
                                        <i class="fas fa-times mr-2"></i>
                                        Tolak
                                    </button>

                                    <!-- Rejection Modal -->
                                    <div x-show="openTolak" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                                        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
                                            <div class="p-6">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-times text-red-600"></i>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-red-600">Tolak Surat</h3>
                                                </div>

                                                <form method="POST" action="{{ route('pimpinan.surat.tolak', $surat->id) }}">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Alasan Penolakan:
                                                        </label>
                                                        <textarea name="alasan_penolakan" 
                                                                  rows="4" 
                                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                                                  placeholder="Jelaskan alasan penolakan surat ini..."
                                                                  required></textarea>
                                                    </div>
                                                    
                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" @click="openTolak = false" 
                                                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                                            Batal
                                                        </button>
                                                        <button type="submit" 
                                                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                                                            <i class="fas fa-ban mr-2"></i>
                                                            Tolak Surat
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada surat {{ $filter }}</h3>
                <p class="text-gray-600">Belum ada surat dengan status {{ $filter }} saat ini.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection