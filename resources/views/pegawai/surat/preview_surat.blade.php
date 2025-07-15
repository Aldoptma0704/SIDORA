@extends('layouts.app')

{{-- Sisipkan stylesheet untuk Quill.js agar format teks benar --}}
@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Menghilangkan padding default dari .ql-editor agar rata kiri */
    .ql-editor.p-0 {
        padding: 0;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-200 py-8">
    <div class="bg-white p-8 max-w-3xl mx-auto text-sm text-black leading-relaxed shadow-lg">

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Berhasil</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- ==================================================================== --}}
        {{-- BLOK HEADER SURAT KONDISIONAL --}}

        {{-- JIKA JENISNYA 'keluar_full', TAMPILKAN HEADER DINAMIS DARI DATABASE --}}
        @if ($surat->jenis === 'keluar_full')
            <div class="flex items-start justify-center mb-4 space-x-6">
                @if ($surat->logo_instansi)
                    <div class="shrink-0">
                        <img src="{{ asset('storage/' . $surat->logo_instansi) }}" alt="Logo Instansi" class="h-24">
                    </div>
                @endif
                
                <div class="text-center">
                    {{-- Render konten dari CKEditor --}}
                    <div class="ql-editor p-0">{!! $surat->nama_instansi !!}</div>
                    <div class="ql-editor p-0 text-xs">{!! $surat->alamat_instansi !!}</div>
                    <div class="ql-editor p-0 text-xs">{!! $surat->kontak_instansi !!}</div>
                </div>
            </div>
            <hr class="border-t-4 border-black my-4">

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
        <p class="text-right mb-4">Bandar Lampung, {{ $surat->created_at->translatedFormat('d F Y') }}</p>

        {{-- Info Surat --}}
        <table class="mb-4">
            <tr><td class="w-24 align-top">Nomor</td><td class="align-top">:</td><td>{{ $surat->nomor_surat ?? '-' }}</td></tr>
            <tr><td class="w-24 align-top">Sifat</td><td class="align-top">:</td><td>{{ $surat->sifat ?? '-' }}</td></tr>
            <tr><td class="w-24 align-top">Lampiran</td><td class="align-top">:</td><td>{{ $surat->lampiran ?? '-' }}</td></tr>
            <tr><td class="w-24 align-top">Hal</td><td class="align-top">:</td><td>{{ $surat->judul }}</td></tr>
        </table>

        <div class="mt-6">
            <p>Yth. {{ $surat->tujuan ?? '...........................................' }}</p>
            <p class="mb-4">Di — <strong>BANDAR LAMPUNG</strong></p>
        </div>
        
        {{-- Konten isi surat dari CKEditor --}}
        <div class="ql-editor p-0">{!! $surat->isi !!}</div>
        
        <p class="mt-6">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

        {{-- Tanda tangan dengan QR Code di samping --}}
        <div class="flex justify-end mt-10">
            <div class="flex-shrink-0 mr-8 self-center">
                @if ($surat->status === 'disetujui' && $surat->signed_at)
                    <img src="data:image/png;base64, {!! base64_encode(
                        QrCode::format('png')->size(100)->generate(
                            url('/verify-surat/' . $surat->id) . '|' . $surat->penandatangan_nama . ' | ' . \Carbon\Carbon::parse($surat->signed_at)->translatedFormat('d F Y H:i')
                        )
                    ) !!}" alt="QR Code">
                @endif
            </div>
            <div class="text-left w-[300px]">
                <p>{{ $surat->penandatangan_jabatan ?? 'Jabatan' }}</p>
                <div class="h-16"></div> {{-- Spacer untuk tanda tangan --}}
                <p class="font-bold underline">{{ $surat->penandatangan_nama ?? 'Nama Pejabat' }}</p>
                <p>NIP. {{ $surat->penandatangan_nip ?? '..........' }}</p>
            </div>
        </div>

    </div>

    {{-- Blok Tombol Aksi --}}
    <div class="max-w-3xl mx-auto my-6 px-8 sm:px-0">
        @if (request('view') == 'status')
            <a href="{{ route('surat.status_surat') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 shadow">
                Kembali
            </a>
        @else
            <div class="flex justify-between items-center">
                <a href="{{ route('surat.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
                    ✅ Selesai & Kembali
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('surat.download', $surat->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 shadow">
                        📄 Download PDF
                    </a>
                    <a href="{{ route('surat.edit', $surat->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 shadow">
                        ✏️ Edit Kembali
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
