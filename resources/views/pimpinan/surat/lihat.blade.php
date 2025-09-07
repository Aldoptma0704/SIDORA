@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

@if (isset($is_pdf) && $is_pdf)
{{-- ================================================================= --}}
{{-- PDF Rendering Section (No layout extension, pure HTML/CSS for DomPDF) --}}
{{-- ================================================================= --}}
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
            text-align: right;
        }
        .signature-block p {
            margin: 0;
        }
        .signature-image {
            height: 80px;
            width: 150px; /* Lebar eksplisit untuk tanda tangan */
            display: block;
            margin-left: auto;
            margin-right: 0;
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
    <div class="header-section">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
            <tr>
                <td class="header-logo-container">
                    {{-- Gunakan base64Logo untuk rendering PDF --}}
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
    
    @if ($surat->jenis === 'keluar_full')
        <div class="header-section">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
                <tr>
                    <td class="header-logo-container">
                        @if ($base64InstansiLogo)
                            <img src="data:image/png;base64,{{ $base64InstansiLogo }}" alt="Logo Instansi" style="height: 90px; width: auto; max-width: 90px;">
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
            <p>{{ $user->position ?? 'Jabatan' }}</p>
            <div style="margin-top: 16px;">
                @if ($user && $user->signature)
                    <div style="margin-bottom: 8px;">
                        {{-- Gunakan base64Signature untuk rendering PDF --}}
                        <img src="data:image/png;base64,{{ $base64Signature }}" alt="Tanda Tangan" class="signature-image">
                    </div>
                @else
                    <div style="font-style: italic; color: #6b7280;">Belum ada tanda tangan diunggah</div>
                @endif
                <div>
                    <p style="font-weight: bold;">{{ $user->name ?? 'Nama Pejabat' }}</p>
                    <p>NIP. {{ $user->nip ?? '..........' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

@else
{{-- ================================================================= --}}
{{-- Web Display Section (Uses layouts.app and Tailwind CSS) --}}
{{-- ================================================================= --}}
@extends('layouts.app')

@section('content')
<div class="bg-white p-8 max-w-3xl mx-auto text-sm text-black leading-relaxed">

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Berhasil</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Header Logo & Informasi Dinas --}}
    @if ($surat->jenis !== 'keluar_full')
        <div class="flex items-center justify-center mb-4">
            <div class="shrink-0">
                <img src="{{ asset('img/logo_lampung.png') }}" alt="Logo" style="height: 90px;">
            </div>
            <div class="ml-6 text-center">
                <h1 class="text-lg font-bold uppercase">PEMERINTAH PROVINSI LAMPUNG</h1>
                <h2 class="text-md font-semibold uppercase">DINAS TENAGA KERJA</h2>
                <p class="text-sm">
                    Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856 <br>
                    Laman: <a href="https://disnaker.lampungprov.go.id" class="text-blue-600 underline" target="_blank">https://disnaker.lampungprov.go.id</a> |
                    Pos-el: <a href="mailto:lampungnaker@gmail.com" class="text-blue-600 underline">lampungnaker@gmail.com</a>
                </p>
            </div>
        </div>
    @endif
    
    @if ($surat->jenis === 'keluar_full')
        <div class="flex items-center justify-center mb-4">
            @if ($surat->logo_instansi)
                <img src="{{ asset('storage/logo/' . $surat->logo_instansi) }}" alt="Logo Instansi" style="height: 90px;">
            @endif
            <div class="ml-6 text-center">
                <h1 class="text-lg font-bold uppercase">{!! $surat->nama_instansi !!}</h1>
                <p class="text-sm">{!! $surat->kontak_instansi !!}</p>
                <p class="text-sm">{!! $surat->alamat_instansi !!}</p>
            </div>
        </div>
    @endif
    <hr style="border-top: 3px solid black;" class="my-4">

    {{-- Tanggal Surat --}}
    <p class="text-right mb-4">Bandar Lampung, {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}</p>

    {{-- Info Surat --}}
    <table class="mb-4">
        <tr><td style="width: 100px;">Nomor</td><td>: {{ $surat->nomor_surat ?? '-' }}</td></tr>
        <tr><td>Sifat</td><td>: {{ $surat->sifat ?? '-' }}</td></tr>
        <tr><td>Lampiran</td><td>: {{ $surat->lampiran ?? '-' }}</td></tr>
        <tr><td>Hal</td><td>: {{ $surat->judul }}</td></tr>
    </table>

    <p>Kepada Yth.</p>
    <p class="mb-2">{{ $surat->tujuan ?? '........................................' }}</p>
    <p class="mb-4">di Tempat</p>

    <p class="mb-4">Dengan hormat,</p>
    <div class="ql-editor p-0">{!! $surat->isi !!}</div>

    <p class="mt-6">Demikian surat ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

{{-- Tanda Tangan --}}
<div class="mt-10 flex justify-end"> {{-- ADDED: flex justify-end to align the whole signature block to the right --}}
    <div class="flex flex-col items-start space-y-2"> {{-- CHANGED: items-end to items-start to left-align content within the block --}}
        <div>
            <p>{{ $user->position ?? 'Jabatan' }}</p>
        </div>

        <div class="mt-4">
            @if ($user && $user->signature)
                <div>
                    <img src="{{ asset('storage/signatures/' . $user->signature) }}" alt="Tanda Tangan" class="h-20">
                </div>
            @else
                <div class="italic text-gray-500">Belum ada tanda tangan diunggah</div>
            @endif

            <div>
                <p class="font-bold">{{ $user->name ?? 'Nama Pejabat' }}</p>
                <p>NIP. {{ $user->nip ?? '..........' }}</p>
            </div>
        </div>
    </div>
</div>


    {{-- Tombol Aksi --}}
    <div class="max-w-3xl mx-auto my-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('pimpinan.status-surat') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 shadow">
                Kembali
            </a>
            <div class="flex space-x-2">
                {{-- Tombol Edit (Hanya tampil jika status draft atau draft_pimpinan) --}}
                @if (in_array($surat->status, ['draft', 'draft_pimpinan']))
                    <a href="{{ route('pimpinan.surat.edit', $surat->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 shadow">
                        ✏️ Edit Surat
                    </a>
                @endif
                {{-- Tombol Download PDF (Mengarah ke rute khusus pimpinan) --}}
                <a href="{{ route('pimpinan.surat.download', $surat->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 shadow">
                    📄 Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@endif
