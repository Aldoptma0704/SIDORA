@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Edit Surat Balasan</h1>
                            <p class="text-blue-100 text-lg">Perbarui detail surat balasan Anda</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="mb-6 animate-fade-in">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="mb-6 animate-fade-in">
                <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</p>
                            <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Edit Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ route('pimpinan.surat.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Use PUT or PATCH for updates --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Surat</label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul', $surat->judul) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5" required>
                    </div>
                    <div>
                        <label for="nomor_surat" class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat', $surat->nomor_surat) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>
                    <div>
                        <label for="sifat" class="block text-sm font-medium text-gray-700 mb-1">Sifat Surat</label>
                        <input type="text" name="sifat" id="sifat" value="{{ old('sifat', $surat->sifat) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>
                    <div>
                        <label for="lampiran" class="block text-sm font-medium text-gray-700 mb-1">Lampiran</label>
                        <input type="text" name="lampiran" id="lampiran" value="{{ old('lampiran', $surat->lampiran) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>
                    <div class="md:col-span-2">
                        <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Surat</label>
                        <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan', $surat->tujuan) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat</label>
                    <select name="jenis" id="jenis" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5" required>
                        <option value="biasa" {{ old('jenis', $surat->jenis) == 'biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="keluar_full" {{ old('jenis', $surat->jenis) == 'keluar_full' ? 'selected' : '' }}>Keluar Full (dengan logo instansi)</option>
                    </select>
                </div>

                <div id="instansi_details" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 {{ $surat->jenis === 'keluar_full' ? '' : 'hidden' }}">
                    <div>
                        <label for="nama_instansi" class="block text-sm font-medium text-gray-700 mb-1">Nama Instansi</label>
                        <input type="text" name="nama_instansi" id="nama_instansi" value="{{ old('nama_instansi', $surat->nama_instansi) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>
                    <div>
                        <label for="kontak_instansi" class="block text-sm font-medium text-gray-700 mb-1">Kontak Instansi</label>
                        <input type="text" name="kontak_instansi" id="kontak_instansi" value="{{ old('kontak_instansi', $surat->kontak_instansi) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>
                    <div class="md:col-span-2">
                        <label for="alamat_instansi" class="block text-sm font-medium text-gray-700 mb-1">Alamat Instansi</label>
                        <textarea name="alamat_instansi" id="alamat_instansi" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">{{ old('alamat_instansi', $surat->alamat_instansi) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="logo_instansi" class="block text-sm font-medium text-gray-700 mb-1">Logo Instansi</label>
                        @if ($surat->logo_instansi)
                            <div class="mb-2">
                                <p class="text-xs text-gray-500">Logo saat ini:</p>
                                <img src="{{ asset('storage/logo/' . $surat->logo_instansi) }}" alt="Current Logo" class="h-20 w-auto object-contain rounded-md border border-gray-200 p-1">
                            </div>
                        @endif
                        <input type="file" name="logo_instansi" id="logo_instansi" 
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah logo.</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="isi" class="block text-sm font-medium text-gray-700 mb-1">Isi Surat</label>
                    {{-- For a rich text editor like Quill, you'd initialize it here --}}
                    {{-- For now, a simple textarea --}}
                    <textarea name="isi" id="isi" rows="10" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">{{ old('isi', strip_tags($surat->isi)) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Anda dapat menggunakan editor teks kaya (seperti Quill.js) untuk memformat isi surat.</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('pimpinan.statussurat') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-all duration-200 shadow-md hover:shadow-lg">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }

    /* Smooth transitions */
    * {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-duration: 200ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<script>
    // JavaScript to toggle instansi_details based on 'jenis' select
    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('jenis');
        const instansiDetails = document.getElementById('instansi_details');

        function toggleInstansiDetails() {
            if (jenisSelect.value === 'keluar_full') {
                instansiDetails.classList.remove('hidden');
            } else {
                instansiDetails.classList.add('hidden');
            }
        }

        jenisSelect.addEventListener('change', toggleInstansiDetails);

        // Initial check on page load
        toggleInstansiDetails();
    });
</script>
@endsection
