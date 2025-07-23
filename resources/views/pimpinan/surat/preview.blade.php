@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

@extends('layouts.app')

@section('content')
<div class="bg-white p-8 max-w-3xl mx-auto text-sm text-black leading-relaxed">

    {{-- Notifikasi jika ada pesan sukses --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Berhasil</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Header Logo & Informasi Dinas --}}
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
    <hr style="border-top: 3px solid black;" class="my-4">

    {{-- Tanggal --}}
    <p class="text-right mb-4">Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

    {{-- Info Surat --}}
    <table class="mb-4">
        <tr><td style="width: 100px;">Nomor</td><td>: {{ $surat->nomor_surat ?? '-' }}</td></tr>
        <tr><td>Sifat</td><td>: {{ $surat->sifat ?? '-' }}</td></tr>
        <tr><td>Lampiran</td><td>: {{ $surat->lampiran ?? '-' }}</td></tr>
        <tr><td>Hal</td><td>: {{ $surat->judul }}</td></tr>
    </table>

    <p>Yth. {{ $surat->tujuan ?? '...........................................' }}</p>
    <p class="mb-4">Di — {{ strtoupper('Bandar Lampung') }}</p>

    <p class="mb-4">Sehubungan dengan hal tersebut, kami sampaikan bahwa:</p>

    <div class="ql-editor">{!! $surat->isi !!}</div>

    <p class="mt-6">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

 {{-- Tanda tangan --}}
<div class="mt-10 text-right">
    
    <div class="flex flex-col items-end mt-4 space-y-2">
        <div>
            <p>{{ $surat->penandatangan_jabatan ?? 'Jabatan' }}</p>
        </div>
        {{-- ✅ Tampilkan tanda tangan jika disetujui dan ada file signature --}}
        @if ($surat->status === 'disetujui' && $surat->signed_at && $penandatangan && $penandatangan->signature)
            <div class="mb-2">
                <img src="{{ asset('storage/signatures/' . $penandatangan->signature) }}" alt="Tanda Tangan" class="h-20">
            </div>
        @else
            <div class="italic text-gray-500">
                Belum ditandatangani
            </div>
        @endif


        {{-- Nama & Jabatan --}}
        <div>
            <p class="font-bold">{{ $surat->penandatangan_nama ?? 'Nama Pejabat' }}</p>
            
            <p>NIP. {{ $surat->penandatangan_nip ?? '..........' }}</p>
        </div>
    </div>
</div> 

{{-- Tombol Aksi --}}
<div class="max-w-3xl mx-auto my-6">
    @if (request('view') == 'status')
        <a href="{{ route('surat.status_surat') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 shadow">
            Kembali
        </a>
    @else
        <div class="flex justify-between items-center">
            <a href="/pimpinan/dashboard" 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
                ✅ Selesai & Kembali
            </a>
            <div class="flex space-x-2">
                <a href="{{ route('surat.download', $surat->id) }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 shadow">
                    📄 Download PDF
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
