<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat {{ $surat->nomor_surat ?? 'Surat Dinas' }}</title>
    <style>
        @page {
            margin-top: 1.2cm;
            margin-bottom: 1.2cm;
            margin-left: 2cm;
            margin-right: 1.8cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        p {
            margin: 0 0 3px 0;
            line-height: 1.25;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        /* Styling untuk Kop Surat */
        .header-table {
            width: 100%;
            margin-bottom: 2px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-table .logo {
            width: 70px;
            text-align: left;
        }
        .header-table .logo img {
            height: 68px;
            width: auto;
        }
        .header-table .logo-spacer {
            width: 70px;
        }
        .header-table .header-center {
            text-align: center;
        }
        .header-table h1 {
            font-size: 13.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            line-height: 1.15;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-table h2 {
            font-size: 12.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            line-height: 1.15;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-table .address {
            font-size: 8.5pt;
            margin: 0;
            line-height: 1.2;
            color: #000;
        }
        
        /* Styling untuk nama instansi pada header dinamis */
        .dynamic-header .kop-nama-pdf,
        .dynamic-header .kop-nama-pdf p,
        .dynamic-header .kop-nama-pdf div,
        .dynamic-header .kop-nama-pdf span,
        .dynamic-header .ql-editor p {
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 2px 0;
            font-size: 13pt;
            line-height: 1.15;
            text-align: center;
        }
        
        /* Aturan khusus untuk menimpa style pada alamat & kontak dinamis */
        .dynamic-header .address {
            font-size: 8.5pt;
            margin: 0 0 2px 0;
            line-height: 1.2;
            color: #000;
            text-align: center;
        }

        .dynamic-header .ql-editor {
            padding: 0;
            text-align: center;
        }

        .line {
            border-top: 2.5px solid #000;
            border-bottom: 0.8px solid #000;
            height: 1.5px;
            margin-top: 4px;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 16px;
        }
        .info-table td {
            padding: 1.5px 0;
            vertical-align: top;
            line-height: 1.25;
        }
        .info-table .label {
            width: 75px;
        }
        .info-table .colon {
            width: 12px;
            text-align: center;
        }
        .info-table .value {
            /* flex / fluid */
        }

        .content {
            margin-top: 14px;
            text-align: justify;
            line-height: 1.25;
        }
        .content p {
            margin: 0 0 3px 0;
        }

        .ql-editor {
            text-align: justify;
        }
        .ql-editor p {
            margin: 0 0 6px 0;
            line-height: 1.35;
        }

        /* Perataan Teks Alignment */
        .text-align-left, .ql-align-left, .ck-align-left, [style*="text-align: left"], [style*="text-align:left"] { text-align: left !important; }
        .text-align-center, .ql-align-center, .ck-align-center, [style*="text-align: center"], [style*="text-align:center"] { text-align: center !important; }
        .text-align-right, .ql-align-right, .ck-align-right, [style*="text-align: right"], [style*="text-align:right"] { text-align: right !important; }
        .text-align-justify, .ql-align-justify, .ck-align-justify, [style*="text-align: justify"], [style*="text-align:justify"] { text-align: justify !important; }

        /* Indentasi / Tab */
        .ck-indent-1, .ql-indent-1 { margin-left: 20px !important; }
        .ck-indent-2, .ql-indent-2 { margin-left: 40px !important; }
        .ck-indent-3, .ql-indent-3 { margin-left: 60px !important; }
        .ck-indent-4, .ql-indent-4 { margin-left: 80px !important; }

        /* Jarak Poin & Paragraf */
        .ql-editor ol {
            list-style-type: decimal;
            margin-top: 4px;
            margin-bottom: 6px;
            padding-left: 24px;
        }
        .ql-editor ul {
            list-style-type: disc;
            margin-top: 4px;
            margin-bottom: 6px;
            padding-left: 24px;
        }
        .ql-editor li {
            margin-bottom: 3px;
            line-height: 1.35;
        }
        .ql-editor ol + p, .ql-editor ul + p {
            margin-top: 12px !important;
            clear: both;
        }
        .ql-editor table {
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .ql-editor th, .ql-editor td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 11pt;
        }
        .ql-editor th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .signature-table {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-table td {
            vertical-align: top;
        }
        .signature-table .spacer-column {
            width: 55%;
        }
        .signature-table .signature-column {
            width: 45%;
            text-align: left;
        }
        .signature-table .spacer, .multi-signature-table .spacer {
            height: 48px;
            text-align: center;
        }
        .signature-table .name, .multi-signature-table .name {
            font-weight: bold;
            margin: 2px 0 1px 0;
        }
        .signature-table .nip, .multi-signature-table .nip {
            margin: 0;
            font-size: 10.5pt;
        }

        .multi-signature-table {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .multi-signature-table td {
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ==================================================================== --}}
    {{-- BLOK HEADER SURAT KONDISIONAL --}}
    @if ($surat->jenis === 'keluar_full')
        @php
            $alamatClean = $surat->alamat_instansi_clean;
            $teleponClean = $surat->telepon_instansi;
            $emailClean = $surat->email_instansi;
            $websiteClean = $surat->website_instansi;
        @endphp
        <table class="header-table">
            <tr>
                <td class="logo">
                    @if ($surat->logo_instansi && file_exists(storage_path('app/public/' . $surat->logo_instansi)))
                        <img src="{{ storage_path('app/public/' . $surat->logo_instansi) }}" alt="Logo">
                    @endif
                </td>
                <td class="header-center dynamic-header">
                    <div class="kop-nama-pdf">{!! $surat->nama_instansi_formatted !!}</div>
                    @if ($alamatClean || $teleponClean)
                        <p class="address">
                            {{ $alamatClean }}@if(!empty($alamatClean) && !empty($teleponClean)), @endif
                            @if(!empty($teleponClean))Telp. {{ $teleponClean }}@endif
                        </p>
                    @endif
                    @if ($websiteClean || $emailClean)
                        <p class="address">
                            @if(!empty($websiteClean))Laman: {{ $websiteClean }}@endif
                            @if(!empty($websiteClean) && !empty($emailClean)) &nbsp;|&nbsp; @endif
                            @if(!empty($emailClean))Pos-el: {{ $emailClean }}@endif
                        </p>
                    @endif
                </td>
                <td class="logo-spacer"></td>
            </tr>
        </table>
        <div class="line"></div>
        
    @else
        <table class="header-table">
            <tr>
                <td class="logo">
                    <img src="{{ public_path('img/logo_lampung.png') }}" alt="Logo">
                </td>
                <td class="header-center">
                    <h1>PEMERINTAH PROVINSI LAMPUNG</h1>
                    <h2>DINAS TENAGA KERJA</h2>
                    <p class="address">Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856</p>
                    <p class="address">
                        Laman: https://disnaker.lampungprov.go.id &nbsp;|&nbsp;
                        Pos-el: lampungnaker@gmail.com
                    </p>
                </td>
                <td class="logo-spacer"></td>
            </tr>
        </table>
        <div class="line"></div>
    @endif
    {{-- ==================================================================== --}}

    {{-- Tanggal Surat di pojok kanan atas (terpisah di atas Nomor Surat) --}}
    <table style="width: 100%; margin-bottom: 12px;">
        <tr>
            <td style="text-align: right; font-size: 11pt;">
                Bandar Lampung, {{ $surat->created_at->locale('id')->translatedFormat('d F Y') }}
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="label">Nomor</td>
            <td class="colon">:</td>
            <td class="value">{{ $surat->nomor_surat ?: '................................................' }}</td>
        </tr>
        <tr>
            <td class="label">Sifat</td>
            <td class="colon">:</td>
            <td class="value">{{ $surat->sifat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td class="colon">:</td>
            <td class="value">{{ $surat->lampiran ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hal</td>
            <td class="colon">:</td>
            <td class="value">{{ $surat->judul }}</td>
        </tr>
    </table>

    <div class="content">
        <p>Yth. {{ $surat->tujuan ?? '...........................................' }}</p>
        <p style="margin-bottom: 18px;">di - tempat</p>
        
        <div class="ql-editor">{!! $surat->isi !!}</div>
        
        <p style="margin-top: 16px; margin-bottom: 22px;">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
    </div>

    @php
        $ttds = $surat->penandatangans->sortBy('urutan')->values();
        $countTtd = $ttds->count();
        $tanggalSurat = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
        $kotaSurat = 'Bandar Lampung';

        $ttdPembuat = $ttds->firstWhere('peran', 'pembuat');
        $ttdPimpinan = $ttds->where('peran', 'pimpinan')->values();
    @endphp

    @if ($countTtd === 1)
        {{-- 1 Penandatangan (Pegawai di Kanan Bawah) --}}
        @php $singleTtd = $ttds->first(); @endphp
        <table class="signature-table">
            <tr>
                <td class="spacer-column">&nbsp;</td>
                <td class="signature-column">
                    <p style="margin-bottom: 2px;">{{ $kotaSurat }}, {{ $tanggalSurat }}</p>
                    <p style="margin-bottom: 2px;">{{ $singleTtd->jabatan ?? ($surat->penandatangan_jabatan ?? 'Jabatan') }}</p>
                    <div class="spacer" style="height: 50px; line-height: 50px; text-align: left;">
                        @if ($singleTtd && $singleTtd->tanda_tangan && file_exists(storage_path('app/public/' . $singleTtd->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $singleTtd->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($surat->status === 'disetujui' && $surat->pengirim && $surat->pengirim->signature && file_exists(storage_path('app/public/signatures/' . $surat->pengirim->signature)))
                            <img src="{{ storage_path('app/public/signatures/' . $surat->pengirim->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @endif
                    </div>
                    <p class="name">{{ $singleTtd->nama ?? ($surat->penandatangan_nama ?? 'Nama Pejabat') }}</p>
                    @if ($singleTtd && $singleTtd->nip)<p class="nip">NIP. {{ $singleTtd->nip }}</p>@endif
                </td>
            </tr>
        </table>

    @elseif ($countTtd === 2)
        {{-- 2 Penandatangan: Kiri (Kabid) & Kanan (Pegawai) --}}
        @php
            $ttd2Kabid   = $ttdPimpinan->get(0);
            $ttd2Pembuat = $ttdPembuat;
        @endphp
        <table class="multi-signature-table">
            {{-- Baris 1: Header / Tanggal --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; vertical-align: bottom;">&nbsp;</td>
                <td style="width: 50%; padding: 0 10px; vertical-align: bottom;">
                    <p style="margin-bottom: 2px;">{{ $kotaSurat }}, {{ $tanggalSurat }}</p>
                </td>
            </tr>
            {{-- Baris 2: Jabatan (Rata atas, tinggi baris otomatis menyesuaikan baris terbanyak) --}}
            <tr>
                <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                    <p style="margin-bottom: 2px;">{{ $ttd2Kabid->jabatan ?? '' }}</p>
                </td>
                <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                    <p style="margin-bottom: 2px;">{{ $ttd2Pembuat->jabatan ?? '' }}</p>
                </td>
            </tr>
            {{-- Baris 3: Ruang Tanda Tangan (Tinggi seragam 50px) --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                    <div class="spacer" style="height: 50px; line-height: 50px;">
                        @if ($ttd2Kabid && $ttd2Kabid->tanda_tangan && file_exists(storage_path('app/public/' . $ttd2Kabid->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $ttd2Kabid->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($surat->status === 'disetujui' && $ttd2Kabid && $ttd2Kabid->user && $ttd2Kabid->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd2Kabid->user->signature)))
                            <img src="{{ storage_path('app/public/signatures/' . $ttd2Kabid->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @endif
                    </div>
                </td>
                <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                    <div class="spacer" style="height: 50px; line-height: 50px;">
                        @if ($ttd2Pembuat && $ttd2Pembuat->tanda_tangan && file_exists(storage_path('app/public/' . $ttd2Pembuat->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $ttd2Pembuat->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($ttd2Pembuat && $ttd2Pembuat->user && $ttd2Pembuat->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd2Pembuat->user->signature)))
                            <img src="{{ storage_path('app/public/signatures/' . $ttd2Pembuat->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @endif
                    </div>
                </td>
            </tr>
            {{-- Baris 4: Nama (DIJAMIN 100% SEJAJAR) --}}
            <tr>
                <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                    <p class="name">{{ $ttd2Kabid->nama ?? '' }}</p>
                </td>
                <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                    <p class="name">{{ $ttd2Pembuat->nama ?? '' }}</p>
                </td>
            </tr>
            {{-- Baris 5: NIP (DIJAMIN 100% SEJAJAR) --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                    @if ($ttd2Kabid && $ttd2Kabid->nip)<p class="nip">NIP. {{ $ttd2Kabid->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                </td>
                <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                    @if ($ttd2Pembuat && $ttd2Pembuat->nip)<p class="nip">NIP. {{ $ttd2Pembuat->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                </td>
            </tr>
        </table>

    @elseif ($countTtd === 3)
        {{-- 3 Penandatangan:
             Baris 1: Kiri (Kabid) & Kanan (Pegawai)
             Baris 2: Bawah Tengah (Kadis/Pimpinan tertinggi "Mengetahui / Menyetujui")
        --}}
        @php
            $ttd3Kabid   = $ttdPimpinan->get(0);
            $ttd3Kadis   = $ttdPimpinan->get(1);
            $ttd3Pembuat = $ttdPembuat;
        @endphp
        <table class="multi-signature-table">
            {{-- Baris 1: Header / Tanggal --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; vertical-align: bottom;">&nbsp;</td>
                <td style="width: 50%; padding: 0 10px; vertical-align: bottom;">
                    <p style="margin-bottom: 2px;">{{ $kotaSurat }}, {{ $tanggalSurat }}</p>
                </td>
            </tr>
            {{-- Baris 2: Jabatan --}}
            <tr>
                <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                    <p style="margin-bottom: 2px;">{{ $ttd3Kabid->jabatan ?? '' }}</p>
                </td>
                <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                    <p style="margin-bottom: 2px;">{{ $ttd3Pembuat->jabatan ?? '' }}</p>
                </td>
            </tr>
            {{-- Baris 3: Ruang Tanda Tangan --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                    <div class="spacer" style="height: 50px; line-height: 50px;">
                        @if ($ttd3Kabid && $ttd3Kabid->tanda_tangan && file_exists(storage_path('app/public/' . $ttd3Kabid->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $ttd3Kabid->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($surat->status === 'disetujui' && $ttd3Kabid && $ttd3Kabid->user && $ttd3Kabid->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd3Kabid->user->signature)))
                            <img src="{{ storage_path('app/public/signatures/' . $ttd3Kabid->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @endif
                    </div>
                </td>
                <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                    <div class="spacer" style="height: 50px; line-height: 50px;">
                        @if ($ttd3Pembuat && $ttd3Pembuat->tanda_tangan && file_exists(storage_path('app/public/' . $ttd3Pembuat->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $ttd3Pembuat->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($ttd3Pembuat && $ttd3Pembuat->user && $ttd3Pembuat->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd3Pembuat->user->signature)))
                            <img src="{{ storage_path('app/public/signatures/' . $ttd3Pembuat->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @endif
                    </div>
                </td>
            </tr>
            {{-- Baris 4: Nama (DIJAMIN 100% SEJAJAR) --}}
            <tr>
                <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                    <p class="name">{{ $ttd3Kabid->nama ?? '' }}</p>
                </td>
                <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                    <p class="name">{{ $ttd3Pembuat->nama ?? '' }}</p>
                </td>
            </tr>
            {{-- Baris 5: NIP (DIJAMIN 100% SEJAJAR) --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                    @if ($ttd3Kabid && $ttd3Kabid->nip)<p class="nip">NIP. {{ $ttd3Kabid->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                </td>
                <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                    @if ($ttd3Pembuat && $ttd3Pembuat->nip)<p class="nip">NIP. {{ $ttd3Pembuat->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                </td>
            </tr>
        </table>

        @if ($ttd3Kadis)
            <table class="multi-signature-table" style="margin-top: 14px;">
                <tr>
                    <td style="width: 25%;"></td>
                    <td style="width: 50%; padding: 0 10px; text-align: center;">
                        <p style="margin-bottom: 2px; font-style: italic;">Mengetahui / Menyetujui,</p>
                        <p style="margin-bottom: 2px;">{{ $ttd3Kadis->jabatan }}</p>
                        <div class="spacer" style="height: 50px; line-height: 50px;">
                            @if ($ttd3Kadis->tanda_tangan && file_exists(storage_path('app/public/' . $ttd3Kadis->tanda_tangan)))
                                <img src="{{ storage_path('app/public/' . $ttd3Kadis->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                            @elseif ($surat->status === 'disetujui' && $ttd3Kadis && $ttd3Kadis->user && $ttd3Kadis->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd3Kadis->user->signature)))
                                <img src="{{ storage_path('app/public/signatures/' . $ttd3Kadis->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                            @endif
                        </div>
                        <p class="name">{{ $ttd3Kadis->nama }}</p>
                        @if ($ttd3Kadis->nip)<p class="nip">NIP. {{ $ttd3Kadis->nip }}</p>@endif
                    </td>
                    <td style="width: 25%;"></td>
                </tr>
            </table>
        @endif

    @elseif ($countTtd > 3)
        {{-- 
            Layout 4 Penandatangan:
            Baris 1 : [Kabid/Staf/UPTD] KIRI  |  [Pegawai/Pembuat] KANAN
            Baris 2 : "Mengetahui/Menyetujui"
                      [Sekretariat] KIRI      |  [Kadis] KANAN
        --}}
        @php
            $ttd4Pembuat    = $ttdPembuat;
            $ttd4Kabid      = $ttdPimpinan->get(0);
            $ttd4Sekretariat= $ttdPimpinan->get(1);
            $ttd4Kadis      = $ttdPimpinan->get(2);
        @endphp

        {{-- BARIS 1: Kiri=Kabid, Kanan=Pegawai --}}
        <table class="multi-signature-table">
            {{-- Baris 1: Header / Tanggal --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; vertical-align: bottom;">&nbsp;</td>
                <td style="width: 50%; padding: 0 10px; vertical-align: bottom;">
                    <p style="margin-bottom: 2px;">{{ $kotaSurat }}, {{ $tanggalSurat }}</p>
                </td>
            </tr>
            {{-- Baris 2: Jabatan (Rata atas, baris nama tetap sejajar berapapun panjang teks jabatan) --}}
            <tr>
                <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                    <p style="margin-bottom: 2px;">{{ $ttd4Kabid->jabatan ?? '' }}</p>
                </td>
                <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                    <p style="margin-bottom: 2px;">{{ $ttd4Pembuat->jabatan ?? '' }}</p>
                </td>
            </tr>
            {{-- Baris 3: Ruang Tanda Tangan (Tinggi seragam 50px) --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                    <div class="spacer" style="height: 50px; line-height: 50px;">
                        @if ($ttd4Kabid && $ttd4Kabid->tanda_tangan && file_exists(storage_path('app/public/' . $ttd4Kabid->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $ttd4Kabid->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($surat->status === 'disetujui' && $ttd4Kabid && $ttd4Kabid->user && $ttd4Kabid->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd4Kabid->user->signature)))
                            <img src="{{ storage_path('app/public/signatures/' . $ttd4Kabid->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @endif
                    </div>
                </td>
                <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                    <div class="spacer" style="height: 50px; line-height: 50px;">
                        @if ($ttd4Pembuat && $ttd4Pembuat->tanda_tangan && file_exists(storage_path('app/public/' . $ttd4Pembuat->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $ttd4Pembuat->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($ttd4Pembuat && $ttd4Pembuat->user && $ttd4Pembuat->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd4Pembuat->user->signature)))
                            <img src="{{ storage_path('app/public/signatures/' . $ttd4Pembuat->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @endif
                    </div>
                </td>
            </tr>
            {{-- Baris 4: Nama (DIJAMIN 100% SEJAJAR KARENA BERADA DALAM TR YANG SAMA) --}}
            <tr>
                <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                    <p class="name">{{ $ttd4Kabid->nama ?? '' }}</p>
                </td>
                <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                    <p class="name">{{ $ttd4Pembuat->nama ?? '' }}</p>
                </td>
            </tr>
            {{-- Baris 5: NIP (DIJAMIN 100% SEJAJAR KARENA BERADA DALAM TR YANG SAMA) --}}
            <tr>
                <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                    @if ($ttd4Kabid && $ttd4Kabid->nip)<p class="nip">NIP. {{ $ttd4Kabid->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                </td>
                <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                    @if ($ttd4Pembuat && $ttd4Pembuat->nip)<p class="nip">NIP. {{ $ttd4Pembuat->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                </td>
            </tr>
        </table>

        @if ($ttd4Sekretariat || $ttd4Kadis)
            {{-- BARIS 2: Mengetahui/Menyetujui — Kiri=Sekretariat, Kanan=Kadis --}}
            <table class="multi-signature-table" style="margin-top: 14px;">
                <tr>
                    <td colspan="2" style="text-align: center; padding-bottom: 4px;">
                        <em style="font-size: 10pt;">Mengetahui / Menyetujui,</em>
                    </td>
                </tr>
                {{-- Baris 1: Jabatan --}}
                <tr>
                    <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                        <p style="margin-bottom: 2px;">{{ $ttd4Sekretariat->jabatan ?? '' }}</p>
                    </td>
                    <td style="width: 50%; padding: 2px 10px; vertical-align: top;">
                        <p style="margin-bottom: 2px;">{{ $ttd4Kadis->jabatan ?? '' }}</p>
                    </td>
                </tr>
                {{-- Baris 2: Ruang Spacer / Gambar TTD --}}
                <tr>
                    <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                        <div class="spacer" style="height: 50px; line-height: 50px;">
                            @if ($ttd4Sekretariat && $ttd4Sekretariat->tanda_tangan && file_exists(storage_path('app/public/' . $ttd4Sekretariat->tanda_tangan)))
                                <img src="{{ storage_path('app/public/' . $ttd4Sekretariat->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                            @elseif ($surat->status === 'disetujui' && $ttd4Sekretariat && $ttd4Sekretariat->user && $ttd4Sekretariat->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd4Sekretariat->user->signature)))
                                <img src="{{ storage_path('app/public/signatures/' . $ttd4Sekretariat->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                            @endif
                        </div>
                    </td>
                    <td style="width: 50%; padding: 0 10px; height: 50px; vertical-align: middle; text-align: center;">
                        <div class="spacer" style="height: 50px; line-height: 50px;">
                            @if ($ttd4Kadis && $ttd4Kadis->tanda_tangan && file_exists(storage_path('app/public/' . $ttd4Kadis->tanda_tangan)))
                                <img src="{{ storage_path('app/public/' . $ttd4Kadis->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                            @elseif ($surat->status === 'disetujui' && $ttd4Kadis && $ttd4Kadis->user && $ttd4Kadis->user->signature && file_exists(storage_path('app/public/signatures/' . $ttd4Kadis->user->signature)))
                                <img src="{{ storage_path('app/public/signatures/' . $ttd4Kadis->user->signature) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                            @endif
                        </div>
                    </td>
                </tr>
                {{-- Baris 3: Nama (DIJAMIN 100% SEJAJAR) --}}
                <tr>
                    <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                        <p class="name">{{ $ttd4Sekretariat->nama ?? '' }}</p>
                    </td>
                    <td style="width: 50%; padding: 2px 10px 0 10px; vertical-align: top;">
                        <p class="name">{{ $ttd4Kadis->nama ?? '' }}</p>
                    </td>
                </tr>
                {{-- Baris 4: NIP (DIJAMIN 100% SEJAJAR) --}}
                <tr>
                    <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                        @if ($ttd4Sekretariat && $ttd4Sekretariat->nip)<p class="nip">NIP. {{ $ttd4Sekretariat->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                    </td>
                    <td style="width: 50%; padding: 0 10px; vertical-align: top;">
                        @if ($ttd4Kadis && $ttd4Kadis->nip)<p class="nip">NIP. {{ $ttd4Kadis->nip }}</p>@else<p class="nip">&nbsp;</p>@endif
                    </td>
                </tr>
            </table>
        @endif

    @else
        {{-- Default 1 Penandatangan (Kanan Bawah) --}}
        @php $singleTtd = $ttds->first(); @endphp
        <table class="signature-table">
            <tr>
                <td class="spacer-column">&nbsp;</td>
                <td class="signature-column">
                    <p style="margin-bottom: 2px;">{{ $kotaSurat }}, {{ $tanggalSurat }}</p>
                    <p style="margin-bottom: 2px;">{{ $singleTtd->jabatan ?? ($surat->penandatangan_jabatan ?? 'Jabatan') }}</p>
                    <div class="spacer" style="height: 50px; line-height: 50px; text-align: left;">
                        @if ($singleTtd && $singleTtd->tanda_tangan && file_exists(storage_path('app/public/' . $singleTtd->tanda_tangan)))
                            <img src="{{ storage_path('app/public/' . $singleTtd->tanda_tangan) }}" style="max-height: 48px; max-width: 130px; vertical-align: middle;">
                        @elseif ($surat->status === 'disetujui' && $surat->signed_at)
                            <img src="data:image/png;base64, {!! base64_encode(
                                QrCode::format('png')->size(80)->generate(
                                    url('/verify-surat/' . $surat->id)
                                )
                            ) !!}" alt="QR Code">
                        @endif
                    </div>
                    <p class="name">{{ $singleTtd->nama ?? ($surat->penandatangan_nama ?? 'Nama Pejabat') }}</p>
                    <p class="nip">NIP. {{ $singleTtd->nip ?? ($surat->penandatangan_nip ?? '..........') }}</p>
                </td>
            </tr>
        </table>
    @endif

</body>
</html>
