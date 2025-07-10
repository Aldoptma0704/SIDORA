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
                <option value="Sub Bagian Umum dan Kepegawaian">Sub Bagian Umum dan Kepegawaian</option>
                <option value="Substansi Keuangan dan Aset">Substansi Keuangan dan Aset</option>
                <option value="Substansi Perencanaan">Substansi Perencanaan</option>
                <optgroup label="Penempatan dan Perluasan Kesempatan Kerja">
                    <option value="Seksi Penempatan Tenaga Kerja Dalam Negeri">Seksi Penempatan Tenaga Kerja Dalam Negeri</option>
                    <option value="Substansi Penempatan Tenaga Kerja Luar Negeri">Substansi Penempatan Tenaga Kerja Luar Negeri</option>
                    <option value="Seksi Pengembangan Informasi Pasar Kerja">Seksi Pengembangan Informasi Pasar Kerja</option>
                </optgroup>
                <optgroup label="Pelatihan dan Produktivitas Tenaga Kerja">
                    <option value="Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja">Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja</option>
                    <option value="Seksi Pengembangan Produktivitas Tenaga Kerja">Seksi Pengembangan Produktivitas Tenaga Kerja</option>
                    <option value="Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja">Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja</option>
                </optgroup>
                <optgroup label="Bidang Pengawasan Ketenagakerjaan">
                    <option value="Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan">Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan</option>
                    <option value="Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja">Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja</option>
                </optgroup>
                <optgroup label="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja">
                    <option value="Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial">Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial</option>
                    <option value="Seksi Pembinaan Syarat Kerja dan Jamsostek">Seksi Pembinaan Syarat Kerja dan Jamsostek</option>
                    <option value="Substansi Penyelesaian Perselisihan Hubungan Industrial">Substansi Penyelesaian Perselisihan Hubungan Industrial</option>
                </optgroup>
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
