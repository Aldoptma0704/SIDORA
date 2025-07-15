{{-- File: resources/views/pegawai/surat/edit.blade.php --}}
@extends('layouts.app')

{{-- Sisipkan stylesheet untuk CKEditor jika diperlukan --}}
@push('styles')
    {{-- <link href="path/to/your/ckeditor.css" rel="stylesheet"> --}}
@endpush


@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow" 
     x-data="{
        jenis: '{{ old('jenis', $surat->jenis) }}',
        
        // Inisialisasi semua editor yang diperlukan
        initAllEditors() {
            // Gunakan $nextTick untuk memastikan elemen sudah terlihat di DOM
            this.$nextTick(() => {
                // Inisialisasi editor untuk 'Isi Surat'
                window.initCkeditor('#editor_isi', '#isi_hidden');
                
                // Inisialisasi editor untuk header jika jenisnya 'keluar_full'
                if (this.jenis === 'keluar_full') {
                    window.initCkeditor('#editor_nama_instansi', '#isi_nama_instansi_hidden');
                    window.initCkeditor('#editor_kontak_instansi', '#isi_kontak_instansi_hidden');
                    window.initCkeditor('#editor_alamat_instansi', '#isi_alamat_instansi_hidden');
                }
            });
        }
     }"
     x-init="initAllEditors()"> {{-- Jalankan inisialisasi saat halaman dimuat --}}

    <h2 class="text-xl font-semibold mb-6 text-gray-700">Edit Surat</h2>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Dropdown Jenis Surat --}}
        <div class="mb-4">
            <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            {{-- REMOVED: Kondisi @if selected dihapus, x-model sudah menanganinya --}}
            <select name="jenis" id="jenis" x-model="jenis" @change="initAllEditors()" class="w-full mt-1 p-2 border-2 border-gray-300 rounded shadow-sm">
                <option value="keluar_full">Surat Keluar (Dengan Kop Surat)</option>
                <option value="keluar">Surat Keluar (Template Standar)</option>
            </select>
        </div>

        {{-- FORM KONTEN SURAT (Digabung menjadi satu) --}}
        <div x-cloak x-show="jenis">
            
            {{-- BAGIAN KOP SURAT (Hanya untuk jenis 'keluar_full') --}}
            <div x-show="jenis === 'keluar_full'" x-transition class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Header Surat (Kop Surat)</p>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Logo Instansi</label>
                    <input type="file" name="logo_instansi_file" accept="image/*" class="w-full mt-1 p-2 border rounded shadow-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Instansi</label>
                        <div id="editor_nama_instansi" class="w-full border p-2 min-h-[60px]">{!! old('nama_instansi', $surat->nama_instansi) !!}</div>
                        <input type="hidden" name="nama_instansi" id="isi_nama_instansi_hidden" value="{{ old('nama_instansi', $surat->nama_instansi) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. Telepon / Kontak</label>
                        <div id="editor_kontak_instansi" class="w-full border p-2 min-h-[60px]">{!! old('kontak_instansi', $surat->kontak_instansi) !!}</div>
                        <input type="hidden" name="kontak_instansi" id="isi_kontak_instansi_hidden" value="{{ old('kontak_instansi', $surat->kontak_instansi) }}">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Alamat Instansi</label>
                    <div id="editor_alamat_instansi" class="w-full border p-2 min-h-[60px]">{!! old('alamat_instansi', $surat->alamat_instansi) !!}</div>
                    <input type="hidden" name="alamat_instansi" id="isi_alamat_instansi_hidden" value="{{ old('alamat_instansi', $surat->alamat_instansi) }}">
                </div>
            </div>

            {{-- DETAIL SURAT (Umum untuk semua jenis) --}}
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
                        <label for="judul" class="block text-sm font-medium text-gray-700">Hal (Perihal)</label>
                        <input type="text" name="judul" id="judul" class="w-full mt-1 p-2 border rounded shadow-sm" value="{{ old('judul', $surat->judul) }}">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="tujuan" class="block text-sm font-medium text-gray-700">Tujuan Surat</label>
                    <textarea name="tujuan" id="tujuan" rows="4" class="w-full mt-1 p-2 border rounded shadow-sm">{{ old('tujuan', $surat->tujuan) }}</textarea>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Isi Surat</label>
                    <div id="editor_isi" class="bg-white border rounded shadow-sm min-h-[200px] p-2">{!! old('isi', $surat->isi) !!}</div>
                    <input type="hidden" name="isi" id="isi_hidden" value="{{ old('isi', $surat->isi) }}">
                </div>
            </div>

            {{-- DETAIL PENANDATANGAN --}}
            <div class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Detail Penandatangan</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="penandatangan_jabatan" placeholder="Jabatan (e.g., Plh. KEPALA DINAS,)" class="w-full p-2 border rounded shadow-sm" value="{{ old('penandatangan_jabatan', $surat->penandatangan_jabatan) }}">
                    <input type="text" name="penandatangan_nama" placeholder="Nama Lengkap & Gelar" class="w-full p-2 border rounded shadow-sm" value="{{ old('penandatangan_nama', $surat->penandatangan_nama) }}">
                    <textarea name="penandatangan_nip" placeholder="Pangkat & NIP" rows="2" class="w-full md:col-span-2 p-2 border rounded shadow-sm">{{ old('penandatangan_nip', $surat->penandatangan_nip) }}</textarea>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end mt-6">
                <a href="{{ route('surat.preview', $surat->id) }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection
