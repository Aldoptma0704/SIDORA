@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;

    $pimpinanUser = ($surat->pengirim_id && $surat->pengirim) 
        ? $surat->pengirim 
        : (Auth::check() && Auth::user()->role === 'pimpinan' ? Auth::user() : \App\Models\User::where('role', 'pimpinan')->first());
@endphp

@extends('layouts.app')

@push('styles')
<style>
    /* Header Kop Surat Rapi & Tanpa Spasi Berlebih */
    .kop-header-text {
        white-space: normal !important;
        text-align: center !important;
        line-height: 1.25 !important;
    }
    .kop-header-text .kop-nama p,
    .kop-header-text .kop-nama h1,
    .kop-header-text .kop-nama h2,
    .kop-header-text .kop-nama div,
    .kop-header-text .kop-nama span {
        margin: 0 0 2px 0 !important;
        padding: 0 !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        white-space: normal !important;
        text-align: center !important;
    }
    .kop-header-text .kop-nama p:first-child {
        font-size: 1.125rem;
    }
    .kop-header-text .kop-nama p:nth-child(n+2) {
        font-size: 1rem;
    }
    .kop-header-text p {
        white-space: normal !important;
    }

    /* Format Tampilan Dokumen Surat Resmi (Word-Like & Rapi Proporsional) */
    .ql-editor {
        padding: 0 !important;
        font-size: 0.95rem;
        line-height: 1.35;
        color: #111827;
    }

    .ql-editor p {
        margin-top: 0;
        margin-bottom: 0.25rem;
        line-height: 1.35;
    }

    /* Format List / Poin */
    .ql-editor ol {
        list-style-type: decimal !important;
        margin-left: 1.5rem !important;
        padding-left: 0.25rem !important;
        margin-top: 0.2rem !important;
        margin-bottom: 0.35rem !important;
    }

    .ql-editor ul {
        list-style-type: disc !important;
        margin-left: 1.5rem !important;
        padding-left: 0.25rem !important;
        margin-top: 0.2rem !important;
        margin-bottom: 0.35rem !important;
    }

    .ql-editor li {
        margin-bottom: 0.15rem !important;
        line-height: 1.35 !important;
        display: list-item !important;
    }

    /* Pemisah Paragraf Sesudah Poin agar Rapi dan Tidak Menempel */
    .ql-editor ol + p,
    .ql-editor ul + p {
        margin-top: 0.5rem !important;
        clear: both !important;
        display: block !important;
    }

    /* Perataan Teks (Alignment) */
    .ql-editor .text-align-left, .ql-editor [style*="text-align: left"], .ql-editor [style*="text-align:left"] {
        text-align: left !important;
    }
    .ql-editor .text-align-center, .ql-editor [style*="text-align: center"], .ql-editor [style*="text-align:center"] {
        text-align: center !important;
    }
    .ql-editor .text-align-right, .ql-editor [style*="text-align: right"], .ql-editor [style*="text-align:right"] {
        text-align: right !important;
    }
    .ql-editor .text-align-justify, .ql-editor [style*="text-align: justify"], .ql-editor [style*="text-align:justify"] {
        text-align: justify !important;
    }

    /* Indentasi / Tab */
    .ql-editor .ck-indent-1, .ql-editor .ql-indent-1 { margin-left: 1.5rem !important; }
    .ql-editor .ck-indent-2, .ql-editor .ql-indent-2 { margin-left: 3rem !important; }
    .ql-editor .ck-indent-3, .ql-editor .ql-indent-3 { margin-left: 4.5rem !important; }
    .ql-editor .ck-indent-4, .ql-editor .ql-indent-4 { margin-left: 6rem !important; }

    /* Tabel Dokumen */
    .ql-editor table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .ql-editor th, .ql-editor td {
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        text-align: left;
    }
    .ql-editor th {
        background-color: #f9fafb;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-100 py-8 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-lg"></i>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Status Decision Banner --}}
        @if ($surat->status === 'disetujui')
            <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-green-200 rounded-2xl p-5 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 bg-green-200 text-green-800 rounded">Disetujui</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($surat->signed_at ?? $surat->updated_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">
                            Didisposisikan ke: <span class="text-green-700">{{ $surat->disposisi ?? 'Umum' }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('pimpinan.surat.download', $surat->id) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i class="fas fa-file-pdf mr-1.5"></i> Unduh PDF
                    </a>
                </div>
            </div>
        @elseif ($surat->status === 'ditolak')
            <div class="bg-gradient-to-r from-rose-50 to-red-50 border border-red-200 rounded-2xl p-5 mb-6 shadow-sm">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 bg-red-200 text-red-800 rounded">Tidak Disetujui</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($surat->updated_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            Alasan Penolakan:
                        </p>
                        <p class="text-sm text-red-700 mt-0.5 bg-white/60 p-2.5 rounded-lg border border-red-100">
                            {{ $surat->alasan_penolakan ?: 'Tidak ada keterangan alasan penolakan.' }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($surat->status === 'draft')
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-5 mb-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-edit text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 bg-amber-200 text-amber-800 rounded">Draft Surat Balasan</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($surat->created_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">
                            Surat ini masih berupa draft dan belum diajukan untuk verifikasi.
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <form action="{{ route('pimpinan.surat.ajukan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $surat->id }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                            <i class="fas fa-paper-plane mr-1.5"></i> Ajukan Sekarang
                        </button>
                    </form>
                    <a href="{{ route('pimpinan.surat.edit', $surat->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                        <i class="fas fa-edit mr-1.5"></i> Edit Surat
                    </a>
                </div>
            </div>
        @else
            @php
                $myUserId = auth()->id();
                $myBagian = strtolower(auth()->user()->bagian ?? '');
                $myPos    = strtolower(auth()->user()->position ?? '');
                
                $isMyTurn = ($surat->status === 'menunggu') && (
                    $surat->disposisi_user_id == $myUserId ||
                    ($surat->penandatangans->where('user_id', $myUserId)->where('status', 'menunggu')->isNotEmpty()) ||
                    (!$surat->disposisi_user_id && $surat->penandatangans->where('status', 'menunggu')->contains(function($t) use ($myBagian, $myPos) {
                        $tj = strtolower($t->jabatan ?? '');
                        return $tj && (($myPos && (str_contains($tj, $myPos) || str_contains($myPos, $tj))) || ($myBagian && str_contains($tj, $myBagian)));
                    }))
                );
            @endphp

            @if ($isMyTurn)
                {{-- Status Menunggu Persetujuan --}}
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-5 mb-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4" x-data="{ openDisposisi: false, openTolak: false }">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 bg-amber-200 text-amber-800 rounded">Menunggu Keputusan Anda</span>
                            <p class="text-sm font-medium text-gray-700 mt-0.5">Surat ini diajukan oleh <strong class="text-gray-900">{{ $surat->user->name ?? 'Pegawai' }}</strong></p>
                        </div>
                    </div>

                    {{-- Action Buttons untuk Pimpinan langsung dari Halaman Preview --}}
                    <div class="flex items-center gap-2">
                        <button @click="openDisposisi = true" type="button" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-xs font-bold rounded-xl shadow-md transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-check-circle mr-1.5"></i> Setujui Surat
                        </button>
                        <button @click="openTolak = true" type="button" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-xs font-bold rounded-xl shadow-md transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-times-circle mr-1.5"></i> Tolak Surat
                        </button>
                    </div>

                    {{-- Approval Modal (Konfirmasi Otomatis) --}}
                    <div x-show="openDisposisi" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
                        <div @click.away="openDisposisi = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 transform">
                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Persetujuan Surat</h3>
                                    <p class="text-xs text-gray-500">Surat akan diteruskan otomatis sesuai rantai tanda tangan</p>
                                </div>
                            </div>

                            {{-- Info rute berikutnya --}}
                            @php
                                $allTtds  = $surat->penandatangans->sortBy('urutan');
                                // Cari ttd saya yang sedang menunggu
                                $myTtdPos = $allTtds->where('user_id', $myUserId)->where('status', 'menunggu')->first();
                                if (!$myTtdPos) {
                                    $myTtdPos = $allTtds->where('peran', 'pimpinan')->where('status', 'menunggu')->sortBy('urutan')->first();
                                }
                                // Cari penandatangan berikutnya setelah urutan saya
                                $nextTtdPreview = $myTtdPos
                                    ? $allTtds->where('peran', 'pimpinan')->where('status', 'menunggu')->where('urutan', '>', $myTtdPos->urutan)->sortBy('urutan')->first()
                                    : null;

                                if ($nextTtdPreview) {
                                    $nextLabel = $nextTtdPreview->jabatan ?: $nextTtdPreview->nama;
                                    $nextColor = 'bg-blue-50 border-blue-200 text-blue-800';
                                    $nextIcon  = 'fa-arrow-right';
                                    $nextInfo  = "Surat akan diteruskan ke: <strong>{$nextLabel}</strong>";
                                } else {
                                    $nextColor = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                                    $nextIcon  = 'fa-building';
                                    $nextInfo  = "Ini adalah tanda tangan terakhir. Surat akan langsung masuk ke <strong>Admin (Subbag Umum & Kepegawaian)</strong>.";
                                }
                            @endphp

                            <div class="mb-5 p-4 rounded-xl border {{ $nextColor }}">
                                <p class="text-sm flex items-start gap-2">
                                    <i class="fas {{ $nextIcon }} mt-0.5 flex-shrink-0"></i>
                                    <span>{!! $nextInfo !!}</span>
                                </p>
                            </div>

                            <form method="POST" action="{{ route('pimpinan.surat.setujui', $surat->id) }}">
                                @csrf
                                {{-- Tidak ada dropdown — routing otomatis via urutan penandatangan --}}
                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" @click="openDisposisi = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-xl shadow-md">
                                        <i class="fas fa-check mr-1.5"></i> Ya, Setujui Surat
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Rejection Modal --}}
                    <div x-show="openTolak" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-cloak>
                        <div @click.away="openTolak = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 transform">
                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Tolak Permohonan Surat</h3>
                                    <p class="text-xs text-gray-500">Berikan catatan alasan penolakan bagi pegawai</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('pimpinan.surat.tolak', $surat->id) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                        Alasan Penolakan:
                                    </label>
                                    <textarea name="alasan_penolakan" rows="4" class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 transition resize-none" placeholder="Tuliskan alasan atau bagian yang perlu direvisi..." required></textarea>
                                </div>
                                
                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" @click="openTolak = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-md">
                                        <i class="fas fa-times-circle mr-1.5"></i> Tolak Surat
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif ($surat->status === 'menunggu')
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-6 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-hourglass-half text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 bg-blue-200 text-blue-800 rounded">Menunggu Giliran Penandatangan</span>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">Surat ini sedang dalam proses penandatanganan pada tahap pimpinan lainnya.</p>
                    </div>
                </div>
            @endif
        @endif

        {{-- JIKA SURAT MERUPAKAN ARSIP PDF YANG DIUNGGAH --}}
        @if ($surat->file_path)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6 mb-8 border border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-gray-100 gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-red-100 rounded-xl text-red-600">
                            <i class="fas fa-file-pdf text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $surat->judul }}</h2>
                            <p class="text-xs text-gray-500">Berkas surat asli diunggah dalam format PDF</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        <i class="fas fa-external-link-alt mr-1.5"></i> Buka di Tab Baru
                    </a>
                </div>
                <iframe src="{{ asset('storage/' . $surat->file_path) }}" class="w-full h-[750px] rounded-xl border border-gray-200"></iframe>
            </div>
        @else
            {{-- LEMBAR DOKUMEN RESMI (Persis seperti yang dibuat pegawai) --}}
            <div class="bg-white p-8 sm:p-12 max-w-3xl mx-auto text-sm text-black leading-relaxed shadow-2xl rounded-xl border border-gray-200">
                
                {{-- HEADER SURAT (KOP SURAT) --}}
                @if ($surat->jenis === 'keluar_full')
                    @php
                        $alamatVal = $surat->alamat_instansi_clean;
                        $teleponVal = $surat->telepon_instansi;
                        $lamanVal = $surat->website_instansi;
                        $posElVal = $surat->email_instansi;
                        $lamanUrl = $lamanVal ? (\Illuminate\Support\Str::startsWith($lamanVal, ['http://', 'https://']) ? $lamanVal : 'https://' . $lamanVal) : null;
                    @endphp
                    <div class="flex items-center justify-center mb-3">
                        @if ($surat->logo_instansi)
                            <div class="shrink-0 mr-6">
                                <img src="{{ asset('storage/' . $surat->logo_instansi) }}" alt="Logo Instansi" class="h-24 w-auto object-contain">
                            </div>
                        @endif
                        
                        <div class="text-center kop-header-text flex-1">
                            {{-- Nama Instansi --}}
                            <div class="kop-nama text-lg font-bold uppercase tracking-wider text-black leading-snug">
                                {!! $surat->nama_instansi_formatted !!}
                            </div>

                            {{-- Baris 1: Alamat dan Kontak/No. Telepon Instansi dalam 1 Baris --}}
                            @if ($alamatVal || $teleponVal)
                                <p class="text-xs text-black leading-tight mt-1 mb-0.5">
                                    @if ($alamatVal)
                                        <span>{{ $alamatVal }}</span>
                                    @endif
                                    @if ($alamatVal && $teleponVal)
                                        <span>, </span>
                                    @endif
                                    @if ($teleponVal)
                                        <span>Telp. {{ $teleponVal }}</span>
                                    @endif
                                </p>
                            @endif

                            {{-- Baris 2: Laman Instansi dan Pos-el dalam 1 Baris di Bawahnya --}}
                            @if ($lamanVal || $posElVal)
                                <p class="text-xs text-black leading-tight m-0">
                                    @if ($lamanVal)
                                        <span>Laman: <a href="{{ $lamanUrl }}" target="_blank" class="text-blue-600 underline">{{ $lamanVal }}</a></span>
                                    @endif
                                    @if ($lamanVal && $posElVal)
                                        <span class="mx-1 font-semibold">|</span>
                                    @endif
                                    @if ($posElVal)
                                        <span>Pos-el: <a href="mailto:{{ $posElVal }}" class="text-blue-600 underline">{{ $posElVal }}</a></span>
                                    @endif
                                </p>
                            @endif
                        </div>

                        @if ($surat->logo_instansi)
                            {{-- Spacer penyeimbang kanan agar teks kop tetap simetris tepat di tengah --}}
                            <div class="shrink-0 w-24 mr-6 hidden sm:block pointer-events-none" aria-hidden="true"></div>
                        @endif
                    </div>
                    <div class="border-t-[3px] border-b border-black h-[5px] my-4"></div>
                @else
                    {{-- Default Template Lampung --}}
                    <div class="flex items-center justify-center mb-4">
                        <div class="shrink-0">
                            <img src="{{ asset('img/logo_lampung.png') }}" alt="Logo" class="h-24">
                        </div>
                        <div class="ml-6 text-center">
                            <h1 class="text-lg font-bold uppercase">PEMERINTAH PROVINSI LAMPUNG</h1>
                            <h2 class="text-md font-semibold uppercase">DINAS TENAGA KERJA</h2>
                            <p class="text-xs">
                                Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856 <br>
                                Laman: <a href="https://disnaker.lampungprov.go.id" class="text-blue-600 underline" target="_blank">https://disnaker.lampungprov.go.id</a> |
                                Pos-el: <a href="mailto:lampungnaker@gmail.com" class="text-blue-600 underline">lampungnaker@gmail.com</a>
                            </p>
                        </div>
                    </div>
                    <hr class="border-t-4 border-black my-4">
                @endif

                {{-- Tanggal Surat Bahasa Indonesia --}}
                <p class="text-right mb-4">
                    Bandar Lampung, {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d F Y') : \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
                </p>

                {{-- Info Surat --}}
                <table class="mb-6">
                    <tr><td class="w-24 align-top">Nomor</td><td class="align-top">:</td><td>{{ $surat->nomor_surat ?: '................................................' }}</td></tr>
                    <tr><td class="w-24 align-top">Sifat</td><td class="align-top">:</td><td>{{ $surat->sifat ?? '-' }}</td></tr>
                    <tr><td class="w-24 align-top">Lampiran</td><td class="align-top">:</td><td>{{ $surat->lampiran ?? '-' }}</td></tr>
                    <tr><td class="w-24 align-top">Hal</td><td class="align-top">:</td><td>{{ $surat->judul }}</td></tr>
                </table>

                <div class="mt-5 mb-5">
                    <p>Yth. {{ $surat->tujuan ?? '...........................................' }}</p>
                    <p class="mb-5">di - tempat</p>
                </div>
                
                {{-- Konten isi surat dari Editor --}}
                <div class="ql-editor p-0">{!! $surat->isi !!}</div>
                
                <p class="mt-5 mb-8">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

                {{-- TANDA TANGAN SURAT (Sebagaimana Dibuat Oleh Pegawai) --}}
                @php
                    $ttds = $surat->penandatangans->sortBy('urutan')->values();
                    $countTtd = $ttds->count();
                    $tanggalSurat = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
                    $kotaSurat = 'Bandar Lampung';

                    $ttdPembuat = $ttds->firstWhere('peran', 'pembuat');
                    $ttdPimpinan = $ttds->where('peran', 'pimpinan')->values();

                    // Helper render blok tanda tangan
                    $renderPimpinanTtd = function($ttd, $statusSurat, $pimpinanUser, $withDate = false, $kotaSurat = '', $tanggalSurat = '') {
                        if (!$ttd) return '';
                        $html = '';
                        if ($withDate) {
                            $html .= '<p class="text-xs text-gray-600 mb-1">' . e($kotaSurat) . ', ' . e($tanggalSurat) . '</p>';
                        } else {
                            $html .= '<p class="text-xs text-transparent mb-1 select-none">&nbsp;</p>';
                        }
                        $jabatanNama = $ttd->jabatan ?: ($ttd->peran === 'pimpinan' ? ($pimpinanUser->position ?? 'Kepala Dinas') : '');
                        $html .= '<div class="min-h-[38px] flex items-center justify-center"><p class="font-medium text-xs leading-tight">' . e($jabatanNama) . '</p></div>';
                        $html .= '<div class="h-24 flex items-center justify-center py-2">';
                        
                        $isSigned = ($ttd->status === 'ditandatangani') || (!empty($ttd->signed_at)) || ($statusSurat === 'disetujui');

                        if ($ttd->peran === 'pimpinan') {
                            if ($isSigned) {
                                // Cari file tanda tangan: ttd->tanda_tangan atau user signature
                                $sig = $ttd->user?->signature ?? null;
                                if (!$sig && $ttd->user_id) {
                                    $u = \App\Models\User::find($ttd->user_id);
                                    $sig = $u?->signature;
                                }
                                if (!$sig && $pimpinanUser && $ttd->user_id == $pimpinanUser->id) {
                                    $sig = $pimpinanUser->signature;
                                }

                                if ($ttd->tanda_tangan && file_exists(storage_path('app/public/' . $ttd->tanda_tangan))) {
                                    $html .= '<img src="' . asset('storage/' . $ttd->tanda_tangan) . '" alt="Tanda Tangan Pimpinan" class="max-h-full max-w-[160px] object-contain">';
                                } elseif ($sig && file_exists(storage_path('app/public/signatures/' . $sig))) {
                                    $html .= '<img src="' . asset('storage/signatures/' . $sig) . '" alt="Tanda Tangan Pimpinan" class="max-h-full max-w-[160px] object-contain">';
                                } elseif ($ttd->tanda_tangan && file_exists(storage_path('app/public/signatures/' . basename($ttd->tanda_tangan)))) {
                                    $html .= '<img src="' . asset('storage/signatures/' . basename($ttd->tanda_tangan)) . '" alt="Tanda Tangan Pimpinan" class="max-h-full max-w-[160px] object-contain">';
                                } else {
                                    $html .= '<img src="' . asset('img/disetujui.png') . '" alt="Disetujui" class="max-h-full max-w-[160px] object-contain" title="Telah Disetujui">';
                                }
                            } else {
                                if ($ttd->status === 'ditolak') {
                                    $html .= '<div class="italic text-[11px] text-red-500 font-medium py-6 px-3 bg-red-50 rounded border border-dashed border-red-200">Ditolak</div>';
                                } else {
                                    $html .= '<div class="italic text-[11px] text-gray-400 py-6 px-3 bg-gray-50/50 rounded border border-dashed border-gray-200">(Menunggu Persetujuan)</div>';
                                }
                            }
                        } else {
                            if ($ttd->tanda_tangan && file_exists(storage_path('app/public/' . $ttd->tanda_tangan))) {
                                $html .= '<img src="' . asset('storage/' . $ttd->tanda_tangan) . '" alt="Tanda Tangan Pegawai" class="max-h-full max-w-[160px] object-contain">';
                            } elseif ($ttd->user?->signature && file_exists(storage_path('app/public/signatures/' . $ttd->user->signature))) {
                                $html .= '<img src="' . asset('storage/signatures/' . $ttd->user->signature) . '" alt="Tanda Tangan Pegawai" class="max-h-full max-w-[160px] object-contain">';
                            } else {
                                $html .= '<div class="italic text-[11px] text-gray-400 py-6">(Tanda Tangan Fisik)</div>';
                            }
                        }
                        
                        $html .= '</div>';
                        $namaPejabat = $ttd->nama ?: ($ttd->peran === 'pimpinan' ? ($pimpinanUser->name ?? 'Pimpinan') : '');
                        $html .= '<p class="font-bold text-xs">' . e($namaPejabat) . '</p>';
                        $nip = $ttd->nip ?: ($ttd->peran === 'pimpinan' ? ($pimpinanUser->nip ?? '') : '');
                        if ($nip) {
                            $html .= '<p class="text-[11px] text-gray-600">NIP. ' . e($nip) . '</p>';
                        }
                        return $html;
                    };
                @endphp

                @if ($countTtd === 1)
                    {{-- 1 Penandatangan: Pegawai sendiri di KANAN --}}
                    @php $singleTtd = $ttds->first(); @endphp
                    <div class="flex justify-end mt-10 px-4">
                        <div class="w-[220px] text-center">
                            {!! $renderPimpinanTtd($singleTtd, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                        </div>
                    </div>

                @elseif ($countTtd === 2)
                    {{-- Layout 2 Penandatangan: Kabid di KIRI | Pegawai di KANAN --}}
                    @php
                        $ttd2Kabid   = $ttdPimpinan->get(0);
                        $ttd2Pembuat = $ttdPembuat;
                    @endphp
                    <div class="flex justify-between items-start mt-10 px-4">
                        {{-- KIRI: Kabid --}}
                        <div class="w-[220px] text-center">
                            {!! $renderPimpinanTtd($ttd2Kabid, $surat->status, $pimpinanUser, false) !!}
                        </div>
                        {{-- KANAN: Pegawai --}}
                        <div class="w-[220px] text-center">
                            {!! $renderPimpinanTtd($ttd2Pembuat, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                        </div>
                    </div>

                @elseif ($countTtd === 3)
                    {{-- Layout 3 Penandatangan:
                         Baris 1: [Kabid] KIRI | [Pegawai] KANAN
                         Baris 2: [Kadis/Pimpinan tertinggi] Bawah tengah "Mengetahui / Menyetujui"
                    --}}
                    @php
                        $ttd3Kabid   = $ttdPimpinan->get(0);
                        $ttd3Kadis   = $ttdPimpinan->get(1);
                        $ttd3Pembuat = $ttdPembuat;
                    @endphp
                    <div class="mt-10 px-4">
                        <div class="flex justify-between items-start mb-2">
                            {{-- KIRI: Kabid --}}
                            <div class="w-[220px] text-center">
                                {!! $renderPimpinanTtd($ttd3Kabid, $surat->status, $pimpinanUser, false) !!}
                            </div>
                            {{-- KANAN: Pegawai --}}
                            <div class="w-[220px] text-center">
                                {!! $renderPimpinanTtd($ttd3Pembuat, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                            </div>
                        </div>

                        {{-- Baris 2: Pimpinan tertinggi di tengah bawah --}}
                        @if ($ttd3Kadis)
                            <div class="flex justify-center mt-6">
                                <div class="w-[260px] text-center">
                                    <p class="text-[11px] text-gray-500 mb-0.5">Mengetahui / Menyetujui,</p>
                                    {!! $renderPimpinanTtd($ttd3Kadis, $surat->status, $pimpinanUser, false) !!}
                                </div>
                            </div>
                        @endif
                    </div>

                @elseif ($countTtd > 3)
                    {{-- 
                        Layout 4 Penandatangan:
                        Baris 1: [Kabid/Staf/UPTD] KIRI  |  [Pegawai/Pembuat] KANAN
                        Baris 2 "Mengetahui/Menyetujui": [Sekretariat] KIRI  |  [Kadis] KANAN
                    --}}
                    @php
                        $ttd4Kabid       = $ttdPimpinan->get(0);
                        $ttd4Sekretariat = $ttdPimpinan->get(1);
                        $ttd4Kadis       = $ttdPimpinan->get(2);
                    @endphp

                    <div class="mt-10 px-4">
                        {{-- BARIS 1: Kiri=Kabid, Kanan=Pegawai --}}
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-[220px] text-center">
                                {!! $renderPimpinanTtd($ttd4Kabid, $surat->status, $pimpinanUser, false) !!}
                            </div>
                            <div class="w-[220px] text-center">
                                {!! $renderPimpinanTtd($ttdPembuat, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                            </div>
                        </div>

                        {{-- BARIS 2: "Mengetahui/Menyetujui" — Kiri=Sekretariat, Kanan=Kadis --}}
                        @if ($ttd4Sekretariat || $ttd4Kadis)
                            <div class="mt-8">
                                <p class="text-center text-[11px] text-gray-500 mb-3 font-medium">Mengetahui / Menyetujui,</p>
                                <div class="flex justify-between items-start">
                                    <div class="w-[220px] text-center">
                                        @if ($ttd4Sekretariat)
                                            {!! $renderPimpinanTtd($ttd4Sekretariat, $surat->status, $pimpinanUser, false) !!}
                                        @else
                                            <div class="h-24"></div>
                                        @endif
                                    </div>
                                    <div class="w-[220px] text-center">
                                        @if ($ttd4Kadis)
                                            {!! $renderPimpinanTtd($ttd4Kadis, $surat->status, $pimpinanUser, false) !!}
                                        @else
                                            <div class="h-24"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                @else
                    {{-- Default / Fallback 1 Penandatangan (Pegawai di Kanan) --}}
                    @php $singleTtd = $ttds->first(); @endphp
                    <div class="flex justify-end mt-10 px-4">
                        <div class="w-[220px] text-center">
                            {!! $renderPimpinanTtd($singleTtd, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Blok Navigasi Bawah --}}
        <div class="max-w-3xl mx-auto my-6 flex justify-between items-center">
            @php
                $from = request('from');
                $backUrl = match($from) {
                    'disposisi' => route('pimpinan.disposisi'),
                    'status'    => route('pimpinan.status-surat'),
                    'masuk'     => route('pimpinan.surat-masuk'),
                    default     => route('pimpinan.dashboard'),
                };
                $backLabel = match($from) {
                    'disposisi' => 'Disposisi',
                    'status'    => 'Status Surat Balasan',
                    'masuk'     => 'Surat Masuk',
                    default     => 'Dashboard',
                };
            @endphp
            <a href="{{ $backUrl }}" class="inline-flex items-center px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-xl text-xs font-semibold shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke {{ $backLabel }}
            </a>
            
            <a href="{{ route('pimpinan.surat.download', $surat->id) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                <i class="fas fa-file-pdf mr-2"></i> Unduh Berkas PDF
            </a>
        </div>

    </div>
</div>
@endsection
