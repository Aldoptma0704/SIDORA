@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header Section --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full -translate-y-16 translate-x-16 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg">
                        <i class="fas fa-file-signature text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                            Buat Surat Balasan
                        </h1>
                        <p class="text-gray-500 mt-1">Formulir resmi pembuatan surat dinas & balasan pimpinan</p>
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
                        <p class="font-bold text-lg mb-2">Terjadi Kesalahan Validasi</p>
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
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden" x-data="suratForm()">
            <form action="{{ route('pimpinan.surat.balas.store') }}" method="POST" enctype="multipart/form-data" @submit="validateForm($event)">
                @csrf

                {{-- Step Indicator --}}
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">1</div>
                            <span class="text-sm font-semibold text-gray-700">Pilih Jenis Surat</span>
                        </div>
                        <div class="flex-1 h-px bg-gray-300"></div>
                        <div class="flex items-center space-x-2" :class="jenis ? 'opacity-100' : 'opacity-50'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold transition-colors" :class="jenis ? 'bg-gradient-to-r from-blue-500 to-purple-600' : 'bg-gray-300'">2</div>
                            <span class="text-sm font-semibold text-gray-700">Isi Detail Surat & Tanda Tangan</span>
                        </div>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-8">
                    {{-- Jenis Surat Selection --}}
                    <div class="mb-8">
                        <label class="block text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                            Jenis Surat
                        </label>
                        <select name="jenis" x-model="jenis" class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-lg" required>
                            <option value="" disabled>-- Pilih Jenis Surat --</option>
                            <option value="keluar_full">🏢 Surat Keluar (Dengan Kop Surat Instansi Lengkap)</option>
                            <option value="keluar">📄 Surat Keluar (Template Standar Pemerintah Provinsi Lampung)</option>
                        </select>
                    </div>

                    {{-- Form Content (Only visible when jenis is selected) --}}
                    <div x-show="jenis" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">

                        {{-- Header Section (Only for keluar_full) --}}
                        <div x-show="jenis === 'keluar_full'" x-transition class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8 mb-8 border border-blue-200">
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl mr-4">
                                    <i class="fas fa-building text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Header Surat</h3>
                                    <p class="text-gray-600">Informasi kop surat dan identitas instansi</p>
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
                                </div>

                                {{-- Institution Details --}}
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-university text-green-500 mr-2"></i>
                                        Nama Instansi
                                    </label>
                                    <p class="text-xs text-gray-500 mb-3">Tuliskan nama instansi (dapat terdiri dari instansi induk dan dinas/unit, contoh: PEMERINTAH PROVINSI LAMPUNG & DINAS TENAGA KERJA)</p>
                                    <div id="editor_nama_instansi" class="w-full border-2 border-gray-200 rounded-lg p-4 min-h-[80px] focus-within:border-blue-400 transition-colors">{!! old('nama_instansi') !!}</div>
                                    <input type="hidden" name="nama_instansi" value="{{ old('nama_instansi') }}">
                                </div>

                                {{-- Alamat & Nomor Telepon (Ditampilkan dalam 1 Baris pada Kop Surat) --}}
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                            Alamat Instansi
                                        </label>
                                        <input type="text" name="alamat_instansi" value="{{ old('alamat_instansi') }}" 
                                               placeholder="Contoh: Jl. Gatot Subroto No.28 Kotak Pos 78"
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-400 focus:outline-none transition-colors text-sm">
                                    </div>
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-phone text-orange-500 mr-2"></i>
                                            No. Telepon / Kontak Instansi
                                        </label>
                                        <input type="text" name="kontak_instansi" value="{{ old('kontak_instansi') }}" 
                                               placeholder="Contoh: (0721) 252065, Fax. 262856"
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-400 focus:outline-none transition-colors text-sm">
                                    </div>
                                </div>

                                {{-- Laman (Website) & Pos-el (Email) (Ditampilkan di Baris Bawah Alamat & Kontak) --}}
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-globe text-indigo-500 mr-2"></i>
                                            Laman Instansi (Website)
                                        </label>
                                        <input type="text" name="website_instansi" value="{{ old('website_instansi') }}" 
                                               placeholder="Contoh: https://disnaker.lampungprov.go.id"
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-400 focus:outline-none transition-colors text-sm">
                                    </div>
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                            Pos-el Instansi (Email)
                                        </label>
                                        <input type="text" name="email_instansi" value="{{ old('email_instansi') }}" 
                                               placeholder="Contoh: lampungnaker@gmail.com"
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-400 focus:outline-none transition-colors text-sm">
                                    </div>
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
                                    <h3 class="text-xl font-bold text-gray-800">Detail Surat Balasan</h3>
                                    <p class="text-gray-600">Informasi dasar dan identitas surat</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 shadow-sm border border-blue-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center justify-between">
                                        <span class="flex items-center">
                                            <i class="fas fa-hashtag text-blue-600 mr-2"></i>
                                            Nomor Surat
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            <i class="fas fa-magic mr-1"></i> Otomatis oleh Admin
                                        </span>
                                    </label>
                                    <input type="text" name="nomor_surat" 
                                           class="w-full p-4 border-2 border-blue-200 bg-white/70 text-gray-600 rounded-xl shadow-inner text-sm font-mono cursor-not-allowed" 
                                           value="{{ old('nomor_surat') }}"
                                           placeholder="Otomatis di-generate Admin (Pergub Lampung No. 14 & 16 Th 2023)" 
                                           readonly>
                                    <p class="text-[11px] text-blue-700 mt-2 flex items-center">
                                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                        Nomor surat akan dibuat otomatis oleh Admin sesuai kode klasifikasi kearsipan resmi.
                                    </p>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-tag text-purple-500 mr-2"></i>
                                        Sifat Surat
                                    </label>
                                    <select name="sifat" class="w-full p-4 border-2 border-gray-200 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all">
                                        <option value="Biasa" {{ old('sifat') == 'Biasa' ? 'selected' : '' }}>📄 Biasa</option>
                                        <option value="Penting" {{ old('sifat') == 'Penting' ? 'selected' : '' }}>⚠️ Penting</option>
                                        <option value="Segera" {{ old('sifat') == 'Segera' ? 'selected' : '' }}>🚨 Segera</option>
                                        <option value="Rahasia" {{ old('sifat') == 'Rahasia' ? 'selected' : '' }}>🔒 Rahasia</option>
                                    </select>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-paperclip text-orange-500 mr-2"></i>
                                        Lampiran
                                    </label>
                                    <input type="text" name="lampiran" value="{{ old('lampiran', '-') }}" 
                                           class="w-full p-4 border-2 border-gray-200 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" 
                                           placeholder="Contoh: 1 (satu) berkas" required>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-heading text-red-500 mr-2"></i>
                                        Hal / Perihal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="judul" value="{{ old('judul') }}" 
                                           class="w-full p-4 border-2 border-gray-200 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" 
                                           placeholder="Contoh: Jawaban Permohonan Kerjasama Pelatihan Kerja" required>
                                </div>
                            </div>

                            <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-user-tag text-teal-500 mr-2"></i>
                                    Tujuan Surat <span class="text-red-500">*</span>
                                </label>
                                <textarea name="tujuan" rows="3" 
                                          class="w-full p-4 border-2 border-gray-200 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all resize-none" 
                                          placeholder="Contoh: Pimpinan PT. Tenaga Kerja Sejahtera&#10;di - tempat" required>{{ old('tujuan') }}</textarea>
                                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-teal-600"></i> Format tujuan pada cetakan dokumen resmi otomatis diikuti baris <strong>di - tempat</strong>.
                                </p>
                            </div>

                            <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-align-left text-blue-500 mr-2"></i>
                                    Isi Surat <span class="text-red-500">*</span>
                                </label>
                                <div id="editor_isi" class="min-h-[250px] bg-white">{!! old('isi') !!}</div>
                                <input type="hidden" name="isi" value="{{ old('isi') }}">

                                {{-- Tips Format & Tab Word --}}
                                <div class="mt-3 p-3.5 bg-blue-50/80 border border-blue-200 rounded-xl text-xs text-gray-700 space-y-2">
                                    <div class="font-bold text-blue-900 flex items-center gap-2">
                                        <i class="fas fa-keyboard text-blue-600 text-sm"></i>
                                        <span>Panduan Mengetik & Format Dokumen (Seperti di Word):</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] text-gray-600">
                                        <div class="flex items-start gap-1.5 bg-white/70 p-2 rounded-lg border border-blue-100">
                                            <i class="fas fa-align-left text-blue-500 mt-0.5"></i>
                                            <div>
                                                <strong class="text-gray-800">Perataan Teks:</strong> Klik ikon rata kiri, tengah, kanan, atau rata kiri-kanan (justify) langsung di toolbar atas.
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-1.5 bg-white/70 p-2 rounded-lg border border-blue-100">
                                            <i class="fas fa-indent text-blue-500 mt-0.5"></i>
                                            <div>
                                                <strong class="text-gray-800">Tab & Indentasi:</strong> Tekan <kbd class="px-1 py-0.5 bg-white border border-gray-300 rounded font-mono font-semibold">Tab</kbd> untuk menjorokkan paragraf/poin, atau <kbd class="px-1 py-0.5 bg-white border border-gray-300 rounded font-mono font-semibold">Shift+Tab</kbd> untuk memundurkan.
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-1.5 bg-white/70 p-2 rounded-lg border border-blue-100 md:col-span-2">
                                            <i class="fas fa-list-ol text-blue-500 mt-0.5"></i>
                                            <div>
                                                <strong class="text-gray-800">Cara Keluar dari Poin (Paragraf Bebas):</strong> Pada baris kosong setelah poin terakhir, tekan <kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded font-mono font-bold text-gray-800">Enter 1x lagi</kbd> (total Enter 2x), ATAU klik kembali tombol <strong>Poin/Angka</strong> di toolbar untuk menonaktifkannya. Paragraf baru akan berdiri sendiri dan memiliki jarak pemisah yang rapi.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4: Multi-Level Tanda Tangan Section (4 OPSI LENGKAP) --}}
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-8 mb-8 border border-purple-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="p-3 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl mr-4">
                                        <i class="fas fa-users-cog text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800">Pengaturan Tanda Tangan &amp; Verifikasi Surat</h3>
                                        <p class="text-gray-600 text-sm">Pilih pejabat penandatangan yang berwenang untuk surat ini</p>
                                    </div>
                                </div>
                                <span class="text-xs px-3 py-1 bg-purple-100 text-purple-800 rounded-full font-bold" x-text="activeCount() + ' Opsi Aktif'"></span>
                            </div>

                            {{-- Visualisasi Rantai Alur Persetujuan --}}
                            <div class="mb-6 p-4 bg-white/80 backdrop-blur rounded-xl border border-purple-200">
                                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider block mb-2">
                                    <i class="fas fa-route text-purple-600 mr-1.5"></i> Estimasi Rantai Alur Surat:
                                </span>
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="px-2.5 py-1 bg-purple-600 text-white rounded-lg font-bold">Pembuat (Pimpinan: {{ auth()->user()->name }})</span>
                                    <template x-if="optKabid.enabled">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-arrow-right text-gray-400"></i>
                                            <span class="px-2.5 py-1 bg-blue-600 text-white rounded-lg font-bold" x-text="optKabid.selectedUnit ? 'Kabid/Staf/UPTD Lain' : 'Pilih Unit...'"></span>
                                        </div>
                                    </template>
                                    <template x-if="optSekretariat.enabled">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-arrow-right text-gray-400"></i>
                                            <span class="px-2.5 py-1 bg-indigo-600 text-white rounded-lg font-bold">Sekretaris Dinas</span>
                                        </div>
                                    </template>
                                    <template x-if="optKadis.enabled">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-arrow-right text-gray-400"></i>
                                            <span class="px-2.5 py-1 bg-rose-600 text-white rounded-lg font-bold">Kepala Dinas (Kadis)</span>
                                        </div>
                                    </template>
                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                    <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg font-bold">Admin (Penomoran &amp; Arsip)</span>
                                </div>
                            </div>

                            <div class="space-y-5">
                                {{-- 1. OPSI TANDA TANGAN SENDIRI (PIMPINAN PEMBUAT) --}}
                                <div class="bg-white rounded-xl p-5 sm:p-6 shadow-sm border-2 transition-all"
                                     :class="optSendiri.enabled ? 'border-purple-300 ring-2 ring-purple-100' : 'border-gray-200 opacity-75'">
                                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                                        <label class="flex items-center gap-3 cursor-pointer select-none">
                                            <input type="checkbox" x-model="optSendiri.enabled" class="w-5 h-5 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                                            <div>
                                                <span class="font-bold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 text-xs flex items-center justify-center font-bold">1</span>
                                                    Tanda Tangan Sendiri ({{ auth()->user()->name }})
                                                </span>
                                                <span class="text-xs text-gray-500 block">Identitas otomatis terisi sesuai akun pimpinan Anda saat ini</span>
                                            </div>
                                        </label>
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                                              :class="optSendiri.enabled ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600'">
                                            <span x-text="optSendiri.enabled ? 'Aktif' : 'Nonaktif'"></span>
                                        </span>
                                    </div>

                                    <div x-show="optSendiri.enabled" x-transition class="pt-4 space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                                    <i class="fas fa-user text-purple-600 mr-1"></i> Nama Lengkap
                                                </label>
                                                <input type="text" x-model="optSendiri.nama" class="w-full p-2.5 text-xs bg-gray-50 border border-gray-300 rounded-lg text-gray-800 font-semibold" readonly>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                                    <i class="fas fa-briefcase text-blue-600 mr-1"></i> Jabatan
                                                </label>
                                                <input type="text" x-model="optSendiri.jabatan" class="w-full p-2.5 text-xs bg-white border border-gray-300 rounded-lg text-gray-800 font-medium focus:ring-2 focus:ring-purple-200">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                                    <i class="fas fa-id-card text-emerald-600 mr-1"></i> NIP
                                                </label>
                                                <input type="text" x-model="optSendiri.nip" class="w-full p-2.5 text-xs bg-white border border-gray-300 rounded-lg text-gray-800 font-medium focus:ring-2 focus:ring-purple-200">
                                            </div>
                                        </div>

                                        {{-- Tanda Tangan Profil / Upload Baru --}}
                                        <div class="p-4 bg-purple-50/70 border border-purple-200 rounded-xl">
                                            @if (auth()->user()->signature)
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white rounded-lg border border-purple-200 shadow-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="bg-gray-50 p-1.5 border border-purple-100 rounded-lg shrink-0">
                                                            <img src="{{ asset('storage/signatures/' . auth()->user()->signature) }}" alt="Tanda Tangan Profil" class="h-12 max-w-[140px] object-contain">
                                                        </div>
                                                        <div>
                                                            <span class="inline-flex items-center text-[11px] font-semibold text-green-700 bg-green-100 px-2.5 py-0.5 rounded-full mb-1">
                                                                <i class="fas fa-check-circle mr-1"></i> Tanda Tangan Profil Terpasang
                                                            </span>
                                                            <p class="text-xs text-gray-600">Tanda tangan dari profil akun pimpinan Anda otomatis digunakan.</p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="inline-flex items-center text-xs text-purple-700 font-medium cursor-pointer hover:text-purple-900 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-200">
                                                            <input type="checkbox" x-model="optSendiri.custom_upload" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 mr-2">
                                                            Upload tanda tangan khusus surat ini
                                                        </label>
                                                    </div>
                                                </div>

                                                <div x-show="optSendiri.custom_upload" x-transition class="pt-3 mt-3 border-t border-purple-200">
                                                    <label class="block text-xs font-semibold text-purple-900 mb-1.5">
                                                        <i class="fas fa-file-upload text-purple-600 mr-1"></i> Upload File Tanda Tangan Khusus
                                                    </label>
                                                    <input type="file" name="file_ttd_pembuat" accept="image/png,image/jpeg,image/jpg" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                                                </div>
                                            @else
                                                <label class="block text-xs font-semibold text-purple-900 mb-2">
                                                    <i class="fas fa-signature text-purple-600 mr-1"></i> Upload Scan/Gambar Tanda Tangan Anda (Opsional)
                                                </label>
                                                <input type="file" name="file_ttd_pembuat" accept="image/png,image/jpeg,image/jpg" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                                                <p class="text-[11px] text-purple-700 mt-1.5">
                                                    Format: PNG atau JPG (maksimal 2MB). Jika tidak di-upload, akan disediakan ruang kosong untuk tanda tangan basah.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. OPSI TANDA TANGAN KEPALA BIDANG / KEPALA STAF / KEPALA UPTD LAIN --}}
                                <div class="bg-white rounded-xl p-5 sm:p-6 shadow-sm border-2 transition-all"
                                     :class="optKabid.enabled ? 'border-blue-300 ring-2 ring-blue-100' : 'border-gray-200 opacity-75'">
                                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                                        <label class="flex items-center gap-3 cursor-pointer select-none">
                                            <input type="checkbox" x-model="optKabid.enabled" @change="onKabidToggle()" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div>
                                                <span class="font-bold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs flex items-center justify-center font-bold">2</span>
                                                    Tanda Tangan Kepala Bidang / Kepala Staf / Kepala UPTD Lain
                                                </span>
                                                <span class="text-xs text-gray-500 block">Pilih unit kerja untuk memuat data pejabat dan mengarahkan verifikasi surat</span>
                                            </div>
                                        </label>
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                                              :class="optKabid.enabled ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'">
                                            <span x-text="optKabid.enabled ? 'Aktif' : 'Nonaktif'"></span>
                                        </span>
                                    </div>

                                    <div x-show="optKabid.enabled" x-transition class="pt-4 space-y-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                                <i class="fas fa-sitemap text-blue-600 mr-1"></i> Pilih Kepala Bidang / Kepala Staf / Kepala UPTD Lain <span class="text-red-500">*</span>
                                            </label>
                                            <select x-model="optKabid.selectedUnit" @change="onKabidUnitChange()" class="w-full p-3 text-sm border-2 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 bg-white">
                                                <option value="">-- Pilih Unit Pejabat Penandatangan --</option>
                                                <template x-for="grp in kabidUnits" :key="grp.group">
                                                    <optgroup :label="grp.group">
                                                        <template x-for="item in grp.items" :key="item.value">
                                                            <option :value="item.value" x-text="item.label"></option>
                                                        </template>
                                                    </optgroup>
                                                </template>
                                            </select>
                                        </div>

                                        {{-- Data Pejabat Ditemukan --}}
                                        <div x-show="optKabid.found === true" x-transition class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 mb-3">
                                                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                                                <span>Akun Pimpinan Terdaftar &amp; Terhubung:</span>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Nama Pejabat</span>
                                                    <span class="text-xs font-bold text-gray-800" x-text="optKabid.nama"></span>
                                                </div>
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Jabatan</span>
                                                    <span class="text-xs font-medium text-gray-700" x-text="optKabid.jabatan"></span>
                                                </div>
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">NIP</span>
                                                    <span class="text-xs text-gray-600" x-text="optKabid.nip || '-'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Alert Belum Ada Akun --}}
                                        <div x-show="optKabid.found === false" x-transition class="p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-amber-900 flex items-start gap-3">
                                            <i class="fas fa-exclamation-triangle text-amber-600 text-lg mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-bold text-xs sm:text-sm">Belum Ada Akun Pimpinan Terdaftar</p>
                                                <p class="text-xs text-amber-800 mt-0.5">
                                                    Belum ada akun pimpinan yang terdaftar untuk unit ini di sistem SIDORA. Silakan hubungi Administrator Subbag Umum dan Kepegawaian untuk mendaftarkan akun pejabat terkait.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. OPSI TANDA TANGAN KEPALA SEKRETARIAT --}}
                                <div class="bg-white rounded-xl p-5 sm:p-6 shadow-sm border-2 transition-all"
                                     :class="optSekretariat.enabled ? 'border-indigo-300 ring-2 ring-indigo-100' : 'border-gray-200 opacity-75'">
                                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                                        <label class="flex items-center gap-3 cursor-pointer select-none">
                                            <input type="checkbox" x-model="optSekretariat.enabled" @change="onSekretariatToggle()" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                            <div>
                                                <span class="font-bold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs flex items-center justify-center font-bold">3</span>
                                                    Tanda Tangan Kepala Sekretariat (Sekretaris Dinas)
                                                </span>
                                                <span class="text-xs text-gray-500 block">Verifikasi dan tanda tangan Sekretaris Dinas Tenaga Kerja</span>
                                            </div>
                                        </label>
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                                              :class="optSekretariat.enabled ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600'">
                                            <span x-text="optSekretariat.enabled ? 'Aktif' : 'Nonaktif'"></span>
                                        </span>
                                    </div>

                                    <div x-show="optSekretariat.enabled" x-transition class="pt-4 space-y-4">
                                        {{-- Data Ditemukan --}}
                                        <div x-show="optSekretariat.found === true" x-transition class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 mb-3">
                                                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                                                <span>Akun Kepala Sekretariat Ditemukan &amp; Terhubung:</span>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Nama Pejabat</span>
                                                    <span class="text-xs font-bold text-gray-800" x-text="optSekretariat.nama"></span>
                                                </div>
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Jabatan</span>
                                                    <span class="text-xs font-medium text-gray-700" x-text="optSekretariat.jabatan"></span>
                                                </div>
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">NIP</span>
                                                    <span class="text-xs text-gray-600" x-text="optSekretariat.nip || '-'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Alert Belum Ada Akun --}}
                                        <div x-show="optSekretariat.found === false" x-transition class="p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-amber-900 flex items-start gap-3">
                                            <i class="fas fa-exclamation-triangle text-amber-600 text-lg mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-bold text-xs sm:text-sm">Belum Ada Akun Kepala Sekretariat Terdaftar</p>
                                                <p class="text-xs text-amber-800 mt-0.5">
                                                    Belum ada akun Kepala Sekretariat / Sekretaris Dinas yang terdaftar di sistem SIDORA. Silakan hubungi Administrator untuk mendaftarkan akun terkait.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. OPSI TANDA TANGAN KEPALA DINAS (KADIS) --}}
                                <div class="bg-white rounded-xl p-5 sm:p-6 shadow-sm border-2 transition-all"
                                     :class="optKadis.enabled ? 'border-rose-300 ring-2 ring-rose-100' : 'border-gray-200 opacity-75'">
                                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100">
                                        <label class="flex items-center gap-3 cursor-pointer select-none">
                                            <input type="checkbox" x-model="optKadis.enabled" @change="onKadisToggle()" class="w-5 h-5 text-rose-600 rounded border-gray-300 focus:ring-rose-500">
                                            <div>
                                                <span class="font-bold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs flex items-center justify-center font-bold">4</span>
                                                    Tanda Tangan Kepala Dinas (Kadis)
                                                </span>
                                                <span class="text-xs text-gray-500 block">Pengesahan tingkat tertinggi oleh Kepala Dinas Tenaga Kerja Provinsi Lampung</span>
                                            </div>
                                        </label>
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                                              :class="optKadis.enabled ? 'bg-rose-100 text-rose-800' : 'bg-gray-100 text-gray-600'">
                                            <span x-text="optKadis.enabled ? 'Aktif' : 'Nonaktif'"></span>
                                        </span>
                                    </div>

                                    <div x-show="optKadis.enabled" x-transition class="pt-4 space-y-4">
                                        {{-- Data Ditemukan --}}
                                        <div x-show="optKadis.found === true" x-transition class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 mb-3">
                                                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                                                <span>Akun Kepala Dinas Ditemukan &amp; Terhubung:</span>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Nama Kepala Dinas</span>
                                                    <span class="text-xs font-bold text-gray-800" x-text="optKadis.nama"></span>
                                                </div>
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Jabatan</span>
                                                    <span class="text-xs font-medium text-gray-700" x-text="optKadis.jabatan"></span>
                                                </div>
                                                <div class="bg-white p-2.5 rounded-lg border border-emerald-100 shadow-sm">
                                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">NIP</span>
                                                    <span class="text-xs text-gray-600" x-text="optKadis.nip || '-'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Alert Belum Ada Akun --}}
                                        <div x-show="optKadis.found === false" x-transition class="p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-amber-900 flex items-start gap-3">
                                            <i class="fas fa-exclamation-triangle text-amber-600 text-lg mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-bold text-xs sm:text-sm">Belum Ada Akun Kepala Dinas Terdaftar</p>
                                                <p class="text-xs text-amber-800 mt-0.5">
                                                    Belum ada akun Kepala Dinas yang terdaftar di sistem SIDORA. Silakan hubungi Administrator untuk mendaftarkan akun Kepala Dinas.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Render Hidden Inputs Dinamis untuk Form Submission --}}
                            <template x-for="(ttd, idx) in activePenandatangans()" :key="idx">
                                <div>
                                    <input type="hidden" :name="'penandatangan[' + idx + '][user_id]'" :value="ttd.user_id || ''">
                                    <input type="hidden" :name="'penandatangan[' + idx + '][peran]'" :value="ttd.peran === 'pembuat' ? 'pembuat' : 'pimpinan'">
                                    <input type="hidden" :name="'penandatangan[' + idx + '][urutan]'" :value="ttd.urutan">
                                    <input type="hidden" :name="'penandatangan[' + idx + '][nama]'" :value="ttd.nama">
                                    <input type="hidden" :name="'penandatangan[' + idx + '][nip]'" :value="ttd.nip">
                                    <input type="hidden" :name="'penandatangan[' + idx + '][jabatan]'" :value="ttd.jabatan">
                                </div>
                            </template>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('pimpinan.status-surat') }}" 
                               onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }"
                               class="px-8 py-4 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 font-semibold rounded-xl hover:from-gray-200 hover:to-gray-300 transition-all transform hover:scale-105 shadow-md text-center cursor-pointer">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Draft Surat
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

