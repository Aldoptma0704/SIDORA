<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat {{ $surat->nomor_surat ?? 'Surat Dinas' }}</title>
    <style>
        @page {
            margin: 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        /* Styling untuk Kop Surat */
        .header-table td {
            vertical-align: top;
            text-align: center;
        }
        .header-table .logo {
            width: 90px;
            text-align: center;
        }
        .header-table .logo img {
            height: 85px;
        }
        .header-table h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-table h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-table .address {
            font-size: 10pt;
            margin: 0;
        }
        
        /* Styling untuk nama instansi pada header dinamis */
        .dynamic-header .ql-editor p {
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            font-size: 14pt;
        }
        
        /* Aturan khusus untuk menimpa style pada alamat & kontak dinamis */
        .dynamic-header .address.ql-editor p {
            font-weight: normal;      /* Tidak tebal */
            text-transform: none;     /* Tidak uppercase */
            font-size: 10pt;          /* Ukuran font lebih kecil */
        }

        .dynamic-header .ql-editor {
            padding: 0; /* Hapus padding default ql-editor */
        }

        .line {
            border-top: 3px solid black;
            border-bottom: 1px solid black;
            height: 2px;
            margin-top: 8px;
            margin-bottom: 20px;
        }
        
        .text-right {
            text-align: right;
        }

        .info-table {
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 1px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 100px;
        }

        .content {
            margin-top: 20px;
            text-align: justify;
        }

        .ql-editor table {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .ql-editor th, .ql-editor td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12pt;
        }
        .ql-editor th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .signature-table {
            margin-top: 40px;
        }
        .signature-table td {
            vertical-align: top;
        }
        /* MODIFIED: Lebar kolom kosong ditambah untuk mendorong tanda tangan lebih ke kanan */
        .signature-table .spacer-column {
            width: 95%;
        }
        /* MODIFIED: Lebar kolom tanda tangan disesuaikan */
        .signature-table .signature-column {
            width: 40%;
            text-align: left;
        }
        .signature-table .spacer {
            height: 60px;
        }
        .signature-table .name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    {{-- ==================================================================== --}}
    {{-- BLOK HEADER SURAT KONDISIONAL --}}
    @if ($surat->jenis === 'keluar_full')
        <table class="header-table">
            <tr>
                <td class="logo">
                    @if ($surat->logo_instansi)
                        <img src="{{ public_path('storage/' . $surat->logo_instansi) }}" alt="Logo">
                    @endif
                </td>
                <td class="dynamic-header">
                    {{-- Nama Instansi akan menggunakan style default .dynamic-header --}}
                    <div class="ql-editor">{!! $surat->nama_instansi !!}</div>
                    
                    {{-- Alamat & Kontak akan menggunakan style override dari .address --}}
                    <div class="ql-editor address">{!! $surat->alamat_instansi !!}</div>
                    <div class="ql-editor address">{!! $surat->kontak_instansi !!}</div>
                </td>
            </tr>
        </table>
        <div class="line"></div>

    @else
        <table class="header-table">
            <tr>
                <td class="logo">
                    <img src="{{ public_path('img/logo_lampung.png') }}" alt="Logo">
                </td>
                <td>
                    <h1>PEMERINTAH PROVINSI LAMPUNG</h1>
                    <h2>DINAS TENAGA KERJA</h2>
                    <p class="address">Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856</p>
                    <p class="address">
                        Laman: <a href="#">https://disnaker.lampungprov.go.id</a> |
                        Pos-el: <a href="#">lampungnaker@gmail.com</a>
                    </p>
                </td>
            </tr>
        </table>
        <div class="line"></div>
    @endif
    {{-- ==================================================================== --}}

    <p class="text-right">Bandar Lampung, {{ $surat->created_at->translatedFormat('d F Y') }}</p>

    <table class="info-table">
        <tr>
            <td class="label">Nomor</td>
            <td>:</td>
            <td>{{ $surat->nomor_surat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Sifat</td>
            <td>:</td>
            <td>{{ $surat->sifat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td>:</td>
            <td>{{ $surat->lampiran ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hal</td>
            <td>:</td>
            <td><strong>{{ $surat->judul }}</strong></td>
        </tr>
    </table>

    <div class="content">
        <p>Yth. {{ $surat->tujuan ?? '...........................................' }}</p>
        <p style="margin-bottom: 20px;">Di — <strong>BANDAR LAMPUNG</strong></p>
        
        <div class="ql-editor">{!! $surat->isi !!}</div>
        
        <p style="margin-top: 20px;">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
    </div>

    <table class="signature-table">
        <tr>
            <td class="spacer-column">&nbsp;</td>
            <td class="signature-column">
                <p>{{ $surat->penandatangan_jabatan ?? 'Jabatan' }}</p>
                <div class="spacer">
                    @if ($surat->status === 'disetujui' && $surat->signed_at)
                        <img src="data:image/png;base64, {!! base64_encode(
                            QrCode::format('png')->size(80)->generate(
                                url('/verify-surat/' . $surat->id)
                            )
                        ) !!}" alt="QR Code">
                    @endif
                </div>
                <p class="name">{{ $surat->penandatangan_nama ?? 'Nama Pejabat' }}</p>
                <p>NIP. {{ $surat->penandatangan_nip ?? '..........' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
