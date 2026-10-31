@extends('layouts.app')

{{-- Stylesheet format dokumen surat dinas --}}
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
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .ql-editor th, .ql-editor td {
        border: 1px solid #d1d5db;
        padding: 4px 8px;
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

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <p class="font-bold">Berhasil</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if ($surat->file_path)
            {{-- Tampilan untuk berkas surat asli / PDF yang diunggah --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-6 mb-8 border border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-gray-100 gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-red-100 rounded-xl text-red-600">
                            <i class="fas fa-file-pdf text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $surat->judul }}</h2>
                            <p class="text-xs text-gray-500">
                                @if($surat->jenis === 'masuk')
                                    Surat Masuk dari: <strong class="text-gray-700">{{ $surat->nama_instansi ?: 'Instansi Luar' }}</strong>
                                @else
                                    Berkas surat asli diunggah dalam format PDF
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        <i class="fas fa-external-link-alt mr-1.5"></i> Buka di Tab Baru
                    </a>
                </div>
                <iframe src="{{ asset('storage/' . $surat->file_path) }}" class="w-full h-[750px] rounded-xl border border-gray-200"></iframe>
            </div>
        @else
            <div class="bg-white p-8 max-w-3xl mx-auto text-sm text-black leading-relaxed shadow-lg rounded-xl border border-gray-200">

        {{-- ==================================================================== --}}
        {{-- BLOK HEADER SURAT KONDISIONAL --}}

        {{-- JIKA JENISNYA 'keluar_full', TAMPILKAN HEADER DINAMIS DARI DATABASE --}}
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

        {{-- JIKA JENISNYA 'keluar' (TEMPLATE), TAMPILKAN HEADER STATIS/DEFAULT --}}
        @else
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
        {{-- ==================================================================== --}}

        {{-- Tanggal --}}
        <p class="text-right mb-4">Bandar Lampung, {{ $surat->created_at->locale('id')->translatedFormat('d F Y') }}</p>

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
        
        {{-- Konten isi surat --}}
        <div class="ql-editor p-0">{!! $surat->isi !!}</div>
        
        <p class="mt-5 mb-8">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

        {{-- Tanda Tangan Surat --}}
        @php
            $ttds = $surat->penandatangans->sortBy('urutan')->values();
            $countTtd = $ttds->count();
            $tanggalSurat = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
            $kotaSurat = 'Bandar Lampung';

            // Pisahkan pembuat & pimpinan
            $ttdPembuat2 = $ttds->firstWhere('peran', 'pembuat');
            $ttdPimpinan2 = $ttds->where('peran', 'pimpinan')->values();

            // Helper render blok tanda tangan
            $renderTtdBlock = function($ttd, $suratStatus, $withDate = false, $kotaSurat = '', $tanggalSurat = '') {
                if (!$ttd) return '';
                $html = '';
                if ($withDate) {
                    $html .= '<p class="text-xs text-gray-600 mb-1">' . e($kotaSurat) . ', ' . e($tanggalSurat) . '</p>';
                } else {
                    $html .= '<p class="text-xs text-transparent mb-1 select-none">&nbsp;</p>';
                }
                $html .= '<div class="min-h-[38px] flex items-center justify-center"><p class="font-medium text-xs leading-tight">' . e($ttd->jabatan) . '</p></div>';
                $html .= '<div class="h-16 flex items-center justify-center py-1">';

                $isSigned = ($ttd->status === 'ditandatangani') || (!empty($ttd->signed_at)) || ($suratStatus === 'disetujui');

                if ($ttd->peran === 'pimpinan') {
                    if ($isSigned) {
                        $sig = $ttd->user?->signature ?? null;
                        if (!$sig && $ttd->user_id) {
                            $u = \App\Models\User::find($ttd->user_id);
                            $sig = $u?->signature;
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
                $html .= '<p class="font-bold text-xs">' . e($ttd->nama) . '</p>';
                if ($ttd->nip) {
                    $html .= '<p class="text-[11px] text-gray-600">NIP. ' . e($ttd->nip) . '</p>';
                }
                return $html;
            };
        @endphp

        @if ($countTtd === 1)
            {{-- 1 Penandatangan: Pegawai sendiri di KANAN --}}
            @php $singleTtd = $ttds->first(); @endphp
            <div class="flex justify-end mt-10 px-4">
                <div class="w-[220px] text-center">
                    {!! $renderTtdBlock($singleTtd, $surat->status, true, $kotaSurat, $tanggalSurat) !!}
                </div>
            </div>

        @elseif ($countTtd === 2)
            {{-- Layout 2 Penandatangan: Kabid di KIRI | Pegawai di KANAN --}}
            @php
                $ttd2Kabid   = $ttdPimpinan2->get(0); // Kabid (kiri)
                $ttd2Pembuat = $ttdPembuat2;           // Pegawai (kanan)
            @endphp
            <div class="flex justify-between items-start mt-10 px-4">
                {{-- KIRI: Kabid --}}
                <div class="w-[220px] text-center">
                    {!! $renderTtdBlock($ttd2Kabid, $surat->status, false) !!}
                </div>
                {{-- KANAN: Pegawai --}}
                <div class="w-[220px] text-center">
                    {!! $renderTtdBlock($ttd2Pembuat, $surat->status, true, $kotaSurat, $tanggalSurat) !!}
                </div>
            </div>

        @elseif ($countTtd === 3)
            {{-- Layout 3 Penandatangan:
                 Baris 1: [Kabid] KIRI | [Pegawai] KANAN
                 Baris 2: [Pimpinan tertinggi/Kadis] Tengah bawah "Mengetahui/Menyetujui"
            --}}
            @php
                $ttd3Kabid   = $ttdPimpinan2->get(0); // Kabid (kiri atas)
                $ttd3Kadis   = $ttdPimpinan2->get(1); // Kadis atau Sekretariat (bawah tengah)
                $ttd3Pembuat = $ttdPembuat2;           // Pegawai (kanan atas)
            @endphp
            <div class="mt-10 px-4">
                {{-- Baris 1: Kabid kiri, Pegawai kanan --}}
                <div class="flex justify-between items-start mb-2">
                    {{-- KIRI: Kabid --}}
                    <div class="w-[220px] text-center">
                        {!! $renderTtdBlock($ttd3Kabid, $surat->status, false) !!}
                    </div>
                    {{-- KANAN: Pegawai --}}
                    <div class="w-[220px] text-center">
                        {!! $renderTtdBlock($ttd3Pembuat, $surat->status, true, $kotaSurat, $tanggalSurat) !!}
                    </div>
                </div>
                {{-- Baris 2: Pimpinan tertinggi di tengah bawah --}}
                @if ($ttd3Kadis)
                    <div class="flex justify-center mt-6">
                        <div class="w-[260px] text-center">
                            <p class="text-[11px] text-gray-500 mb-0.5">Mengetahui / Menyetujui,</p>
                            {!! $renderTtdBlock($ttd3Kadis, $surat->status, false) !!}
                        </div>
                    </div>
                @endif
            </div>

        @elseif ($countTtd > 3)
            {{-- 
                Layout 4 Penandatangan (urutan berdasarkan field 'urutan'):
                Baris 1: [Kabid/Staf/UPTD (urutan 2)] di KIRI | [Pegawai/Pembuat (urutan 1)] di KANAN
                Baris 2 "Mengetahui/Menyetujui": [Sekretariat (urutan 3)] di KIRI | [Kadis (urutan 4)] di KANAN
            --}}
            @php
                $ttdsSorted    = $ttds->sortBy('urutan')->values();
                $ttdPembuat    = $ttdsSorted->firstWhere('peran', 'pembuat');
                $ttdPimpinan   = $ttdsSorted->where('peran', 'pimpinan')->values();
                // urutan 2 = Kabid, urutan 3 = Sekretariat, urutan 4 = Kadis
                $ttdKabid      = $ttdPimpinan->get(0); // urutan pimpinan pertama (Kabid)
                $ttdSekretariat= $ttdPimpinan->get(1); // urutan pimpinan kedua (Sekretariat)
                $ttdKadis      = $ttdPimpinan->get(2); // urutan pimpinan ketiga (Kadis)
            @endphp

            <div class="mt-4 px-2">
                {{-- BARIS 1: Kiri=Kabid, Kanan=Pegawai --}}
                <div class="flex justify-between items-start mb-1">
                    {{-- Kiri: Kabid/Kepala Staff/Kepala UPTD --}}
                    <div class="w-[220px] text-center">
                        {!! $renderTtdBlock($ttdKabid, $surat->status, false) !!}
                    </div>
                    {{-- Kanan: Pegawai Pembuat --}}
                    <div class="w-[220px] text-center">
                        {!! $renderTtdBlock($ttdPembuat, $surat->status, true, $kotaSurat, $tanggalSurat) !!}
                    </div>
                </div>

                {{-- BARIS 2: "Mengetahui/Menyetujui" — Kiri=Sekretariat, Kanan=Kadis --}}
                @if ($ttdSekretariat || $ttdKadis)
                    <div class="mt-3">
                        <p class="text-center text-[11px] text-gray-500 mb-2 font-medium">Mengetahui / Menyetujui,</p>
                        <div class="flex justify-between items-start">
                            {{-- Kiri bawah: Sekretariat (di bawah Kabid) --}}
                            <div class="w-[220px] text-center">
                                @if ($ttdSekretariat)
                                    {!! $renderTtdBlock($ttdSekretariat, $surat->status, false) !!}
                                @else
                                    <div class="h-24"></div>
                                @endif
                            </div>
                            {{-- Kanan bawah: Kadis (di bawah Pegawai) --}}
                            <div class="w-[220px] text-center">
                                @if ($ttdKadis)
                                    {!! $renderTtdBlock($ttdKadis, $surat->status, false) !!}
                                @else
                                    <div class="h-24"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        @else
            {{-- Default / Fallback 1 Penandatangan (Pegawai Sendiri di Kanan) --}}
            @php $singleTtd = $ttds->first(); @endphp
            <div class="flex justify-end mt-10 px-4">
                <div class="w-[220px] text-center">
                    {!! $renderTtdBlock($singleTtd, $surat->status, true, $kotaSurat, $tanggalSurat) !!}
                </div>
            </div>
        @endif
        </div>
        @endif

    {{-- Blok Tombol Aksi --}}
    <div class="max-w-3xl mx-auto my-6 px-8 sm:px-0">
        @if (request('view') == 'status')
            {{-- ===== PANEL RUTE SURAT ===== --}}
            @php
                $penandatangansSorted = $surat->penandatangans->sortBy('urutan');
                $pembuatTtd = $penandatangansSorted->firstWhere('peran', 'pembuat');
                $pimpinanTtds = $penandatangansSorted->where('peran', 'pimpinan');

                // Helper: tentukan label tahap berdasarkan jabatan/bagian user
                $getTahapLabel = function($ttd) {
                    $jabatanLower = strtolower($ttd->jabatan ?? '');
                    $namaLower    = strtolower($ttd->nama ?? '');
                    $userBagian   = strtolower($ttd->user?->bagian ?? '');
                    $userPos      = strtolower($ttd->user?->position ?? '');

                    $allText = $jabatanLower . ' ' . $userBagian . ' ' . $userPos;

                    if (str_contains($allText, 'kepala dinas') || str_contains($allText, 'kadis')) {
                        return 'Kepala Dinas';
                    }
                    if (str_contains($allText, 'sekretari')) {
                        return 'Kepala Sekretariat';
                    }
                    if (str_contains($allText, 'uptd') || str_contains($allText, 'kepala uptd')) {
                        return 'Kepala UPTD';
                    }
                    if (str_contains($allText, 'kabid') || str_contains($allText, 'kepala bidang') || str_contains($allText, 'kepala staf') || str_contains($allText, 'kepala staff')) {
                        return 'Kepala Bidang / Kepala Staf';
                    }
                    // Fallback: gunakan jabatan apa adanya, atau label generik
                    return $ttd->jabatan ?: 'Pimpinan';
                };

                // Tentukan label status rute berdasarkan progress penandatanganan
                $allDone    = $pimpinanTtds->every(fn($t) => $t->status === 'ditandatangani');
                $anyDitolak = $pimpinanTtds->contains(fn($t) => $t->status === 'ditolak');
            @endphp

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-6 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <h2 class="text-white font-bold text-base flex items-center gap-2">
                        <i class="fas fa-route"></i>
                        Rute &amp; Progres Surat
                    </h2>
                    <p class="text-blue-100 text-xs mt-0.5">Lacak posisi surat dalam alur persetujuan</p>
                </div>
                <div class="p-6">
                    {{-- STEP 1: Pembuat --}}
                    <div class="flex items-start gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-md bg-green-500">
                                <i class="fas fa-check"></i>
                            </div>
                            @if($pimpinanTtds->count() > 0)
                                <div class="w-0.5 flex-1 bg-gray-200 mt-1 min-h-[2rem]"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-6">
                            <p class="text-sm font-semibold text-gray-900">Surat Dibuat oleh Pegawai</p>
                            <p class="text-xs text-gray-500">
                                <span class="font-medium text-gray-700">{{ $pembuatTtd?->nama ?? $surat->user?->name ?? 'Pegawai' }}</span>
                                @if($pembuatTtd?->jabatan) — {{ $pembuatTtd->jabatan }} @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $surat->created_at?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
                            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            </span>
                        </div>
                    </div>

                    {{-- STEP tiap Pimpinan --}}
                    @foreach($pimpinanTtds->values() as $idx => $ttd)
                        @php
                            $isLast = $idx === ($pimpinanTtds->count() - 1);
                            $isDone = $ttd->status === 'ditandatangani';
                            $isDitolak = $ttd->status === 'ditolak';

                            if ($isDone) {
                                $stepBg = 'bg-green-500'; $stepIcon = 'fa-check';
                                $badge = 'bg-green-100 text-green-800'; $badgeIcon = 'fa-check-circle';
                                $badgeText = 'Telah disetujui ' . ($getTahapLabel($ttd));
                            } elseif ($isDitolak) {
                                $stepBg = 'bg-red-500'; $stepIcon = 'fa-times';
                                $badge = 'bg-red-100 text-red-800'; $badgeIcon = 'fa-times-circle';
                                $badgeText = 'Ditolak oleh ' . ($getTahapLabel($ttd));
                            } else {
                                $stepBg = 'bg-amber-400'; $stepIcon = 'fa-clock';
                                $badge = 'bg-amber-100 text-amber-800'; $badgeIcon = 'fa-clock';
                                $badgeText = 'Menunggu — ' . ($getTahapLabel($ttd));
                            }

                            // Label header step (nama jabatan)
                            $posLabel = $getTahapLabel($ttd);
                        @endphp

                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-md {{ $stepBg }}">
                                    <i class="fas {{ $stepIcon }}"></i>
                                </div>
                                @if(!$isLast || $surat->status === 'disetujui')
                                    <div class="w-0.5 flex-1 {{ $isDone ? 'bg-green-300' : 'bg-gray-200' }} mt-1 min-h-[2rem]"></div>
                                @endif
                            </div>
                            <div class="flex-1 pb-6">
                                <p class="text-sm font-semibold text-gray-900">{{ $posLabel }}</p>
                                <p class="text-xs text-gray-500">
                                    <span class="font-medium text-gray-700">{{ $ttd->nama ?: '—' }}</span>
                                    @if($ttd->jabatan) — {{ $ttd->jabatan }} @endif
                                </p>
                                @if($isDone && $ttd->signed_at)
                                    <p class="text-xs text-gray-400 mt-0.5">Ditandatangani {{ \Carbon\Carbon::parse($ttd->signed_at)->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
                                @elseif($isDitolak && $ttd->catatan)
                                    <p class="text-xs text-red-500 mt-0.5">Alasan: {{ $ttd->catatan }}</p>
                                @else
                                    <p class="text-xs text-amber-500 mt-0.5">Menunggu tanda tangan pejabat</p>
                                @endif
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">
                                    <i class="fas {{ $badgeIcon }} mr-1"></i> {{ $badgeText }}
                                </span>
                            </div>
                        </div>
                    @endforeach

                    {{-- STEP AKHIR: Admin --}}
                    @if($surat->status === 'disetujui')
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-md bg-blue-600 flex-shrink-0">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">Admin — Subbag Umum &amp; Kepegawaian</p>
                                <p class="text-xs text-gray-500">Surat telah masuk ke arsip admin</p>
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-check-circle mr-1"></i> Selesai Diproses
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Catatan penolakan global --}}
            @if($surat->status === 'ditolak' && $surat->alasan_penolakan)
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    <p class="text-sm font-semibold text-red-800"><i class="fas fa-times-circle mr-1"></i> Surat Ditolak</p>
                    <p class="text-sm text-red-700 mt-1">{{ $surat->alasan_penolakan }}</p>
                </div>
            @endif

            <a href="{{ route('pegawai.surat.status') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-700 hover:bg-gray-800 text-white font-semibold rounded-xl shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Status Surat
            </a>
        @else
            <div class="flex justify-between items-center">
                <a href="{{ route('pegawai.surat.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
                    ✅ Selesai &amp; Kembali
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('pegawai.surat.download', $surat->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 shadow">
                        📄 Download PDF
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection