@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
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
                        <h1 class="text-3xl font-bold mb-2">Edit Surat Balasan</h1>
                        <p class="text-blue-100 text-lg">Perbarui isi dan detail surat balasan Anda</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="mb-6 animate-fade-in">
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md">
                    <p class="font-medium text-red-800">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Edit Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ route('pimpinan.surat.update', $surat->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Surat</label>
                    <input type="text" name="judul" id="judul" 
                           value="{{ old('judul', $surat->judul) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5" required>
                </div>

                <div class="mb-6">
                    <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Surat</label>
                    <input type="text" name="tujuan" id="tujuan" 
                           value="{{ old('tujuan', $surat->tujuan) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5" required>
                </div>

                <div class="mb-6">
                    <label for="isi" class="block text-sm font-medium text-gray-700 mb-1">Isi Surat</label>
                    <textarea name="isi" id="isi" rows="10"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5"
                              required>{{ old('isi', strip_tags($surat->isi)) }}</textarea>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('pimpinan.status-surat') }}" 
                       class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
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

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-5px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.4s ease-out; }
</style>
@endsection
