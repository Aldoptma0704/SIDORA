@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-md rounded-2xl p-4 shadow-sm">
                            <i class="fas fa-file-signature text-3xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold mb-1">Daftar Disposisi Surat</h1>
                            <p class="text-blue-100 text-sm">Riwayat dan arsip keputusan surat masuk dari pegawai (Disetujui & Ditolak)</p>
                        </div>
                    </div>
                    
                    {{-- Navigasi Cepat Dashboard --}}
                    <div>
                        <a href="/pimpinan/dashboard" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold backdrop-blur-md transition border border-white/20 shadow-sm">
                            <i class="fas fa-tachometer-alt mr-2"></i> Ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="mb-6 animate-fade-in">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl p-4 shadow-sm flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- 3 TOMBOL FILTER: Total Surat, Surat Disetujui, Surat Tak Disetujui --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {{-- Tombol 1: Total Surat --}}
            <a href="{{ route('pimpinan.disposisi', ['status' => 'all']) }}" 
               class="group relative flex items-center justify-between p-5 rounded-2xl border-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm hover:shadow-md {{ $filter === 'all' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-transparent shadow-lg' : 'bg-white text-gray-700 border-gray-200/80 hover:border-blue-300' }}">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600 group-hover:scale-110' }} transition-transform">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base {{ $filter === 'all' ? 'text-white' : 'text-gray-900' }}">Total Surat</h3>
                        <p class="text-xs {{ $filter === 'all' ? 'text-blue-100' : 'text-gray-500' }}">Semua arsip keputusan</p>
                    </div>
                </div>
                <div class="text-2xl font-black px-3.5 py-1 rounded-xl {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700 font-bold' }}">
                    {{ $totalCount }}
                </div>
            </a>

            {{-- Tombol 2: Surat Disetujui --}}
            <a href="{{ route('pimpinan.disposisi', ['status' => 'disetujui']) }}" 
               class="group relative flex items-center justify-between p-5 rounded-2xl border-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm hover:shadow-md {{ $filter === 'disetujui' ? 'bg-gradient-to-r from-emerald-600 to-green-600 text-white border-transparent shadow-lg' : 'bg-white text-gray-700 border-gray-200/80 hover:border-emerald-300' }}">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $filter === 'disetujui' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-600 group-hover:scale-110' }} transition-transform">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base {{ $filter === 'disetujui' ? 'text-white' : 'text-gray-900' }}">Surat Disetujui</h3>
                        <p class="text-xs {{ $filter === 'disetujui' ? 'text-emerald-100' : 'text-gray-500' }}">Disetujui & didisposisikan</p>
                    </div>
                </div>
                <div class="text-2xl font-black px-3.5 py-1 rounded-xl {{ $filter === 'disetujui' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700 font-bold' }}">
                    {{ $disetujuiCount }}
                </div>
            </a>

            {{-- Tombol 3: Surat Tak Disetujui --}}
            <a href="{{ route('pimpinan.disposisi', ['status' => 'ditolak']) }}" 
               class="group relative flex items-center justify-between p-5 rounded-2xl border-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm hover:shadow-md {{ $filter === 'ditolak' ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white border-transparent shadow-lg' : 'bg-white text-gray-700 border-gray-200/80 hover:border-rose-300' }}">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $filter === 'ditolak' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-600 group-hover:scale-110' }} transition-transform">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base {{ $filter === 'ditolak' ? 'text-white' : 'text-gray-900' }}">Surat Tak Disetujui</h3>
                        <p class="text-xs {{ $filter === 'ditolak' ? 'text-rose-100' : 'text-gray-500' }}">Ditolak / tidak disetujui</p>
                    </div>
                </div>
                <div class="text-2xl font-black px-3.5 py-1 rounded-xl {{ $filter === 'ditolak' ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-700 font-bold' }}">
                    {{ $ditolakCount }}
                </div>
            </a>
        </div>

        {{-- Tabel Disposisi Surat --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200/80">
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                <div class="flex items-center space-x-3">
                    <span class="w-3 h-3 rounded-full {{ $filter === 'disetujui' ? 'bg-emerald-500' : ($filter === 'ditolak' ? 'bg-rose-500' : 'bg-blue-500') }}"></span>
                    <h2 class="text-lg font-bold text-gray-800">
                        @if($filter === 'disetujui')
                            Daftar Surat Disetujui ({{ $disetujuiCount }})
                        @elseif($filter === 'ditolak')
                            Daftar Surat Tak Disetujui / Ditolak ({{ $ditolakCount }})
                        @else
                            Daftar Semua Disposisi Surat ({{ $totalCount }})
                        @endif
                    </h2>
                </div>
                <div class="text-xs text-gray-500 font-medium">
                    Menampilkan {{ $surats->count() }} data surat
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Judul Surat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status / Disposisi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal Disetujui</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Isi Ringkas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($surats as $index => $surat)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50/40 hover:to-indigo-50/40 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                
                                {{-- Judul Surat & Nomor --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-bold text-gray-900 hover:text-blue-700 transition">
                                            {{ $surat->judul }}
                                        </div>
                                        @if($surat->nomor_surat)
                                            <div class="text-xs text-gray-500 font-mono mt-0.5">
                                                No: {{ $surat->nomor_surat }}
                                            </div>
                                        @endif
                                        <div class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-user-edit mr-1"></i> Pengaju: {{ $surat->user->name ?? 'Pegawai' }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Status / Disposisi --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($surat->status === 'disetujui')
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 w-fit">
                                                <i class="fas fa-check-circle mr-1 text-emerald-600"></i> Disetujui
                                            </span>
                                            <span class="text-xs text-gray-700">
                                                <strong>Disposisi:</strong> {{ $surat->disposisi ?? 'Sub Bagian Umum' }}
                                            </span>
                                        </div>
                                    @elseif ($surat->status === 'ditolak')
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 w-fit">
                                                <i class="fas fa-times-circle mr-1 text-rose-600"></i> Tidak Disetujui
                                            </span>
                                            @if($surat->alasan_penolakan)
                                                <span class="text-xs text-red-600 max-w-xs truncate" title="{{ $surat->alasan_penolakan }}">
                                                    <strong>Alasan:</strong> {{ Str::limit($surat->alasan_penolakan, 35) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Tanggal Disetujui --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($surat->status === 'disetujui')
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($surat->signed_at ?? $surat->updated_at)->locale('id')->translatedFormat('d F Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($surat->signed_at ?? $surat->updated_at)->format('H:i') }} WIB
                                        </div>
                                    @elseif ($surat->status === 'ditolak')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                            <i class="fas fa-times mr-1.5 text-red-600"></i> Tidak Disetujui
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Isi Ringkas --}}
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-600 leading-relaxed max-w-xs sm:max-w-sm">
                                        {{ Str::limit(strip_tags($surat->isi), 90) }}
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('pimpinan.surat.preview', ['id' => $surat->id, 'from' => 'disposisi']) }}" 
                                       class="inline-flex items-center px-3.5 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 shadow-sm hover:shadow transform hover:scale-105 transition-all duration-200">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-700 mb-1">
                                            @if($filter === 'disetujui')
                                                Belum Ada Surat Disetujui
                                            @elseif($filter === 'ditolak')
                                                Belum Ada Surat Tak Disetujui
                                            @else
                                                Belum Ada Data Disposisi
                                            @endif
                                        </h3>
                                        <p class="text-xs text-gray-500 max-w-sm mb-4">
                                            @if($filter === 'disetujui')
                                                Surat yang Anda setujui dan disposisikan akan muncul dalam daftar ini.
                                            @elseif($filter === 'ditolak')
                                                Surat yang Anda tolak akan tercatat rapi di bagian ini.
                                            @else
                                                Belum ada keputusan surat yang dilakukan.
                                            @endif
                                        </p>
                                        <a href="/pimpinan/dashboard" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-arrow-left mr-2"></i> Periksa Surat di Dashboard
                                        </a>
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
@endsection
