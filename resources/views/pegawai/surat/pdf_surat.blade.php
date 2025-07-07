<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat {{ $surat->nomor_surat }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.6;
            margin: 40px;
        }

        .header {
            text-align: center;
        }

        .logo-text {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-text img {
            height: 80px;
        }

        hr {
            border: 1.5px solid black;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .table-info {
            width: 60%;
            margin-bottom: 10px;
            border-spacing: 0;
        }

        .table-info td {
            padding: 2px 6px;
            vertical-align: top;
        }

        .table-info td.label {
            width: 80px;
            text-align: left;
            white-space: nowrap;
        }

        .ttd {
            margin-top: 80px;
            text-align: right;
        }

        /* Border hanya untuk tabel isi surat */
        .isi-tabel {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        .isi-tabel th, .isi-tabel td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        strong, b {
            font-weight: bold;
        }

        em, i {
            font-style: italic;
        }

        p {
            margin: 6px 0;
        }
    </style>
</head>
<body>
    {{-- Header Logo + Identitas --}}
    <table width="100%" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 80px; padding-left: 50px;">
                <img src="{{ public_path('img/logo_lampung.png') }}" alt="Logo" style="height: 80px;">
            </td>
            <td style="text-align: center; padding-right: 68px;">
                <h2 style="margin: 0; font-size: 16px;">PEMERINTAH PROVINSI LAMPUNG</h2>
                <h3 style="margin: 0; font-size: 16px;">DINAS TENAGA KERJA</h3>
                <p style="margin: 0; font-size: 11px;">
                    Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856
                </p>
                <p style="margin: 0; font-size: 11px;">
                    Laman: <u style="color:blue;">https://disnaker.lampungprov.go.id</u> |
                    Pos-el: <u style="color:blue;">lampungnaker@gmail.com</u>
                </p>
            </td>
        </tr>
    </table>

    <hr style="border: 1.5px solid black; margin-top: 0; margin-bottom: 20px;">

    <p style="text-align: right;">Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

    {{-- Informasi Surat --}}
    <table class="table-info">
        <tr>
            <td class="label">Nomor</td>
            <td>: {{ $surat->nomor_surat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Sifat</td>
            <td>: {{ $surat->sifat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td>: {{ $surat->lampiran ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hal</td>
            <td>: {{ $surat->judul }}</td>
        </tr>
    </table>

    <p>Yth. {{ $surat->tujuan }}</p>
    <p>Di — BANDAR LAMPUNG</p>

    <p>Sehubungan dengan hal tersebut, kami sampaikan bahwa:</p>

    {{-- Isi Surat --}}
    {!! str_replace('<table', '<table class="isi-tabel"', $surat->isi) !!}

    <p>Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

    {{-- Tanda Tangan --}}
    <div class="ttd">
        <p>Hormat kami,</p>
        <br><br><br>
        <p><strong>{{ $surat->penandatangan_nama }}</strong></p>
        <p>{{ $surat->penandatangan_jabatan }}</p>
        <p>NIP. {{ $surat->penandatangan_nip }}</p>
    </div>
</body>
</html>
