@extends('layouts.app')

@section('title', 'Dashboard Administrator - SIDORA')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Page -->
    <x-page-header 
        title="Dashboard Administrator" 
        description="Ringkasan operasional dokumen dinas, disposisi surat, dan manajemen pengguna SIDORA."
        icon="fas fa-chart-pie">
        <x-slot:actions>
            <a href="{{ route('admin.surat.masuk') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <i class="fas fa-inbox mr-2"></i>
                Buka Surat Masuk
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-users-cog mr-2 text-gray-500"></i>
                Kelola Pengguna
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Empat Kartu Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-stat-card 
            title="Total Surat Masuk" 
            :value="$totalSuratMasuk" 
            icon="fas fa-inbox" 
            color="blue"
            description="Dokumen dari instansi luar" />

        <x-stat-card 
            title="Total Surat Keluar" 
            :value="$totalSuratKeluar" 
            icon="fas fa-paper-plane" 
            color="emerald"
            description="Surat dinas yang diajukan" />

        <x-stat-card 
            title="Belum Didisposisi" 
            :value="$suratBelumDisposisi" 
            icon="fas fa-exclamation-circle" 
            color="amber"
            description="Surat masuk menunggu arahan" />

        <x-stat-card 
            title="Total Pengguna" 
            :value="$totalUsers" 
            icon="fas fa-users" 
            color="slate"
            description="Akun terdaftar dalam sistem" />
    </div>

    <!-- Layout Dua Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri Lebar: Surat Masuk Terbaru (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Surat Masuk Terbaru" subtitle="Daftar 6 dokumen terakhir yang diterima di sistem SIDORA">
                <x-slot:headerActions>
                    <a href="{{ route('admin.surat.masuk') }}" 
                       class="text-xs font-medium text-primary-600 hover:text-primary-800 transition-colors">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </x-slot:headerActions>

                @if($suratMasukTerbaru->isEmpty())
                    <div class="text-center py-10">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <i class="fas fa-inbox text-xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-700">Belum ada surat masuk</p>
                        <p class="text-xs text-gray-500 mt-1 mb-4">Catat surat masuk baru yang diterima dari pihak luar instansi.</p>
                        <a href="{{ route('admin.surat.masuk') }}" 
                           class="inline-flex items-center px-3.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-1.5"></i> Input Surat Masuk
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Instansi Pengirim</th>
                                    <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Perihal</th>
                                    <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-5 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($suratMasukTerbaru as $surat)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-5 py-3.5 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $surat->nama_instansi ?: 'Instansi Luar' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-600 max-w-xs truncate" title="{{ $surat->judul }}">
                                            {{ $surat->judul }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">
                                            {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if($surat->disposisi_user_id)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fas fa-check text-[9px]"></i> Didisposisi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    <i class="fas fa-clock text-[9px]"></i> Belum Disposisi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                            <a href="{{ route('admin.surat.masuk.lihat', $surat->id) }}" 
                                               class="inline-flex items-center px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-[11px] font-medium transition-colors">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Kolom Kanan Sempit: Panel Pendukung (1 Kolom) -->
        <div class="space-y-6">
            <!-- Ringkasan Pengguna Berdasarkan Role -->
            <x-card title="Pengguna per Peran" subtitle="Distribusi hak akses sistem SIDORA">
                <x-slot:headerActions>
                    <a href="{{ route('admin.users.index') }}" 
                       class="text-xs font-medium text-primary-600 hover:text-primary-800 transition-colors">
                        Kelola <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </x-slot:headerActions>

                <div class="space-y-4">
                    <!-- Admin -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 flex items-center gap-1.5">
                                <i class="fas fa-user-shield text-blue-600 w-4"></i> Administrator
                            </span>
                            <span class="font-bold text-gray-900">{{ $countAdmin }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-blue-600 h-1.5 rounded-full" 
                                 style="width: {{ $totalUsers > 0 ? ($countAdmin / $totalUsers) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Pegawai -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 flex items-center gap-1.5">
                                <i class="fas fa-user-tie text-slate-600 w-4"></i> Pegawai
                            </span>
                            <span class="font-bold text-gray-900">{{ $countPegawai }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-slate-600 h-1.5 rounded-full" 
                                 style="width: {{ $totalUsers > 0 ? ($countPegawai / $totalUsers) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Pimpinan -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 flex items-center gap-1.5">
                                <i class="fas fa-crown text-amber-600 w-4"></i> Pimpinan / Kadis
                            </span>
                            <span class="font-bold text-gray-900">{{ $countPimpinan }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-amber-500 h-1.5 rounded-full" 
                                 style="width: {{ $totalUsers > 0 ? ($countPimpinan / $totalUsers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-gray-100">
                    <a href="{{ route('admin.users.create') }}" 
                       class="w-full inline-flex items-center justify-center px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-medium transition-colors shadow-sm">
                        <i class="fas fa-user-plus mr-1.5"></i> Tambah Pengguna Baru
                    </a>
                </div>
            </x-card>

            <!-- Navigasi Cepat Pengelolaan Surat Admin -->
            <x-card title="Pengelolaan Surat" subtitle="Menu operasional harian">
                <div class="space-y-2">
                    <a href="{{ route('admin.laporan') }}" 
                       class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 text-xs font-medium text-gray-700 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-file-alt text-primary-600"></i>
                            <span>Laporan Rekapitulasi</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-[10px]"></i>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
