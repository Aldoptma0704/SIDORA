@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Buat Surat Balasan</h1>
                        <p class="text-blue-100 text-lg">Formulir pembuatan surat balasan resmi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Form Container --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden" x-data="suratForm()">
            
            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-red-800 mb-2">Terjadi Kesalahan Validasi</h3>
                            <ul class="text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center">
                                        <span class="w-1 h-1 bg-red-500 rounded-full mr-2"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('pimpinan.surat-balasan.simpan') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                <input type="hidden" name="surat_asal_id" value="{{ $suratAsal->id ?? '' }}">

                {{-- Step 1: Jenis Surat --}}
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-sm mr-3">1</div>
                        <h3 class="text-lg font-semibold text-gray-800">Pilih Jenis Surat</h3>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Surat <span class="text-red-500">*</span></label>
                        <select name="jenis" x-model="jenis" 
                                class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" 
                                required>
                            <option value="" disabled>-- Pilih Jenis Surat --</option>
                            <option value="keluar_full">🏢 Surat Keluar (Dengan Kop Surat Lengkap)</option>
                            <option value="keluar">📄 Surat Keluar (Template Standar)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">Pilih jenis surat sesuai dengan kebutuhan format dokumen</p>
                    </div>
                </div>

                {{-- Form Content (Show only when jenis is selected) --}}
                <div x-show="jenis" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">

                    {{-- Step 2: Header Surat (Only for keluar_full) --}}
                    <div x-show="jenis === 'keluar_full'" x-transition class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold text-sm mr-3">2</div>
                            <h3 class="text-lg font-semibold text-gray-800">Header Surat</h3>
                        </div>
                        
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-200">
                            {{-- Logo Upload --}}
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Instansi</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-indigo-300 border-dashed rounded-xl cursor-pointer bg-white hover:bg-indigo-50 transition-colors duration-200">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-indigo-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-2 text-sm text-indigo-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                                            <p class="text-xs text-indigo-400">PNG, JPG atau GIF (MAX. 2MB)</p>
                                        </div>
                                        <input type="file" name="logo_instansi_file" accept="image/*" class="hidden">
                                    </label>
                                </div>
                            </div>

                            {{-- Instansi Info Grid --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Instansi <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div id="editor_nama_instansi" class="w-full border-2 border-gray-300 rounded-xl p-4 min-h-[80px] bg-white focus-within:border-indigo-500 transition-colors duration-200">{!! old('nama_instansi') !!}</div>
                                        <input type="hidden" name="nama_instansi" value="{{ old('nama_instansi') }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon / Kontak</label>
                                    <div class="relative">
                                        <div id="editor_kontak_instansi" class="w-full border-2 border-gray-300 rounded-xl p-4 min-h-[80px] bg-white focus-within:border-indigo-500 transition-colors duration-200">{!! old('kontak_instansi') !!}</div>
                                        <input type="hidden" name="kontak_instansi" value="{{ old('kontak_instansi') }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Instansi</label>
                                <div class="relative">
                                    <div id="editor_alamat_instansi" class="w-full border-2 border-gray-300 rounded-xl p-4 min-h-[80px] bg-white focus-within:border-indigo-500 transition-colors duration-200">{!! old('alamat_instansi') !!}</div>
                                    <input type="hidden" name="alamat_instansi" value="{{ old('alamat_instansi') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Detail Surat --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-semibold text-sm mr-3">
                                <span x-text="jenis === 'keluar_full' ? '3' : '2'"></span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Detail Surat Keluar</h3>
                        </div>
                        
                        <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-xl p-6 border border-green-200">
                            {{-- Basic Info Grid --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Surat</label>
                                    <div class="relative">
                                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" 
                                               class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200"
                                               placeholder="Contoh: 001/DS/2024">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sifat</label>
                                    <select name="sifat" class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200">
                                        <option value="Biasa" {{ old('sifat') == 'Biasa' ? 'selected' : '' }}>📄 Biasa</option>
                                        <option value="Penting" {{ old('sifat') == 'Penting' ? 'selected' : '' }}>⚠️ Penting</option>
                                        <option value="Segera" {{ old('sifat') == 'Segera' ? 'selected' : '' }}>🚨 Segera</option>
                                        <option value="Rahasia" {{ old('sifat') == 'Rahasia' ? 'selected' : '' }}>🔒 Rahasia</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran</label>
                                    <input type="text" name="lampiran" value="{{ old('lampiran', '-') }}" 
                                           class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200"
                                           placeholder="Contoh: 1 (satu) berkas">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hal (Perihal) <span class="text-red-500">*</span></label>
                                    <input type="text" name="judul" value="{{ old('judul') }}" 
                                           class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200"
                                           placeholder="Contoh: Balasan Permohonan Izin"
                                           required>
                                </div>
                            </div>

                            {{-- Tujuan Surat --}}
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Surat <span class="text-red-500">*</span></label>
                                <textarea name="tujuan" rows="3" 
                                          class="w-full p-4 border-2 border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 resize-none"
                                          placeholder="Contoh: Kepala Bagian Umum dan Kepegawaian"
                                          required>{{ old('tujuan') }}</textarea>
                            </div>

                            {{-- Isi Surat --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Isi Surat <span class="text-red-500">*</span></label>
                                <div class="border-2 border-gray-300 rounded-xl bg-white focus-within:border-green-500 transition-colors duration-200">
                                    <div id="editor_isi" class="w-full p-4 min-h-[250px]">{!! old('isi') !!}</div>
                                    <input type="hidden" name="isi" id="isi_hidden" value="{{ old('isi') }}">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Gunakan editor untuk memformat teks dengan rapi</p>
                            </div>
                        </div>
                    </div>

                    {{-- Step 4: Penandatangan Info --}}
                    @if(isset($pimpinan))
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-semibold text-sm mr-3">
                                    <span x-text="jenis === 'keluar_full' ? '4' : '3'"></span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Informasi Penandatangan</h3>
                            </div>
                            
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                                <div class="flex items-start space-x-4">
                                    <div class="bg-purple-100 rounded-full p-3">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 mb-4">Penandatangan Otomatis</h4>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            <div class="space-y-3">
                                                <div class="flex items-center">
                                                    <span class="text-sm font-medium text-gray-600 w-20">Nama:</span>
                                                    <span class="text-sm text-gray-800">{{ $pimpinan->nama_lengkap ?? 'Belum diisi' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-sm font-medium text-gray-600 w-20">Jabatan:</span>
                                                    <span class="text-sm text-gray-800">{{ $pimpinan->jabatan ?? 'Belum diisi' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-sm font-medium text-gray-600 w-20">NIP:</span>
                                                    <span class="text-sm text-gray-800">{{ $pimpinan->pangkat_nip ?? 'Belum diisi' }}</span>
                                                </div>
                                            </div>
                                            @if($pimpinan->ttd)
                                                <div class="flex justify-center lg:justify-end">
                                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                                                        <p class="text-xs text-gray-600 mb-2 text-center">Tanda Tangan Digital</p>
                                                        <img src="{{ asset('storage/ttd/' . $pimpinan->ttd) }}" 
                                                             alt="Tanda Tangan" 
                                                             class="h-20 w-auto mx-auto">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex justify-center lg:justify-end">
                                                    <div class="bg-gray-100 rounded-xl p-6 text-center">
                                                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                        <p class="text-xs text-gray-500">Tanda tangan belum diupload</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                        <a href="/pimpinan/dashboard" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white font-medium rounded-xl hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan Surat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Enhanced JavaScript --}}
<script>
    function suratForm() {
        return {
            jenis: '{{ old('jenis', '') }}',
            editorsInitialized: {
                isi: false,
                header: false
            },
            
            init() {
                // Initialize editors if form has validation errors
                if (this.jenis) {
                    this.initAllEditors();
                }

                // Watch for changes in jenis dropdown
                this.$watch('jenis', () => {
                    this.initAllEditors();
                });
            },

            initAllEditors() {
                if (!this.jenis) return;

                this.$nextTick(() => {
                    // Initialize content editor (once)
                    if (!this.editorsInitialized.isi) {
                        this.createEditor('#editor_isi', '#isi_hidden');
                        this.editorsInitialized.isi = true;
                    }

                    // Initialize header editors for full letterhead (once)
                    if (this.jenis === 'keluar_full' && !this.editorsInitialized.header) {
                        this.createEditor('#editor_nama_instansi', 'input[name=nama_instansi]');
                        this.createEditor('#editor_kontak_instansi', 'input[name=kontak_instansi]');
                        this.createEditor('#editor_alamat_instansi', 'input[name=alamat_instansi]');
                        this.editorsInitialized.header = true;
                    }
                });
            },

            createEditor(editorSelector, hiddenInputSelector) {
                const element = document.querySelector(editorSelector);
                if (!element) return;

                // Use global initCkeditor function
                if (typeof window.initCkeditor === 'function') {
                    window.initCkeditor(editorSelector, hiddenInputSelector);
                }
            }
        }
    }

    // Enhanced file upload preview
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[type="file"][name="logo_instansi_file"]');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Create preview (you can enhance this further)
                        console.log('File selected:', file.name);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>

<style>
    /* Custom scrollbar for editors */
    .ck-editor__editable {
        max-height: 300px;
        overflow-y: auto;
    }

    /* Smooth transitions */
    * {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-duration: 200ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* File upload hover effects */
    .file-upload-area:hover {
        background-color: #f0f9ff;
        border-color: #3b82f6;
    }

    /* Focus states */
    .focus-within\:border-indigo-500:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .focus-within\:border-green-500:focus-within {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
</style>

@endsection