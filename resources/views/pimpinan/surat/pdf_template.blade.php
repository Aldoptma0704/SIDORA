<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Balasan - {{ $surat->judul }}</title>
    <style>
        /* CSS khusus untuk DomPDF */
        @page {
            margin-top: 1.2cm;
            margin-bottom: 1.2cm;
            margin-left: 2cm;
            margin-right: 1.8cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.25;
            color: #000000;
        }
        p {
            margin: 0 0 3px 0;
            line-height: 1.25;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
        }
        .header-section, .info-section, .signature-section {
            page-break-inside: avoid;
        }
        .header-logo-container {
            width: 70px;
            text-align: left;
            vertical-align: middle;
            padding-right: 10px;
        }
        .header-text-container {
            text-align: center;
            vertical-align: middle;
        }
        .header-text h1 {
            font-size: 13.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 2px 0;
            line-height: 1.15;
        }
        .header-text h2 {
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 2px 0;
            line-height: 1.15;
        }
        .header-text p {
            font-size: 8.5pt;
            margin: 0;
            line-height: 1.2;
        }
        .hr-line {
            border-top: 2.5px solid black;
            border-bottom: 0.8px solid black;
            height: 1.5px;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        .letter-info td {
            padding: 1px 4px 1px 0;
            white-space: nowrap;
        }
        .signature-block {
            margin-top: 16px;
            text-align: right;
        }
        .signature-content {
            display: inline-block;
            text-align: left;
            margin-left: 320px;
        }
        .signature-block p {
            margin: 0 0 2px 0;
        }
        .signature-image {
            max-height: 50px;
            max-width: 140px;
            display: block;
            margin-left: 0;
            margin-right: auto;
        }

        /* Gaya untuk konten dari Editor */
        .ql-editor p {
            margin: 0 0 6px 0;
            line-height: 1.35;
        }
        .text-align-left, .ql-align-left, .ck-align-left, [style*="text-align: left"], [style*="text-align:left"] { text-align: left !important; }
        .text-align-center, .ql-align-center, .ck-align-center, [style*="text-align: center"], [style*="text-align:center"] { text-align: center !important; }
        .text-align-right, .ql-align-right, .ck-align-right, [style*="text-align: right"], [style*="text-align:right"] { text-align: right !important; }
        .text-align-justify, .ql-align-justify, .ck-align-justify, [style*="text-align: justify"], [style*="text-align:justify"] { text-align: justify !important; }

        .ck-indent-1, .ql-indent-1 { margin-left: 20px !important; }
        .ck-indent-2, .ql-indent-2 { margin-left: 40px !important; }
        .ck-indent-3, .ql-indent-3 { margin-left: 60px !important; }
        .ck-indent-4, .ql-indent-4 { margin-left: 80px !important; }

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
    </style>
</head>
<body>
    {{-- Conditional Header Section --}}
    @if ($surat->jenis !== 'keluar_full')
        {{-- Default Lampung Provincial Government Header (ONLY if not 'keluar_full') --}}
        <div class="header-section">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
                <tr>
                    <td class="header-logo-container">
                        {{-- Gunakan base64Logo untuk rendering PDF --}}
                        <!-- <img src="data:image/png;base64,{{ $base64Logo }}" alt="Logo" style="height: 90px; width: auto; max-width: 90px;"> -->
                        <img src="data:image/png;base64,{{ $base64Logo }}" alt="Logo" style="height: 90px; width: auto; max-width: 90px;">
                    </td>
                    <td class="header-text-container">
                        <h1 class="header-text">PEMERINTAH PROVINSI LAMPUNG</h1>
                        <h2 class="header-text">DINAS TENAGA KERJA</h2>
                        <p class="header-text">
                            Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856 <br>
                            Laman: <a href="https://disnaker.lampungprov.go.id" style="color: #2563eb; text-decoration: underline;">https://disnaker.lampungprov.go.id</a> |
                            Pos-el: <a href="mailto:lampungnaker@gmail.com" style="color: #2563eb; text-decoration: underline;">lampungnaker@gmail.com</a>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Custom Header Section (ONLY if 'keluar_full') --}}
    @if ($surat->jenis === 'keluar_full')
        <div class="header-section">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
                <tr>
                    <td class="header-logo-container">
                        @if ($base64InstansiLogo) {{-- Menggunakan variabel base64InstansiLogo --}}
                            <div class="shrink-0">
                                {{-- Menggunakan variabel base64InstansiLogo yang sudah di-encode --}}
                                <img src="{{ $base64InstansiLogo }}" alt="Logo Instansi" style="height: 90px; width: auto; max-width: 90px;">
                            </div>
                        @endif
                    </td>
                    <td class="header-text-container">
                        <h1 class="header-text">{!! $surat->nama_instansi !!}</h1>
                        <p class="header-text">{!! $surat->kontak_instansi !!}</p>
                        <p class="header-text">{!! $surat->alamat_instansi !!}</p>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <hr class="hr-line">

    <div class="info-section">
        <p style="text-align: right; margin-bottom: 16px;">Bandar Lampung, {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}</p>

        <table class="letter-info" style="margin-bottom: 16px;">
            <tr>
                <td style="width: 100px;">Nomor</td>
                <td>: {{ $surat->nomor_surat ?? '-' }}</td>
            </tr>
            <tr>
                <td style="width: 100px;">Sifat</td>
                <td>: {{ $surat->sifat ?? '-' }}</td>
            </tr>
            <tr>
                <td style="width: 100px;">Lampiran</td>
                <td>: {{ $surat->lampiran ?? '-' }}</td>
            </tr>
            <tr>
                <td style="width: 100px;">Hal</td>
                <td>: {{ $surat->judul }}</td>
            </tr>
        </table>

        <p style="margin-bottom: 8px;">Kepada Yth.</p>
        <p style="margin-bottom: 8px;">{{ $surat->tujuan ?? '........................................' }}</p>
        <p style="margin-bottom: 16px;">di - tempat</p>

        <p style="margin-bottom: 16px;">Dengan hormat,</p>
        <div class="ql-editor p-0" style="margin-bottom: 16px;">{!! $surat->isi !!}</div>

        <p style="margin-top: 24px;">Demikian surat ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
    </div>

    <div class="signature-section">
        <div class="signature-block">
            <div class="signature-content">
                <p>{{ $user->position ?? 'Jabatan' }}</p>
                <div style="margin-top: 16px;">
                    {{-- Logika untuk menampilkan tanda tangan hanya jika surat disetujui, dikirim, atau draft_pimpinan DAN ditandatangani --}}
                    @if (in_array($surat->status, ['disetujui', 'dikirim', 'draft_pimpinan']))
                        <div style="margin-bottom: 8px;">
                            <img src="{{ $base64Signature }}" alt="Tanda Tangan" class="signature-image">
                        </div>
                    @else
                        <div style="font-style: italic; color: #6b7280;">Belum ditandatangani</div>
                    @endif
                    <div>
                        <p style="font-weight: bold;">{{ $user->name ?? 'Nama Pejabat' }}</p>
                        <p>NIP. {{ $user->nip ?? '..........' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
