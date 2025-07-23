@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">➕ Tambah Pengguna Baru</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold text-gray-700">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
        </div>

        {{-- ✅ Tambahan Jabatan --}}
        <div class="mb-4">
            <label for="position" class="block text-sm font-semibold text-gray-700">Jabatan</label>
            <input type="text" name="position" id="position" value="{{ old('position') }}"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
        </div>

        {{-- ✅ Tambahan NIP --}}
        <div class="mb-4">
            <label for="nip" class="block text-sm font-semibold text-gray-700">NIP</label>
            <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
        </div>

        <div class="mb-4">
            <label for="role" class="block text-sm font-semibold text-gray-700">Peran (Role)</label>
            <select name="role" id="role" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                <option value="pimpinan" {{ old('role') == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="bagian" class="block text-sm font-semibold text-gray-700">Bagian/Subbagian</label>
            <select name="bagian" id="bagian"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                <option value="">-- Pilih Bagian --</option>
                {{-- Pilihan bagian tetap sama --}}
                <option value="Sub Bagian Umum dan Kepegawaian">Sub Bagian Umum dan Kepegawaian</option>
                <option value="Substansi Keuangan dan Aset">Substansi Keuangan dan Aset</option>
                <option value="Substansi Perencanaan">Substansi Perencanaan</option>

                <option value="Penempatan dan Perluasan Kesempatan Kerja">Penempatan dan Perluasan Kesempatan Kerja</option>
                <option value="Pelatihan dan Produktivitas Tenaga Kerja">Pelatihan dan Produktivitas Tenaga Kerja</option>
                <option value="Bidang Pengawasan Ketenagakerjaan">Bidang Pengawasan Ketenagakerjaan</option>
                <option value="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja">Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
            <input type="password" name="password" id="password" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 shadow">
                💾 Simpan
            </button>
        </div>
    </form>
</div>
@endsection