.ck-editor__editable {
    border-radius: 8px !important;
}

.focus-within\:border-blue-400:focus-within {
    border-color: #60a5fa !important;
}
</style>

<script>
    function suratForm() {
        return {
            jenis: '{{ old('jenis', '') }}',
            editorsInitialized: {
                isi: false,
                header: false
            },
            currentUserId: {{ auth()->id() }},
            pimpinanUsers: {!! json_encode($pimpinanUsers) !!},

            kabidUnits: [
                {
                    group: 'Kepala Bidang (Kabid)',
                    items: [
                        { value: 'Bidang Penempatan dan Perluasan Kesempatan Kerja', label: 'Kepala Bidang Penempatan dan Perluasan Kesempatan Kerja' },
                        { value: 'Bidang Pelatihan dan Produktivitas Tenaga Kerja', label: 'Kepala Bidang Pelatihan dan Produktivitas Tenaga Kerja' },
                        { value: 'Bidang Pengawasan Ketenagakerjaan', label: 'Kepala Bidang Pengawasan Ketenagakerjaan' },
                        { value: 'Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja', label: 'Kepala Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja' }
                    ]
                },
                {
                    group: 'Kepala Subbag & Kepala Staf Sekretariat',
                    items: [
                        { value: 'Sub Bagian Umum dan Kepegawaian', label: 'Kasubbag Umum dan Kepegawaian' },
                        { value: 'Staf Perencana Sekretariat', label: 'Kepala Staf Perencana' },
                        { value: 'Keuangan dan Aset Sekretariat', label: 'Kepala Staf Keuangan' },
                        { value: 'Kepegawaian Sekretariat', label: 'Kepala Staf Kepegawaian' }
                    ]
                },
                {
                    group: 'Kepala Unit Pelaksana Teknis Daerah (UPTD)',
                    items: [
                        { value: 'UPTD BLK Bandar Lampung', label: 'Kepala UPTD BLK Bandar Lampung' },
                        { value: 'UPTD BLK Metro', label: 'Kepala UPTD BLK Metro' },
                        { value: 'UPTD BLK Kalianda', label: 'Kepala UPTD BLK Kalianda' },
                        { value: 'UPTD BLK Way Abung', label: 'Kepala UPTD BLK Way Abung' },
                        { value: 'UPTD Balai Pengembangan Produktivitas Daerah', label: 'Kepala UPTD Balai Pengembangan Produktivitas Daerah' },
                        { value: 'UPTD Balai Keselamatan dan Kesehatan Kerja', label: 'Kepala UPTD Balai Keselamatan dan Kesehatan Kerja (K3)' }
                    ]
                }
            ],

            // 1. Tanda Tangan Sendiri (Pimpinan)
            optSendiri: {
                enabled: true,
                nama: '{{ auth()->user()->name }}',
                nip: '{{ auth()->user()->nip ?? "" }}',
                jabatan: '{{ auth()->user()->position ?: (auth()->user()->bagian ?: "Pimpinan Dinas Tenaga Kerja") }}',
                custom_upload: false
            },

            // 2. Tanda Tangan Kabid / Staf / UPTD Lain
            optKabid: {
                enabled: false,
                selectedUnit: '',
                user_id: null,
                nama: '',
                nip: '',
                jabatan: '',
                found: null
            },

            // 3. Tanda Tangan Kepala Sekretariat
            optSekretariat: {
                enabled: false,
                user_id: null,
                nama: '',
                nip: '',
                jabatan: '',
                found: null
            },

            // 4. Tanda Tangan Kadis
            optKadis: {
                enabled: false,
                user_id: null,
                nama: '',
                nip: '',
                jabatan: '',
                found: null
            },

            onKabidToggle() {
                if (!this.optKabid.enabled) {
                    this.optKabid.selectedUnit = '';
                    this.optKabid.user_id = null;
                    this.optKabid.nama = '';
                    this.optKabid.nip = '';
                    this.optKabid.jabatan = '';
                    this.optKabid.found = null;
                }
            },

            onKabidUnitChange() {
                if (!this.optKabid.selectedUnit) {
                    this.optKabid.user_id = null;
                    this.optKabid.nama = '';
                    this.optKabid.nip = '';
                    this.optKabid.jabatan = '';
                    this.optKabid.found = null;
                    return;
                }

                let selectedItem = null;
                for (const grp of this.kabidUnits) {
                    const found = grp.items.find(i => i.value === this.optKabid.selectedUnit);
                    if (found) { selectedItem = found; break; }
                }

                const targetVal = (this.optKabid.selectedUnit || '').toLowerCase().trim();
                const targetLabel = selectedItem ? selectedItem.label.toLowerCase().trim() : targetVal;

                // Cari pimpinan lain: KEPALA SEKRETARIAT & KEPALA DINAS TIDAK BOLEH MUNCUL DI OPSI INI
                const user = this.pimpinanUsers.find(u => {
                    // Jangan pilih diri sendiri di opsi 2
                    if (u.id === this.currentUserId) return false;

                    const uBagian = (u.bagian || '').toLowerCase().trim();
                    const uPos = (u.position || '').toLowerCase().trim();

                    const isKadis = uBagian.includes('kepala dinas') || uBagian.includes('kadis') || uPos.includes('kepala dinas') || uPos.includes('kadis');
                    const isSekretariat = uBagian === 'sekretariat' || uBagian === 'kepala sekretariat' || uPos.includes('sekretaris') || uPos === 'kepala sekretariat';
                    if (isKadis || isSekretariat) {
                        return false;
                    }

                    if (uBagian === targetVal || uPos === targetLabel || uPos === targetVal) {
                        return true;
                    }

                    if (targetVal.includes('perencana')) {
                        return uPos.includes('perencana') || uBagian.includes('perencana');
                    }
                    if (targetVal.includes('keuangan')) {
                        return uPos.includes('keuangan') || uBagian.includes('keuangan');
                    }
                    if (targetVal.includes('kepegawaian')) {
                        if (targetVal.includes('sub bagian umum') || targetVal.includes('kasubbag')) {
                            return uPos.includes('kasubbag') || uBagian.includes('umum dan kepegawaian');
                        }
                        return (uPos.includes('staf kepegawaian') || uBagian.includes('kepegawaian sekretariat')) && !uPos.includes('kasubbag');
                    }
                    if (targetVal.includes('penempatan')) {
                        return uPos.includes('penempatan') || uBagian.includes('penempatan');
                    }
                    if (targetVal.includes('pelatihan')) {
                        return uPos.includes('pelatihan') || uBagian.includes('pelatihan');
                    }
                    if (targetVal.includes('pengawasan')) {
                        return uPos.includes('pengawasan') || uBagian.includes('pengawasan');
                    }
                    if (targetVal.includes('hubungan industrial')) {
                        return uPos.includes('hubungan industrial') || uBagian.includes('hubungan industrial');
                    }
                    if (targetVal.includes('blk bandar lampung')) {
                        return uPos.includes('blk bandar lampung') || uBagian.includes('blk bandar lampung');
                    }
                    if (targetVal.includes('blk metro')) {
                        return uPos.includes('blk metro') || uBagian.includes('blk metro');
                    }
                    if (targetVal.includes('blk kalianda')) {
                        return uPos.includes('blk kalianda') || uBagian.includes('blk kalianda');
                    }
                    if (targetVal.includes('blk way abung')) {
                        return uPos.includes('blk way abung') || uBagian.includes('blk way abung');
                    }
                    if (targetVal.includes('produktivitas daerah')) {
                        return uPos.includes('produktivitas daerah') || uBagian.includes('produktivitas daerah') || uPos.includes('bppd');
                    }
                    if (targetVal.includes('keselamatan dan kesehatan') || targetVal.includes('k3')) {
                        return uPos.includes('keselamatan dan kesehatan') || uPos.includes('k3') || uBagian.includes('k3');
                    }

                    return false;
                });

                if (user) {
                    this.optKabid.user_id = user.id;
                    this.optKabid.nama = user.name;
                    this.optKabid.nip = user.nip || '-';
                    this.optKabid.jabatan = user.position || (selectedItem ? selectedItem.label : this.optKabid.selectedUnit);
                    this.optKabid.found = true;
                } else {
                    this.optKabid.user_id = null;
                    this.optKabid.nama = '';
                    this.optKabid.nip = '';
                    this.optKabid.jabatan = '';
                    this.optKabid.found = false;
                }
            },

            onSekretariatToggle() {
                if (!this.optSekretariat.enabled) {
                    this.optSekretariat.user_id = null;
                    this.optSekretariat.nama = '';
                    this.optSekretariat.nip = '';
                    this.optSekretariat.jabatan = '';
                    this.optSekretariat.found = null;
                    return;
                }
                const user = this.pimpinanUsers.find(u => {
                    const uBagian = (u.bagian || '').toLowerCase().trim();
                    const uPos = (u.position || '').toLowerCase().trim();
                    return uBagian === 'sekretariat' || uPos.includes('sekretaris');
                });

                if (user) {
                    this.optSekretariat.user_id = user.id;
                    this.optSekretariat.nama = user.name;
                    this.optSekretariat.nip = user.nip || '-';
                    this.optSekretariat.jabatan = user.position || 'Sekretaris Dinas Tenaga Kerja';
                    this.optSekretariat.found = true;
                } else {
                    this.optSekretariat.user_id = null;
                    this.optSekretariat.nama = '';
                    this.optSekretariat.nip = '';
                    this.optSekretariat.jabatan = '';
                    this.optSekretariat.found = false;
                }
            },

            onKadisToggle() {
                if (!this.optKadis.enabled) {
                    this.optKadis.user_id = null;
                    this.optKadis.nama = '';
                    this.optKadis.nip = '';
                    this.optKadis.jabatan = '';
                    this.optKadis.found = null;
                    return;
                }
                const user = this.pimpinanUsers.find(u => {
                    const uBagian = (u.bagian || '').toLowerCase().trim();
                    const uPos = (u.position || '').toLowerCase().trim();
                    return uBagian.includes('kepala dinas') || uPos.includes('kepala dinas') || uPos.includes('kadis');
                });

                if (user) {
                    this.optKadis.user_id = user.id;
                    this.optKadis.nama = user.name;
                    this.optKadis.nip = user.nip || '-';
                    this.optKadis.jabatan = user.position || 'Kepala Dinas Tenaga Kerja Provinsi Lampung';
                    this.optKadis.found = true;
                } else {
                    this.optKadis.user_id = null;
                    this.optKadis.nama = '';
                    this.optKadis.nip = '';
                    this.optKadis.jabatan = '';
                    this.optKadis.found = false;
                }
            },

            activeCount() {
                return this.activePenandatangans().length;
            },

            activePenandatangans() {
                const list = [];
                let urutan = 1;

                // 1. Sendiri (Pimpinan)
                if (this.optSendiri.enabled && this.optSendiri.nama) {
                    list.push({
                        urutan: urutan++,
                        peran: 'pembuat',
                        user_id: this.currentUserId,
                        nama: this.optSendiri.nama,
                        nip: this.optSendiri.nip,
                        jabatan: this.optSendiri.jabatan
                    });
                }

                // 2. Kabid / Staf / UPTD Lain
                if (this.optKabid.enabled && this.optKabid.found && this.optKabid.nama) {
                    list.push({
                        urutan: urutan++,
                        peran: 'pimpinan',
                        user_id: this.optKabid.user_id,
                        nama: this.optKabid.nama,
                        nip: this.optKabid.nip,
                        jabatan: this.optKabid.jabatan
                    });
                }

                // 3. Sekretaris Dinas
                if (this.optSekretariat.enabled && this.optSekretariat.found && this.optSekretariat.nama) {
                    list.push({
                        urutan: urutan++,
                        peran: 'pimpinan',
                        user_id: this.optSekretariat.user_id,
                        nama: this.optSekretariat.nama,
                        nip: this.optSekretariat.nip,
                        jabatan: this.optSekretariat.jabatan
                    });
                }

                // 4. Kadis
                if (this.optKadis.enabled && this.optKadis.found && this.optKadis.nama) {
                    list.push({
                        urutan: urutan++,
                        peran: 'pimpinan',
                        user_id: this.optKadis.user_id,
                        nama: this.optKadis.nama,
                        nip: this.optKadis.nip,
                        jabatan: this.optKadis.jabatan
                    });
                }

                return list;
            },

            validateForm(e) {
                if (this.optKabid.enabled && (!this.optKabid.selectedUnit || this.optKabid.found === false)) {
                    e.preventDefault();
                    alert('Tanda Tangan Kepala Bidang/Staf/UPTD diaktifkan namun belum memilih unit atau akun pimpinan belum terdaftar!');
                    return false;
                }
                if (this.optSekretariat.enabled && this.optSekretariat.found === false) {
                    e.preventDefault();
                    alert('Tanda Tangan Kepala Sekretariat diaktifkan namun akun Sekretaris Dinas belum terdaftar di sistem SIDORA!');
                    return false;
                }
                if (this.optKadis.enabled && this.optKadis.found === false) {
                    e.preventDefault();
                    alert('Tanda Tangan Kepala Dinas diaktifkan namun akun Kepala Dinas belum terdaftar di sistem SIDORA!');
                    return false;
                }
                if (this.activePenandatangans().length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal salah satu opsi tanda tangan untuk surat ini!');
                    return false;
                }
                return true;
            },

            init() {
                if (this.jenis) {
                    this.initAllEditors();
                }

                this.$watch('jenis', (value) => {
                    if (value) {
                        this.$nextTick(() => {
                            this.initAllEditors();
                        });
                    }
                });
            },

            initAllEditors() {
                if (!this.editorsInitialized.isi) {
                    window.initCkeditor('#editor_isi', 'input[name="isi"]');
                    this.editorsInitialized.isi = true;
                }

                if (this.jenis === 'keluar_full' && !this.editorsInitialized.header) {
                    this.$nextTick(() => {
                        window.initCkeditor('#editor_nama_instansi', 'input[name="nama_instansi"]');
                        this.editorsInitialized.header = true;
                    });
                }
            }
        }
    }
</script>
@endsection
