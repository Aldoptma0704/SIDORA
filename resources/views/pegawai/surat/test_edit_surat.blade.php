@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow"
     x-data="{
        jenis: '{{ old('jenis', 'keluar') }}',
        editorKeluarInitialized: false,
        editorTemplateInitialized: false,

        initEditorKeluar() {
            if (this.jenis === 'keluar' && !this.editorKeluarInitialized) {
                window.initCkeditor('#editor_keluar', '#isi_keluar');
                window.initCkeditor('#editor_nama_instansi', '#isi_nama_instansi_hidden');
                window.initCkeditor('#editor_kontak_instansi', '#isi_kontak_instansi_hidden');
                window.initCkeditor('#editor_alamat_instansi', '#isi_alamat_instansi_hidden');
                this.editorKeluarInitialized = true;
            }
        },

        initEditorTemplate() {
            if (!this.editorTemplateInitialized) {
                window.initCkeditor('#editor_template', '#isi_template_hidden');
                this.editorTemplateInitialized = true;
            }
        }    
     }"
    x-init="if (jenis === 'keluar_full') { initEditorKeluar() } else if (jenis === 'keluar') { initEditorTemplate() }">

    <h2 class="text-xl font-semibold mb-6 text-gray-700">Buat Surat Baru</h2>

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
    
    <form action="{{ route('surat.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            <select name="jenis" x-model="jenis" @change="
                if(jenis === 'keluar_full') { initEditorKeluar() }
                else if(jenis === 'keluar') { initEditorTemplate() }
            " class="w-full mt-1 p-2 border rounded shadow-sm focus:ring focus:border-blue-300" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="keluar_full">Surat Keluar</option>
                <option value="keluar">Surat Keluar Template</option>
            </select>
        </div>

        <div x-cloak x-show="jenis === 'keluar_full'" x-transition>
            {{-- HEADER SURAT --}}
            <div class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Header Surat</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Logo Instansi</label>
                    <input type="file" name="logo_instansi_file" accept="image/*" class="w-full mt-1 p-2 border rounded shadow-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Instansi</label>
                        <div id="editor_nama_instansi" class="w-full border p-2 min-h-[60px]"></div>
                        <input type="hidden" name="nama_instansi" id="isi_nama_instansi_hidden" value="{{ old('nama_instansi') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. Telepon / Kontak</label>
                        <div id="editor_kontak_instansi" class="w-full border p-2 min-h-[60px]"></div>
                        <input type="hidden" name="kontak_instansi" id="isi_kontak_instansi_hidden" value="{{ old('kontak_instansi') }}">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Alamat Instansi</label>
                    <div id="editor_alamat_instansi" class="w-full border p-2 min-h-[60px]"></div>
                    <input type="hidden" name="alamat_instansi" id="isi_alamat_instansi_hidden" value="{{ old('alamat_instansi') }}">
                </div>
            </div>

            {{-- ISI SURAT --}}
            <div class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Isi Surat Keluar</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sifat</label>
                        <input type="text" name="sifat" value="{{ old('sifat', 'Biasa') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lampiran</label>
                        <input type="text" name="lampiran" value="{{ old('lampiran', '-') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hal (Perihal)</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Tujuan Surat</label>
                    <textarea name="tujuan" rows="3" class="w-full mt-1 p-2 border rounded shadow-sm">{{ old('tujuan') }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Isi Surat</label>
                    <div id="editor_keluar" class="w-full border p-2 min-h-[200px]"></div>
                    <input type="hidden" name="isi" id="isi_keluar_hidden" value="{{ old('isi') }}">
                </div>
            </div>

            {{-- PENANDATANGAN --}}
            <div class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Detail Penandatangan</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="penandatangan_jabatan" value="{{ old('penandatangan_jabatan') }}" placeholder="Jabatan (e.g., Plh. KEPALA DINAS,)" class="w-full mt-1 p-2 border rounded shadow-sm">
                    <input type="text" name="penandatangan_nama" value="{{ old('penandatangan_nama') }}" placeholder="Nama Lengkap & Gelar" class="w-full mt-1 p-2 border rounded shadow-sm">
                    <textarea name="penandatangan_nip" placeholder="Pangkat & NIP" rows="2" class="w-full md:col-span-2 mt-1 p-2 border rounded shadow-sm">{{ old('penandatangan_nip') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Form Surat Keluar --}}
        <div x-show="jenis === 'keluar'" x-transition x-init="initEditorKeluar()">
            <div class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Detail Surat Keluar</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nomor_surat" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat') }}" class="w-full mt-1 p-2 border rounded shadow-sm" :disabled="jenis !== 'keluar'">
                    </div>
                    <div>
                        <label for="sifat" class="block text-sm font-medium text-gray-700">Sifat</label>
                        <input type="text" name="sifat" id="sifat" value="{{ old('sifat', 'Biasa') }}" class="w-full mt-1 p-2 border rounded shadow-sm" :disabled="jenis !== 'keluar'">
                    </div>
                    <div>
                        <label for="lampiran" class="block text-sm font-medium text-gray-700">Lampiran</label>
                        <input type="text" name="lampiran" id="lampiran" value="{{ old('lampiran', '-') }}" class="w-full mt-1 p-2 border rounded shadow-sm" :disabled="jenis !== 'keluar'">
                    </div>
                    <div>
                        <label for="judul_keluar" class="block text-sm font-medium text-gray-700">Hal (Perihal)</label>
                        <input type="text" name="judul" id="judul_keluar" value="{{ old('judul') }}" class="w-full mt-1 p-2 border rounded shadow-sm" :disabled="jenis !== 'keluar'">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="tujuan" class="block text-sm font-medium text-gray-700">Tujuan Surat</label>
                    <textarea name="tujuan" id="tujuan" rows="4" class="w-full mt-1 p-2 border rounded shadow-sm" placeholder="Yth. Dekan Fakultas MIPA..." :disabled="jenis !== 'keluar'">{{ old('tujuan') }}</textarea>
                </div>
                <div class="mt-4">
                    <label for="editor_keluar" class="block text-sm font-medium text-gray-700">Isi Surat</label>
                    <div id="editor_keluar" class="w-full border p-2 min-h-[200px]"></div>
                    {{-- ID hidden input ini harus unik dan sesuai dengan yang dipanggil di x-data --}}
                    <input type="hidden" name="isi" id="isi_keluar_hidden" value="{{ old('isi') }}">
                </div>
                <hr class="my-6">   
                <p class="text-lg font-semibold text-gray-600 mb-4">Detail Penandatangan</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="penandatangan_jabatan" value="{{ old('penandatangan_jabatan') }}" placeholder="Jabatan (e.g., Plh. KEPALA DINAS,)" class="w-full mt-1 p-2 border rounded shadow-sm" :disabled="jenis !== 'keluar'">
                    <input type="text" name="penandatangan_nama" value="{{ old('penandatangan_nama') }}" placeholder="Nama Lengkap & Gelar" class="w-full mt-1 p-2 border rounded shadow-sm" :disabled="jenis !== 'keluar'">
                    <textarea name="penandatangan_nip" placeholder="Pangkat & NIP" rows="2" class="w-full md:col-span-2 mt-1 p-2 border rounded shadow-sm" :disabled="jenis !== 'keluar'">{{ old('penandatangan_nip') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <a href="{{ route('surat.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Surat</button>
        </div>
    </form>
</div>
@endsection
