{{-- File: resources/views/pegawai/surat/edit.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- MODIFIED: Inisialisasi x-data dengan jenis surat yang ada --}}
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow" x-data="{ jenis: '{{ old('jenis', $surat->jenis) }}' }">
    {{-- MODIFIED: Judul halaman --}}
    <h2 class="text-xl font-semibold mb-6 text-gray-700">Edit Surat</h2>

    {{-- MODIFIED: Action form ke route 'update' dan tambahkan @method('PUT') --}}
    <form action="{{ route('surat.update', $surat->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Dropdown Jenis Surat --}}
        <div class="mb-4">
            <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            <select name="jenis" id="jenis" x-model="jenis" class="w-full mt-1 p-2 border rounded shadow-sm focus:ring focus:border-blue-300" required>
                <option value="">-- Pilih Jenis --</option>
                {{-- MODIFIED: Tambahkan 'selected' untuk opsi yang sesuai --}}
                <option value="masuk" @if(old('jenis', $surat->jenis) == 'masuk') selected @endif>Surat Masuk</option>
                <option value="keluar" @if(old('jenis', $surat->jenis) == 'keluar') selected @endif>Surat Keluar</option>
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
                        {{-- MODIFIED: Tambahkan value --}}
                        <input type="text" id="judul_masuk" name="judul" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('judul', $surat->judul) }}">
                    </div>
                    <div class="mb-4">
                        <label for="isi_masuk" class="block text-sm font-medium text-gray-700">Isi Ringkas</label>
                        {{-- MODIFIED: Isi textarea --}}
                        <div id="editor_masuk" class="bg-white border rounded shadow-sm min-h-[200px] p-2">{!! old('isi', $surat->isi) !!}</div>
                        <input type="hidden" name="isi" id="isi_masuk" value="{{ old('isi', $surat->isi) }}">
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
                            <input type="text" name="nomor_surat" id="nomor_surat" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('nomor_surat', $surat->nomor_surat) }}">
                        </div>
                        <div>
                            <label for="sifat" class="block text-sm font-medium text-gray-700">Sifat</label>
                            <input type="text" name="sifat" id="sifat" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('sifat', $surat->sifat) }}">
                        </div>
                        <div>
                            <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
                            <input type="text" name="lampiran" id="lampiran" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('lampiran', $surat->lampiran) }}">
                        </div>
                        <div>
                            <label for="judul_keluar" class="block text-sm font-medium text-gray-700">Hal (Perihal)</label>
                            <input type="text" name="judul" id="judul_keluar" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('judul', $surat->judul) }}">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="tujuan" class="block text-sm font-medium text-gray-700">Tujuan Surat</label>
                        <textarea name="tujuan" id="tujuan" rows="4" class="w-full mt-1 p-2 border rounded shadow-sm">{{ old('tujuan', $surat->tujuan) }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Isi Surat</label>
                        <div id="editor_keluar" class="bg-white border rounded shadow-sm min-h-[200px] p-2">{!! old('isi', $surat->isi) !!}</div>
                        <input type="hidden" name="isi" id="isi_keluar" value="{{ old('isi', $surat->isi) }}">
                    </div>

                    <hr class="my-6">
                    <p class="text-lg font-semibold text-gray-600 mb-4">Detail Penandatangan</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="penandatangan_jabatan" placeholder="Jabatan (e.g., Plh. KEPALA DINAS,)" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('penandatangan_jabatan', $surat->penandatangan_jabatan) }}">
                        <input type="text" name="penandatangan_nama" placeholder="Nama Lengkap & Gelar" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('penandatangan_nama', $surat->penandatangan_nama) }}">
                        <textarea name="penandatangan_nip" placeholder="Pangkat & NIP" rows="2" class="w-full md:col-span-2 mt-1 p-2 border rounded shadow-sm">{{ old('penandatangan_nip', $surat->penandatangan_nip) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end mt-6">
                {{-- Arahkan Batal ke halaman preview --}}
                <a href="{{ route('surat.preview', $surat->id) }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
      const jenis = @json(old('jenis', $surat->jenis));

      if (jenis === 'masuk') {
          window.initCkeditor('#editor_masuk', '#isi_masuk');
      } else if (jenis === 'keluar') {
          window.initCkeditor('#editor_keluar', '#isi_keluar');
      }
    });
</script>
@endpush
