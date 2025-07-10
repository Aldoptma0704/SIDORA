@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">📩 Disposisikan Surat</h2>

    <form action="{{ route('admin.disposisi.kirim') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="surat_id" class="block font-medium text-gray-700">Pilih Surat</label>
            <select name="surat_id" required class="w-full border p-2 rounded">
                @foreach ($surats as $surat)
                    <option value="{{ $surat->id }}">{{ $surat->judul }} ({{ $surat->status }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="disposisi_user_id" class="block font-medium text-gray-700">Pilih Pegawai Tujuan</label>
            <select name="disposisi_user_id" required class="w-full border p-2 rounded">
                @foreach ($pegawais as $pegawai)
                    <option value="{{ $pegawai->id }}">{{ $pegawai->name }} - {{ $pegawai->bagian }}</option>
                @endforeach
            </select>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Kirim Disposisi</button>
    </form>
</div>
@endsection
