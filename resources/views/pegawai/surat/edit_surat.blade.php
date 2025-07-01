@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow" x-data="{ jenis: '' }">
    <h2 class="text-xl font-semibold mb-6 text-gray-700">Buat Surat Baru</h2>

    <form action="{{ route('surat.store') }}" method="PUT">
        @csrf

        {{-- Dropdown Jenis Surat --}}
        <div class="mb-4">
            <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            <select name="jenis" id="jenis" x-model="jenis" class="w-full mt-1 p-2 border rounded shadow-sm focus:ring focus:border-blue-300" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="masuk">Surat Masuk</option>
                <option value="keluar">Surat Keluar</option>
            </select>
        </div>

        {{-- Wrapper seluruh form (hanya muncul jika jenis != '') --}}
        <div x-cloak x-show="jenis">
            {{-- Form Surat Masuk --}}
            <div x-show="jenis === 'masuk'" x-transition>
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="text-lg font-semibold text-gray-600 mb-4">Detail Surat Masuk</p>

                    <div class="mb-4">
                        <label for="judul_masuk" class="block text-sm font-medium text-gray-700">Judul / Perihal Surat</label>
                        <input type="text" id="judul_masuk" name="judul" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="isi_masuk" class="block text-sm font-medium text-gray-700">Isi Ringkas</label>
                        <textarea id="isi_masuk" name="isi" rows="10" class="w-full mt-1 p-2 border rounded shadow-sm"></textarea>
                    </div>
                </div>
            </div>

            {{-- Form Surat Keluar --}}
            <div x-show="jenis === 'keluar'" x-transition>
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <p class="text-lg font-semibold text-gray-600 mb-4">Detail Surat Keluar</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nomor_surat" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                            <input type="text" name="nomor_surat" id="nomor_surat" class="w-full mt-1 p-2 border rounded shadow-sm">
                        </div>
                        <div>
                            <label for="sifat" class="block text-sm font-medium text-gray-700">Sifat</label>
                            <input type="text" name="sifat" id="sifat" value="Biasa" class="w-full mt-1 p-2 border rounded shadow-sm">
                        </div>
                        <div>
                            <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
                            <input type="text" name="lampiran" id="lampiran" value="-" class="w-full mt-1 p-2 border rounded shadow-sm">
                        </div>
                        <div>
                            <label for="judul_keluar" class="block text-sm font-medium text-gray-700">Hal (Perihal)</label>
                            <input type="text" name="judul" id="judul_keluar" class="w-full mt-1 p-2 border rounded shadow-sm">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="tujuan" class="block text-sm font-medium text-gray-700">Tujuan Surat</label>
                        <textarea name="tujuan" id="tujuan" rows="4" class="w-full mt-1 p-2 border rounded shadow-sm" placeholder="Yth. Dekan Fakultas MIPA..."></textarea>
                    </div>

                    <div class="mt-4">
                        <label for="isi_keluar" class="block text-sm font-medium text-gray-700">Isi Surat</label>
                        <textarea name="isi" id="isi_keluar" rows="10" class="w-full mt-1 p-2 border rounded shadow-sm"></textarea>
                    </div>

                    <hr class="my-6">
                    <p class="text-lg font-semibold text-gray-600 mb-4">Detail Penandatangan</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="penandatangan_jabatan" placeholder="Jabatan (e.g., Plh. KEPALA DINAS,)" class="w-full mt-1 p-2 border rounded shadow-sm">
                        <input type="text" name="penandatangan_nama" placeholder="Nama Lengkap & Gelar" class="w-full mt-1 p-2 border rounded shadow-sm">
                        <textarea name="penandatangan_nip" placeholder="Pangkat & NIP" rows="2" class="w-full md:col-span-2 mt-1 p-2 border rounded shadow-sm"></textarea>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end mt-6">
                <a href="{{ route('surat.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Surat</button>
            </div>
        </div>
    </form>
</div>
@endsection
