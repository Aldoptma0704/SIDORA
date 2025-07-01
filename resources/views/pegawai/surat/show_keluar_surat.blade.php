@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 border font-serif" id="surat-content">
    
    {{-- KOP SURAT --}}
    <div class="text-center border-b-4 border-black pb-2 mb-4">
        {{-- Ganti dengan path logo Anda --}}
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Lambang_provinsi_Lampung.svg/1200px-Lambang_provinsi_Lampung.svg.png" alt="Logo Lampung" class="h-24 mx-auto">
        <h1 class="text-xl font-bold">PEMERINTAH PROVINSI LAMPUNG</h1>
        <h2 class="text-2xl font-bold">DINAS TENAGA KERJA</h2>
        <p class="text-sm">Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252605-258638-262817 Fac. 262856</p>
    </div>

    <div class="flex justify-end mb-4">
        <p>Bandar Lampung, {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="grid grid-cols-2">
        <div>
            <table>
                <tr><td class="pr-2">Nomor</td><td>: {{ $surat->nomor_surat }}</td></tr>
                <tr><td class="pr-2">Sifat</td><td>: {{ $surat->sifat }}</td></tr>
                <tr><td class="pr-2">Lampiran</td><td>: {{ $surat->lampiran }}</td></tr>
                <tr><td class="pr-2 font-bold">Hal</td><td class="font-bold">: {{ $surat->judul }}</td></tr>
            </table>
        </div>
        <div class="pl-8">
             <p>Yth.</p>
             <div class="whitespace-pre-wrap">{!! nl2br(e($surat->tujuan)) !!}</div>
        </div>
    </div>
    
    <div class="mt-8">
        <div class="whitespace-pre-wrap leading-relaxed">
            {!! nl2br(e($surat->isi)) !!}
        </div>

        <div class="flex justify-end mt-12">
            <div class="w-1/2 text-center">
                <p>{!! nl2br(e($surat->penandatangan_jabatan)) !!}</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $surat->penandatangan_nama }}</p>
                <div class="whitespace-pre-wrap">{!! nl2br(e($surat->penandatangan_nip)) !!}</div>
            </div>
        </div>
    </div>
</div>
@endsection