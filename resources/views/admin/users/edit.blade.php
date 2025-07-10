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
                <optgroup label="Penempatan dan Perluasan Kesempatan Kerja">
                    <option value="Seksi Penempatan Tenaga Kerja Dalam Negeri" {{ old('bagian', $user->bagian) == 'Seksi Penempatan Tenaga Kerja Dalam Negeri' ? 'selected' : '' }}>Seksi Penempatan Tenaga Kerja Dalam Negeri</option>
                    <option value="Substansi Penempatan Tenaga Kerja Luar Negeri" {{ old('bagian', $user->bagian) == 'Substansi Penempatan Tenaga Kerja Luar Negeri' ? 'selected' : '' }}>Substansi Penempatan Tenaga Kerja Luar Negeri</option>
                    <option value="Seksi Pengembangan Informasi Pasar Kerja" {{ old('bagian', $user->bagian) == 'Seksi Pengembangan Informasi Pasar Kerja' ? 'selected' : '' }}>Seksi Pengembangan Informasi Pasar Kerja</option>
                </optgroup>
                <optgroup label="Pelatihan dan Produktivitas Tenaga Kerja">
                    <option value="Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja" {{ old('bagian', $user->bagian) == 'Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja' ? 'selected' : '' }}>Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja</option>
                    <option value="Seksi Pengembangan Produktivitas Tenaga Kerja" {{ old('bagian', $user->bagian) == 'Seksi Pengembangan Produktivitas Tenaga Kerja' ? 'selected' : '' }}>Seksi Pengembangan Produktivitas Tenaga Kerja</option>
                    <option value="Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja" {{ old('bagian', $user->bagian) == 'Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja' ? 'selected' : '' }}>Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja</option>
                </optgroup>
                <optgroup label="Bidang Pengawasan Ketenagakerjaan">
                    <option value="Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan" {{ old('bagian', $user->bagian) == 'Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan' ? 'selected' : '' }}>Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan</option>
                    <option value="Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja" {{ old('bagian', $user->bagian) == 'Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja' ? 'selected' : '' }}>Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja</option>
                </optgroup>
                <optgroup label="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja">
                    <option value="Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial" {{ old('bagian', $user->bagian) == 'Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial' ? 'selected' : '' }}>Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial</option>
                    <option value="Seksi Pembinaan Syarat Kerja dan Jamsostek" {{ old('bagian', $user->bagian) == 'Seksi Pembinaan Syarat Kerja dan Jamsostek' ? 'selected' : '' }}>Seksi Pembinaan Syarat Kerja dan Jamsostek</option>
                    <option value="Substansi Penyelesaian Perselisihan Hubungan Industrial" {{ old('bagian', $user->bagian) == 'Substansi Penyelesaian Perselisihan Hubungan Industrial' ? 'selected' : '' }}>Substansi Penyelesaian Perselisihan Hubungan Industrial</option>
                </optgroup>
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
