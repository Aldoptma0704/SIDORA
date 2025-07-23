@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

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
        <hr style="border-top: 3px solid black;" class="my-4">
    @endif

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
<div class="mt-10 text-right">
    <div>
        <p>{{ $user->position ?? 'Jabatan' }}</p>
    </div>

    <div class="flex flex-col items-end mt-4 space-y-2">
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


    {{-- Tombol Aksi --}}
    <div class="max-w-3xl mx-auto my-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('pimpinan.statussurat') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 shadow">
                Kembali
            </a>
            <a href="{{ route('surat.download', $surat->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 shadow">
                📄 Download PDF
            </a>
        </div>
    </div>
</div>
@endsection
