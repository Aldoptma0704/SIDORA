@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;

    $pimpinanUser = ($surat->pengirim_id && $surat->pengirim) 
        ? $surat->pengirim 
        : \App\Models\User::where('role', 'pimpinan')->first();
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

    /* Format Tampilan Dokumen Surat Resmi (Word-Like) */
    .ql-editor {
        padding: 0 !important;
        font-size: 1rem;
        line-height: 1.6;
        color: #111827;
    }

    .ql-editor p {
        margin-top: 0;
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }

    /* Format List / Poin */
    .ql-editor ol {
        list-style-type: decimal !important;
        margin-left: 1.5rem !important;
        padding-left: 0.5rem !important;
        margin-top: 0.35rem !important;
        margin-bottom: 0.75rem !important;
    }

    .ql-editor ul {
        list-style-type: disc !important;
        margin-left: 1.5rem !important;
        padding-left: 0.5rem !important;
        margin-top: 0.35rem !important;
        margin-bottom: 0.75rem !important;
    }

    .ql-editor li {
        margin-bottom: 0.35rem !important;
        line-height: 1.6 !important;
        display: list-item !important;
    }

    /* Pemisah Paragraf Sesudah Poin agar Tidak Menempel / Bukan Bagian Poin */
    .ql-editor ol + p,
    .ql-editor ul + p {
        margin-top: 1.25rem !important;
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
    .ql-editor .ck-indent-1, .ql-editor .ql-indent-1 { margin-left: 2rem !important; }
    .ql-editor .ck-indent-2, .ql-editor .ql-indent-2 { margin-left: 4rem !important; }
    .ql-editor .ck-indent-3, .ql-editor .ql-indent-3 { margin-left: 6rem !important; }
    .ql-editor .ck-indent-4, .ql-editor .ql-indent-4 { margin-left: 8rem !important; }

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

    @media print {
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 20px;
            box-shadow: none !important;
            border: none !important;
        }
        .no-print {
            display: none !important;
        }
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

        {{-- Header & Decision Status Banner --}}
        <div class="no-print bg-gradient-to-r from-emerald-50 to-green-50 border border-green-200 rounded-2xl p-5 mb-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 bg-green-200 text-green-800 rounded">Disetujui Pimpinan</span>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($surat->signed_at ?? $surat->updated_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">
                        Didisposisikan ke: <span class="text-green-700">{{ $surat->disposisi ?? 'Umum' }}</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pembuat: <span class="text-gray-700 font-medium">{{ $surat->user->name ?? 'Pegawai' }}</span> ({{ $surat->user->bagian ?? 'Pegawai' }})
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg border border-gray-300 shadow-sm transition">
                    <i class="fas fa-print mr-1.5"></i> Cetak Surat
                </button>
                <a href="{{ route('admin.surat.download', $surat->id) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <i class="fas fa-file-pdf mr-1.5"></i> Unduh PDF
                </a>
            </div>
        </div>

        {{-- Panel Penomoran Surat Resmi (Pergub Lampung No. 14 & 16 Th 2023) --}}
        <div class="no-print bg-white border {{ $surat->nomor_surat ? 'border-indigo-200' : 'border-amber-300 bg-amber-50/40' }} rounded-2xl p-5 mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start space-x-3.5">
                    <div class="w-11 h-11 {{ $surat->nomor_surat ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }} rounded-xl flex items-center justify-center flex-shrink-0 text-xl font-bold">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-gray-900">Penomoran Surat Resmi</h3>
                            @if($surat->nomor_surat)
                                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">
                                    <i class="fas fa-check mr-1"></i> Terdaftar
                                </span>
                            @else
                                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full border border-amber-200">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Perlu Penomoran
                                </span>
                            @endif
                        </div>
                        @if($surat->nomor_surat)
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-base sm:text-lg font-mono font-extrabold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-200">
                                    {{ $surat->nomor_surat }}
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1">Sesuai Pergub Lampung No. 14 Th 2023 (Tata Naskah Dinas) &amp; Pergub No. 16 Th 2023 (Klasifikasi Arsip).</p>
                        @else
                            <p class="text-xs text-amber-900 mt-1">Surat telah disetujui pimpinan dan siap untuk di-generate nomor registrasi resmi oleh Admin.</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">Format standar: <code>500.15.6.1/0001/N.08/10/2026</code></p>
                        @endif
                    </div>
                </div>

                <div class="shrink-0 flex items-center">
                    <button type="button" onclick="openModalGenerateNomor('{{ $surat->id }}', '{{ addslashes($surat->judul) }}', '{{ $surat->nomor_surat ?? '' }}')"
                            class="inline-flex items-center px-4 py-2.5 {{ $surat->nomor_surat ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md' }} text-xs font-bold rounded-xl transition">
                        <i class="fas {{ $surat->nomor_surat ? 'fa-edit' : 'fa-magic' }} mr-2"></i>
                        {{ $surat->nomor_surat ? 'Ubah Nomor Surat' : 'Generate Nomor Surat' }}
                    </button>
                </div>
            </div>
        </div>

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
            {{-- LEMBAR DOKUMEN RESMI (Persis seperti yang disetujui pimpinan dengan tanda tangan lengkap) --}}
            <div id="printableArea" class="bg-white p-8 sm:p-12 max-w-3xl mx-auto text-sm text-black leading-relaxed shadow-2xl rounded-xl border border-gray-200">
                
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

                {{-- TANDA TANGAN SURAT (LENGKAP SESUAI DOKUMEN DISETUJUI) --}}
                @php
                    $ttds = $surat->penandatangans->sortBy('urutan')->values();
                    $countTtd = $ttds->count();
                    $tanggalSurat = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
                    $kotaSurat = 'Bandar Lampung';

                    $ttdPembuat = $ttds->firstWhere('peran', 'pembuat');
                    $ttdPimpinan = $ttds->where('peran', 'pimpinan')->values();

                    $renderAdminTtd = function($ttd, $statusSurat, $pimpinanUser, $withDate = false, $kotaSurat = '', $tanggalSurat = '') {
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
                                $html .= '<div class="italic text-[11px] text-gray-400 py-6">(Tanda Tangan Digital)</div>';
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
                            {!! $renderAdminTtd($singleTtd, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
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
                            {!! $renderAdminTtd($ttd2Kabid, $surat->status, $pimpinanUser, false) !!}
                        </div>
                        {{-- KANAN: Pegawai --}}
                        <div class="w-[220px] text-center">
                            {!! $renderAdminTtd($ttd2Pembuat, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
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
                                {!! $renderAdminTtd($ttd3Kabid, $surat->status, $pimpinanUser, false) !!}
                            </div>
                            {{-- KANAN: Pegawai --}}
                            <div class="w-[220px] text-center">
                                {!! $renderAdminTtd($ttd3Pembuat, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                            </div>
                        </div>

                        {{-- Baris 2: Pimpinan tertinggi di tengah bawah --}}
                        @if ($ttd3Kadis)
                            <div class="flex justify-center mt-6">
                                <div class="w-[260px] text-center">
                                    <p class="text-[11px] text-gray-500 mb-0.5">Mengetahui / Menyetujui,</p>
                                    {!! $renderAdminTtd($ttd3Kadis, $surat->status, $pimpinanUser, false) !!}
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
                                {!! $renderAdminTtd($ttd4Kabid, $surat->status, $pimpinanUser, false) !!}
                            </div>
                            <div class="w-[220px] text-center">
                                {!! $renderAdminTtd($ttdPembuat, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                            </div>
                        </div>

                        {{-- BARIS 2: "Mengetahui/Menyetujui" — Kiri=Sekretariat, Kanan=Kadis --}}
                        @if ($ttd4Sekretariat || $ttd4Kadis)
                            <div class="mt-8">
                                <p class="text-center text-[11px] text-gray-500 mb-3 font-medium">Mengetahui / Menyetujui,</p>
                                <div class="flex justify-between items-start">
                                    <div class="w-[220px] text-center">
                                        @if ($ttd4Sekretariat)
                                            {!! $renderAdminTtd($ttd4Sekretariat, $surat->status, $pimpinanUser, false) !!}
                                        @else
                                            <div class="h-24"></div>
                                        @endif
                                    </div>
                                    <div class="w-[220px] text-center">
                                        @if ($ttd4Kadis)
                                            {!! $renderAdminTtd($ttd4Kadis, $surat->status, $pimpinanUser, false) !!}
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
                            {!! $renderAdminTtd($singleTtd, $surat->status, $pimpinanUser, true, $kotaSurat, $tanggalSurat) !!}
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Blok Navigasi Bawah --}}
        <div class="no-print max-w-3xl mx-auto my-6 flex justify-between items-center">
            <a href="{{ route('admin.laporan') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-xl text-xs font-semibold shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Laporan Surat
            </a>
            
            <a href="{{ route('admin.surat.download', $surat->id) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                <i class="fas fa-file-pdf mr-2"></i> Unduh Berkas PDF
            </a>
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
</script>
@endsection
