{{-- File: resources/views/pegawai/surat/edit.blade.php --}}
@extends('layouts.app')

@push('styles')
    {{-- <link href="path/to/your/ckeditor.css" rel="stylesheet"> --}}
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header Section --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-full -translate-y-16 translate-x-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-2xl shadow-lg">
                        <i class="fas fa-edit text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                            Edit Surat
                        </h1>
                        <p class="text-gray-500 mt-1">Perbarui dan sempurnakan dokumen surat Anda</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 text-red-800 p-6 mb-8 rounded-xl shadow-lg animate-fade-in">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-bold text-lg mb-2">Terjadi Kesalahan</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Form --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden" 
             x-data="{
                jenis: '{{ old('jenis', $surat->jenis) }}',
                
                initAllEditors() {
                    this.$nextTick(() => {
                        window.initCkeditor('#editor_isi', '#isi_hidden');
                        
                        if (this.jenis === 'keluar_full') {
                            window.initCkeditor('#editor_nama_instansi', '#isi_nama_instansi_hidden');
                            window.initCkeditor('#editor_kontak_instansi', '#isi_kontak_instansi_hidden');
                            window.initCkeditor('#editor_alamat_instansi', '#isi_alamat_instansi_hidden');
                        }
                    });
                }
             }"
             x-init="initAllEditors()">

            <form action="{{ route('pegawai.surat.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Progress Indicator --}}
                <div class="bg-gradient-to-r from-orange-50 to-yellow-50 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-yellow-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                <i class="fas fa-edit text-xs"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Mode Edit</span>
                        </div>
                        <div class="flex-1 h-px bg-gray-300"></div>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-yellow-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                <i class="fas fa-save text-xs"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Simpan Perubahan</span>
                        </div>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-8">
                    {{-- Letter Type Selection --}}
                    <div class="mb-8">
                        <label class="block text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-clipboard-list text-orange-500 mr-2"></i>
                            Jenis Surat
                        </label>
                        <select name="jenis" id="jenis" x-model="jenis" @change="initAllEditors()" 
                                class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-lg">
                            <option value="keluar_full" :selected="jenis === 'keluar_full'">🏢 Surat Keluar (Dengan Kop Surat)</option>
                            <option value="keluar" :selected="jenis === 'keluar'">📄 Surat Keluar (Template Standar)</option>
                        </select>
                    </div>

                    {{-- Form Content --}}
                    <div x-cloak x-show="jenis">
                        
                        {{-- Header Section (Only for keluar_full) --}}
                        <div x-show="jenis === 'keluar_full'" x-transition class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8 mb-8 border border-blue-200">
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl mr-4">
                                    <i class="fas fa-building text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Header Surat (Kop Surat)</h3>
                                    <p class="text-gray-600">Perbarui informasi kop surat dan identitas instansi</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                {{-- Logo Upload --}}
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-image text-blue-500 mr-2"></i>
                                        Logo Instansi
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="logo_instansi_file" accept="image/*" 
                                               class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-50 file:to-purple-50 file:text-blue-700 hover:file:from-blue-100 hover:file:to-purple-100 file:transition-all file:duration-200 border-2 border-dashed border-gray-300 rounded-xl p-4 hover:border-blue-400 transition-colors">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Kosongkan jika tidak ingin mengubah logo yang ada
                                    </p>
                                </div>

                                {{-- Institution Details --}}
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                                            <i class="fas fa-university text-green-500 mr-2"></i>
                                            Nama Instansi
                                        </label>
                                        <div id="editor_nama_instansi" class="w-full border-2 border-gray-200 rounded-lg p-4 min-h-[80px] focus-within:border-blue-400 transition-colors">{!! old('nama_instansi', $surat->nama_instansi) !!}</div>
                                        <input type="hidden" name="nama_instansi" id="isi_nama_instansi_hidden" value="{{ old('nama_instansi', $surat->nama_instansi) }}">
                                    </div>
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                                            <i class="fas fa-phone text-orange-500 mr-2"></i>
                                            No. Telepon / Kontak
                                        </label>
                                        <div id="editor_kontak_instansi" class="w-full border-2 border-gray-200 rounded-lg p-4 min-h-[80px] focus-within:border-blue-400 transition-colors">{!! old('kontak_instansi', $surat->kontak_instansi) !!}</div>
                                        <input type="hidden" name="kontak_instansi" id="isi_kontak_instansi_hidden" value="{{ old('kontak_instansi', $surat->kontak_instansi) }}">
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                        Alamat Instansi
                                    </label>
                                    <div id="editor_alamat_instansi" class="w-full border-2 border-gray-200 rounded-lg p-4 min-h-[80px] focus-within:border-blue-400 transition-colors">{!! old('alamat_instansi', $surat->alamat_instansi) !!}</div>
                                    <input type="hidden" name="alamat_instansi" id="isi_alamat_instansi_hidden" value="{{ old('alamat_instansi', $surat->alamat_instansi) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Letter Details Section --}}
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-8 mb-8 border border-green-200">
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl mr-4">
                                    <i class="fas fa-file-alt text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Detail Surat Keluar</h3>
                                    <p class="text-gray-600">Perbarui informasi dasar dan identitas surat</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-hashtag text-blue-500 mr-2"></i>
                                        Nomor Surat
                                    </label>
                                    <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat', $surat->nomor_surat) }}"
                                           class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                                </div>
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-tag text-purple-500 mr-2"></i>
                                        Sifat
                                    </label>
                                    <input type="text" name="sifat" id="sifat" value="{{ old('sifat', $surat->sifat) }}"
                                           class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                                </div>
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-paperclip text-orange-500 mr-2"></i>
                                        Lampiran
                                    </label>
                                    <input type="text" name="lampiran" id="lampiran" value="{{ old('lampiran', $surat->lampiran) }}"
                                           class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                                </div>
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-bookmark text-indigo-500 mr-2"></i>
                                        Hal (Perihal)
                                    </label>
                                    <input type="text" name="judul" id="judul" value="{{ old('judul', $surat->judul) }}"
                                           class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                                </div>
                            </div>

                            <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-map-pin text-red-500 mr-2"></i>
                                    Tujuan Surat
                                </label>
                                <textarea name="tujuan" id="tujuan" rows="4" 
                                          class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all resize-none">{{ old('tujuan', $surat->tujuan) }}</textarea>
                            </div>

                            <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-edit text-green-500 mr-2"></i>
                                    Isi Surat
                                </label>
                                <div id="editor_isi" class="w-full border-2 border-gray-200 rounded-lg p-4 min-h-[250px] focus-within:border-green-400 transition-colors">{!! old('isi', $surat->isi) !!}</div>
                                <input type="hidden" name="isi" id="isi_hidden" value="{{ old('isi', $surat->isi) }}">
                            </div>
                        </div>

                        {{-- Signatory Section --}}
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-8 mb-8 border border-purple-200">
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl mr-4">
                                    <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Detail Penandatangan</h3>
                                    <p class="text-gray-600">Perbarui informasi pejabat yang menandatangani surat</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-briefcase text-blue-500 mr-2"></i>
                                        Jabatan
                                    </label>
                                    <input type="text" name="penandatangan_jabatan" value="{{ old('penandatangan_jabatan', $surat->penandatangan_jabatan) }}" 
                                           placeholder="e.g., Plh. KEPALA DINAS," 
                                           class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                                </div>
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-user text-green-500 mr-2"></i>
                                        Nama Lengkap
                                    </label>
                                    <input type="text" name="penandatangan_nama" value="{{ old('penandatangan_nama', $surat->penandatangan_nama) }}" 
                                           placeholder="Nama Lengkap & Gelar" 
                                           class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all">
                                </div>
                            </div>
                            <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-id-card text-orange-500 mr-2"></i>
                                    Pangkat & NIP
                                </label>
                                <textarea name="penandatangan_nip" rows="3" placeholder="Pangkat & NIP" 
                                          class="w-full p-4 border-2 border-gray-200 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all resize-none">{{ old('penandatangan_nip', $surat->penandatangan_nip) }}</textarea>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('pegawai.surat.preview', $surat->id) }}" 
                               class="px-8 py-4 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 font-semibold rounded-xl hover:from-gray-200 hover:to-gray-300 transition-all transform hover:scale-105 shadow-md text-center">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-8 py-4 bg-gradient-to-r from-orange-600 to-yellow-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-yellow-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}

/* Focus states for form elements */
.focus-within\:border-blue-400:focus-within,
.focus-within\:border-green-400:focus-within {
    border-color: #60a5fa !important;
}

/* Custom editor styling */
.ck-editor__editable {
    border-radius: 8px !important;
    min-height: 150px !important;
}
</style>
@endsection