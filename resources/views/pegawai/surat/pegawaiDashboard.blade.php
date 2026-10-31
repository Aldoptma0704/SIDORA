@extends('layouts.app')

@section('title', 'Dashboard Pegawai - SIDORA')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Sapaan Singkat -->
    <x-page-header 
        title="Selamat Datang, {{ Auth::user()->name }}" 
        description="Pantau pengajuan, alur persetujuan, dan arsip dokumen dinas Anda."
        icon="fas fa-home">
        <x-slot:actions>
            <a href="{{ route('pegawai.surat.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Buat Surat Baru
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Empat Kartu Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-stat-card 
            title="Total Surat Diajukan" 
            :value="$totalSurat" 
            icon="fas fa-envelope" 
            color="blue"
            description="Seluruh arsip surat Anda" />

        <x-stat-card 
            title="Menunggu Persetujuan" 
            :value="$countMenunggu" 
            icon="fas fa-clock" 
            color="amber"
            description="Sedang dalam proses telaah" />

        <x-stat-card 
            title="Telah Disetujui" 
            :value="$countDisetujui" 
            icon="fas fa-check-circle" 
            color="emerald"
            description="Surat selesai & diterbitkan" />

        <x-stat-card 
            title="Perlu Revisi / Ditolak" 
            :value="$countDitolak" 
            icon="fas fa-times-circle" 
            color="rose"
            description="Perlu perbaikan oleh pemohon" />
    </div>

    <!-- Layout Dua Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri Lebar: Surat Terbaru yang Diajukan (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Pengajuan Surat Terbaru" subtitle="Daftar 6 surat terakhir yang Anda buat">
                <x-slot:headerActions>
                    <a href="{{ route('pegawai.surat.status') }}" 
                       class="text-xs font-medium text-primary-600 hover:text-primary-800 transition-colors">
                        Lihat Semua Status <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </x-slot:headerActions>

                @if($recentSurats->isEmpty())
                    <div class="text-center py-10">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <i class="fas fa-file-signature text-xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-700">Belum ada pengajuan surat</p>
                        <p class="text-xs text-gray-500 mt-1 mb-4">Mulai membuat surat baru untuk keperluan kedinasan.</p>
                        <a href="{{ route('pegawai.surat.create') }}" 
                           class="inline-flex items-center px-3.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-1.5"></i> Buat Surat Sekarang
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 -mx-5 -my-5 sm:-mx-6 sm:-my-6">
                        @foreach($recentSurats as $surat)
                            <div class="p-4 sm:p-5 hover:bg-gray-50/80 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <x-status-badge :status="$surat->status" />
                                        <span class="text-xs text-gray-400">·</span>
                                        <span class="text-xs text-gray-500">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d M Y') : '-' }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $surat->judul }}
                                    </h4>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">
                                        Nomor: {{ $surat->nomor_surat ?: 'Belum terbit' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0 self-end sm:self-center">
                                    <a href="{{ route('pegawai.surat.preview', $surat->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                                        <i class="fas fa-eye mr-1.5"></i> Detail
                                    </a>
                                    @if($surat->status === 'ditolak')
                                        <a href="{{ route('pegawai.surat.edit', $surat->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-medium transition-colors">
                                            <i class="fas fa-edit mr-1.5"></i> Revisi
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Kolom Kanan Sempit: Panel Pendukung (1 Kolom) -->
        <div class="space-y-6">
            <!-- Kartu Persentase Tingkat Persetujuan (Kompak) -->
            <x-card title="Tingkat Persetujuan" subtitle="Rasio surat yang telah disetujui">
                <div class="space-y-3">
                    <div class="flex items-baseline justify-between">
                        <span class="text-3xl font-bold text-gray-900">{{ $approvalRate }}%</span>
                        <span class="text-xs text-gray-500 font-medium">{{ $countDisetujui }} dari {{ $totalSurat }} surat</span>
                    </div>

                    <!-- Progress Bar Kompak -->
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" 
                             style="width: {{ $approvalRate }}%"></div>
                    </div>

                    <p class="text-xs text-gray-500 pt-1 leading-relaxed">
                        @if($totalSurat === 0)
                            Belum ada riwayat surat untuk dihitung.
                        @elseif($approvalRate >= 80)
                            Tingkat persetujuan sangat baik. Surat Anda sebagian besar memenuhi standar verifikasi.
                        @else
                            Perhatikan catatan revisi jika terdapat surat yang ditolak untuk proses perbaikan.
                        @endif
                    </p>
                </div>
            </x-card>

            <!-- Kartu Alur Pengajuan Surat -->
            <x-card title="Alur Pengajuan Surat" subtitle="Tahapan pemrosesan dokumen resmi">
                <ol class="relative border-l border-gray-200 space-y-4 ml-2.5 my-1">
                    <li class="pl-4">
                        <span class="absolute -left-1.5 top-1.5 w-3 h-3 bg-primary-600 rounded-full border-2 border-white ring-2 ring-primary-100"></span>
                        <h4 class="text-xs font-semibold text-gray-900">1. Pengajuan Dokumen</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Pegawai mengisi form atau mengunggah PDF surat keluar.</p>
                    </li>
                    <li class="pl-4">
                        <span class="absolute -left-1.5 top-1.5 w-3 h-3 bg-amber-500 rounded-full border-2 border-white ring-2 ring-amber-100"></span>
                        <h4 class="text-xs font-semibold text-gray-900">2. Review & Disposisi</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Pimpinan memeriksa isi surat dan memberikan persetujuan.</p>
                    </li>
                    <li class="pl-4">
                        <span class="absolute -left-1.5 top-1.5 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white ring-2 ring-emerald-100"></span>
                        <h4 class="text-xs font-semibold text-gray-900">3. Penomoran & Arsip</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Admin menerbitkan nomor resmi dan surat siap didistribusikan.</p>
                    </li>
                </ol>
            </x-card>
        </div>
    </div>
</div>
@endsection
