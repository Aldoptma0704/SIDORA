<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Balasan - {{ $surat->judul }}</title>
    <style>
        /* CSS khusus untuk DomPDF */
        @page {
            margin: 20mm; /* Atur margin halaman A4 */
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
        }
        .header-section, .info-section, .signature-section {
            page-break-inside: avoid; /* Hindari pemisahan halaman di dalam bagian ini */
        }
        .header-logo-container {
            width: 15%;
            text-align: left;
            vertical-align: middle;
            padding-right: 15px;
        }
        .header-text-container {
            width: 85%;
            text-align: center;
            vertical-align: middle;
        }
        .header-text h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .header-text h2 {
            font-size: 12pt;
            font-weight: 600;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .header-text p {
            font-size: 9pt;
            margin: 0;
        }
        .hr-line {
            border-top: 3px solid black;
            margin-top: 16px;
            margin-bottom: 16px;
        }
        .letter-info td {
            padding-right: 8px;
            white-space: nowrap;
        }
        .signature-block {
            margin-top: 40px;
            text-align: right; /* Keeps the entire block on the right */
        }
        .signature-content { /* New class for the content within the signature block */
            display: inline-block; /* Allows content to respect text-align of parent, but its own children can be left-aligned */
            text-align: left; /* Aligns content inside this div to the left */
            margin-left: 350px; /* Adjust this value to move it further right */
        }
        .signature-block p {
            margin: 0;
        }
        .signature-image {
            height: 80px;
            width: 150px;
            display: block;
            margin-left: 0; /* Align image to the left within its container */
            margin-right: auto; /* Align image to the left within its container */
        }
        /* Gaya untuk konten dari Quill editor */
        .ql-editor p {
            margin-bottom: 1em;
        }
        .ql-editor ol, .ql-editor ul {
            margin-left: 1.5em;
            margin-bottom: 1em;
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
        <p style="margin-bottom: 16px;">di Tempat</p>

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
