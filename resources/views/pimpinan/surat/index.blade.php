@extends('layouts.app')

@section('title', 'Dashboard Pimpinan - SIDORA')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Sapaan Singkat -->
    <x-page-header 
        title="Selamat Datang, {{ Auth::user()->name }}" 
        description="Kelola telaah, persetujuan tanda tangan elektronik, dan disposisi dokumen dinas."
        icon="fas fa-file-signature">
        <x-slot:actions>
            <a href="{{ route('pimpinan.disposisi') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-paper-plane mr-2 text-gray-500"></i>
                Disposisi Surat
            </a>
            <a href="{{ route('pimpinan.surat-masuk') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <i class="fas fa-inbox mr-2"></i>
                Surat Masuk
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Notifikasi Flash Message -->
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 text-emerald-600">
                <i class="fas fa-check"></i>
            </div>
            <div class="text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center flex-shrink-0 text-rose-600">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="text-rose-800 text-sm font-medium">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Empat Kartu Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-stat-card 
            title="Perlu Ditinjau" 
            :value="$countMenunggu" 
            icon="fas fa-clock" 
            color="amber"
            description="Surat menunggu persetujuan Anda" />

        <x-stat-card 
            title="Telah Disetujui" 
            :value="$countDisetujui" 
            icon="fas fa-check-circle" 
            color="emerald"
            description="Surat yang disetujui/ditandatangani" />

        <x-stat-card 
            title="Surat Masuk Dinas" 
            :value="$countSuratMasuk" 
            icon="fas fa-inbox" 
            color="blue"
            description="Dokumen dari instansi luar" />

        <x-stat-card 
            title="Riwayat Disposisi" 
            :value="$countDisposisi" 
            icon="fas fa-paper-plane" 
            color="slate"
            description="Surat yang diarahkan ke Anda" />
    </div>

    <!-- Layout Dua Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri Lebar: Daftar Surat Perlu Persetujuan (2 Kolom) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between pb-1">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Surat Menunggu Persetujuan Anda</h2>
                    <p class="text-xs text-gray-500">Tinjau dan proses surat di bawah ini sesuai hierarki jabatan</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                    {{ $surats->count() }} Surat
                </span>
            </div>

            @forelse ($surats as $surat)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-gray-300 transition-colors">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                        <!-- Info Surat -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <x-status-badge :status="$surat->status" />
                                <span class="text-xs text-gray-400">·</span>
                                <span class="text-xs text-gray-500">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d F Y') : '-' }}
                                </span>
                                @if($surat->user)
                                    <span class="text-xs text-gray-400">·</span>
                                    <span class="text-xs text-gray-600">
                                        <i class="far fa-user mr-1 text-gray-400"></i>
                                        {{ $surat->user->name }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base font-semibold text-gray-900 mb-2 leading-snug">
                                {{ $surat->judul }}
                            </h3>

                            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">
                                {!! Str::limit(strip_tags($surat->isi), 160) !!}
                            </p>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex sm:flex-col gap-2 w-full md:w-36 flex-shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-gray-100">
                            <!-- Tombol Lihat -->
                            <a href="{{ route('pimpinan.surat.preview', $surat->id) }}" 
                               class="flex-1 md:flex-none inline-flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                                <i class="fas fa-eye mr-1.5"></i> Detail
                            </a>

                            @if($surat->status === 'menunggu')
                                <!-- Modal Konfirmasi Setujui -->
                                <div x-data="{ openSetujui: false }" class="flex-1 md:flex-none">
                                    <button @click="openSetujui = true"
                                            type="button"
                                            class="w-full inline-flex items-center justify-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition-colors shadow-sm">
                                        <i class="fas fa-check mr-1.5"></i> Setujui
                                    </button>

                                    <!-- Dialog Persetujuan -->
                                    <div x-show="openSetujui" 
                                         x-transition
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4" 
                                         x-cloak>
                                        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 text-left" @click.away="openSetujui = false">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 border border-emerald-100">
                                                    <i class="fas fa-check-circle text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-base font-bold text-gray-900">Persetujuan Dokumen</h3>
                                                    <p class="text-xs text-gray-500">Konfirmasi persetujuan tanda tangan surat</p>
                                                </div>
                                            </div>

                                            @php
                                                $myUid = auth()->id();
                                                $allSorted = $surat->penandatangans->sortBy('urutan');
                                                $myStep = $allSorted->where('user_id', $myUid)->where('status', 'menunggu')->first()
                                                          ?? $allSorted->where('peran', 'pimpinan')->where('status', 'menunggu')->sortBy('urutan')->first();
                                                $nextStep = $myStep
                                                    ? $allSorted->where('peran', 'pimpinan')->where('status', 'menunggu')->where('urutan', '>', $myStep->urutan)->sortBy('urutan')->first()
                                                    : null;
                                            @endphp

                                            <div class="mb-5 p-3.5 rounded-lg border text-xs leading-relaxed {{ $nextStep ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800' }}">
                                                @if($nextStep)
                                                    <i class="fas fa-arrow-right mr-1.5"></i>
                                                    Surat akan diteruskan ke: <strong>{{ $nextStep->jabatan ?: $nextStep->nama }}</strong>
                                                @else
                                                    <i class="fas fa-building mr-1.5"></i>
                                                    Ini verifikasi pimpinan terakhir. Surat akan langsung masuk ke <strong>Admin (Subbag Umum & Kepegawaian)</strong> untuk penomoran.
                                                @endif
                                            </div>

                                            <form method="POST" action="{{ route('pimpinan.surat.setujui', $surat->id) }}">
                                                @csrf
                                                <div class="flex justify-end gap-2.5">
                                                    <button type="button" @click="openSetujui = false"
                                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm">
                                                        Ya, Setujui Surat
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Tolak Surat -->
                                <div x-data="{ openTolak: false }" class="flex-1 md:flex-none">
                                    <button @click="openTolak = true"
                                            type="button"
                                            class="w-full inline-flex items-center justify-center px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-medium transition-colors">
                                        <i class="fas fa-times mr-1.5"></i> Tolak
                                    </button>

                                    <!-- Dialog Penolakan -->
                                    <div x-show="openTolak" 
                                         x-transition
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4" 
                                         x-cloak>
                                        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 text-left" @click.away="openTolak = false">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 bg-rose-50 rounded-lg flex items-center justify-center text-rose-600 border border-rose-100">
                                                    <i class="fas fa-times-circle text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-base font-bold text-gray-900">Tolak Pengajuan Surat</h3>
                                                    <p class="text-xs text-gray-500">Berikan catatan revisi bagi pembuat surat</p>
                                                </div>
                                            </div>

                                            <form method="POST" action="{{ route('pimpinan.surat.tolak', $surat->id) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                                        Alasan Penolakan / Catatan Perbaikan:
                                                    </label>
                                                    <textarea name="alasan_penolakan" 
                                                              rows="3" 
                                                              class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500" 
                                                              placeholder="Tuliskan bagian mana yang perlu diperbaiki..."
                                                              required></textarea>
                                                </div>
                                                
                                                <div class="flex justify-end gap-2.5">
                                                    <button type="button" @click="openTolak = false" 
                                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">
                                                        Batal
                                                    </button>
                                                    <button type="submit" 
                                                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm">
                                                        Kirim Penolakan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-10 text-center">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-double text-lg"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Semua Surat Telah Ditelaah</h3>
                    <p class="text-xs text-gray-500 mt-1">Saat ini tidak ada antrean surat yang menunggu persetujuan dari Anda.</p>
                </div>
            @endforelse
        </div>

        <!-- Kolom Kanan Sempit: Panel Pendukung (1 Kolom) -->
        <div class="space-y-6">
            <!-- Informasi Panduan Verifikasi -->
            <x-card title="Petunjuk Persetujuan" subtitle="Tata kelola tanda tangan berjenjang">
                <div class="space-y-3.5 text-xs text-gray-600 leading-relaxed">
                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold text-[10px]">
                            1
                        </div>
                        <p>Pastikan nomor, sifat, lampiran, dan perihal sudah sesuai dengan format kedinasan sebelum menyetujui.</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold text-[10px]">
                            2
                        </div>
                        <p>Persetujuan Anda akan otomatis memajukan status surat ke urutan penandatangan berikutnya atau ke Admin.</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold text-[10px]">
                            3
                        </div>
                        <p>Bila ada kesalahan substansi, gunakan tombol <strong>Tolak</strong> disertai catatan revisi yang jelas.</p>
                    </div>
                </div>
            </x-card>

            <!-- Tautan Akses Terkait -->
            <x-card title="Akses Cepat" subtitle="Navigasi ke fungsi pimpinan lainnya">
                <div class="space-y-2">
                    <a href="{{ route('pimpinan.disposisi') }}" 
                       class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 text-xs font-medium text-gray-700 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-paper-plane text-primary-600"></i>
                            <span>Disposisi Dokumen</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-[10px]"></i>
                    </a>

                    <a href="{{ route('pimpinan.surat-masuk') }}" 
                       class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 text-xs font-medium text-gray-700 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-inbox text-primary-600"></i>
                            <span>Arsip Surat Masuk</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-[10px]"></i>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
