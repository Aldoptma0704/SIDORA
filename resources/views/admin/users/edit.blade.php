@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Pengguna</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold text-gray-700">Nama</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
        </div>

        {{-- ✅ Tambahan Jabatan --}}
        <div class="mb-4">
            <label for="position" class="block text-gray-700 font-medium mb-2">Jabatan</label>
            <input type="text" name="position" id="position"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-500"
                value="{{ old('position', $user->position) }}">
        </div>



        {{-- ✅ Tambahan NIP --}}
        <div class="mb-4">
            <label for="nip" class="block text-gray-700 font-medium mb-2">NIP</label>
            <input type="text" name="nip" id="nip"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-500"
                value="{{ old('nip', $user->nip) }}">
        </div>


        <div class="mb-4">
            <label for="role" class="block text-sm font-semibold text-gray-700">Peran</label>
            <select name="role" id="role" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pegawai" {{ old('role', $user->role) == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                <option value="pimpinan" {{ old('role', $user->role) == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="bagian" class="block text-sm font-semibold text-gray-700">Bagian/Subbagian</label>
            <select name="bagian" id="bagian"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                <option value="">-- Pilih Bagian --</option>
                <option value="Sub Bagian Umum dan Kepegawaian" {{ old('bagian', $user->bagian) == 'Sub Bagian Umum dan Kepegawaian' ? 'selected' : '' }}>Sub Bagian Umum dan Kepegawaian</option>
                <option value="Substansi Keuangan dan Aset" {{ old('bagian', $user->bagian) == 'Substansi Keuangan dan Aset' ? 'selected' : '' }}>Substansi Keuangan dan Aset</option>
                <option value="Substansi Perencanaan" {{ old('bagian', $user->bagian) == 'Substansi Perencanaan' ? 'selected' : '' }}>Substansi Perencanaan</option>

                <option value="Penempatan dan Perluasan Kesempatan Kerja" {{ old('bagian', $user->bagian) == 'Penempatan dan Perluasan Kesempatan Kerja' ? 'selected' : '' }}>Penempatan dan Perluasan Kesempatan Kerja</option>
                <option value="Pelatihan dan Produktivitas Tenaga Kerja" {{ old('bagian', $user->bagian) == 'Pelatihan dan Produktivitas Tenaga Kerja' ? 'selected' : '' }}>Pelatihan dan Produktivitas Tenaga Kerja</option>
                <option value="Bidang Pengawasan Ketenagakerjaan" {{ old('bagian', $user->bagian) == 'Bidang Pengawasan Ketenagakerjaan' ? 'selected' : '' }}>Bidang Pengawasan Ketenagakerjaan</option>
                <option value="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja" {{ old('bagian', $user->bagian) == 'Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja' ? 'selected' : '' }}>Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja</option>

            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 shadow">
                💾 Update
            </button>
        </div>
    </form>
</div>
@endsection
