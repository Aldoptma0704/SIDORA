@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-circle text-primary-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Profil Pengguna</h1>
                <p class="text-gray-600 mt-1">Kelola informasi akun dan tanda tangan digital Anda</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl mb-6 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="font-medium">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Photo Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Profil</h3>
            
            <div class="text-center">
                @if ($user->profile_photo)
                    <img src="{{ asset('storage/profile_photos/' . $user->profile_photo) }}" 
                         alt="Foto Profil" 
                         class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-primary-100 shadow-lg">
                @else
                    <div class="w-32 h-32 rounded-full bg-gray-100 mx-auto border-4 border-gray-200 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-gray-400"></i>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    @method('POST')
                    
                    <div class="mb-4">
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Foto Baru
                        </label>
                        <input type="file" 
                               name="profile_photo" 
                               id="profile_photo" 
                               accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
            </div>
        </div>

        <!-- User Information Section -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Pengguna</h3>

            @if (Auth::user()->role === 'admin')
                <!-- Admin - Editable Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" 
                               name="name" 
                               value="{{ $user->name }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <input type="text" 
                               name="position" 
                               value="{{ $user->position }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('position') border-red-500 @enderror">
                        @error('position')
                            <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                        <input type="text" 
                               name="nip" 
                               value="{{ $user->nip }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('nip') border-red-500 @enderror">
                        @error('nip')
                            <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               name="email" 
                               value="{{ $user->email }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

            @else
                <!-- Non-Admin - Read Only Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                            {{ $user->name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                            {{ $user->position }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                        <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                            {{ $user->nip }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                            {{ $user->email }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Digital Signature Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Tanda Tangan Digital</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upload Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Tanda Tangan Baru</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-signature text-primary-600"></i>
                    </div>
                    <input type="file" 
                           name="signature" 
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('signature') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, atau GIF (Maks. 2MB)</p>
                    @error('signature')
                        <div class="text-red-500 text-sm mt-2 flex items-center justify-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Preview Section -->
            <div>
                @if($user->signature)
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan Saat Ini</label>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <img src="{{ asset('storage/signatures/' . $user->signature) }}"
                             alt="Tanda Tangan"
                             class="max-w-full h-32 object-contain mx-auto rounded border bg-white p-2">
                        <p class="text-xs text-gray-500 text-center mt-2">Tanda tangan yang sedang digunakan</p>
                    </div>
                @else
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Tanda Tangan</label>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 text-center">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-signature text-gray-400"></i>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada tanda tangan yang diupload</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-4 mt-6">
        <a href="{{ url()->previous() }}" 
           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
        
        <button type="submit"
                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors shadow-sm">
            <i class="fas fa-save mr-2"></i>
            Simpan Perubahan
        </button>
    </div>
    
    </form>
</div>

<style>
    .shadow-custom {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    input[type="file"]::-webkit-file-upload-button {
        visibility: hidden;
    }
    
    input[type="file"]::before {
        content: 'Pilih File';
        display: inline-block;
        background: linear-gradient(top, #f9f9f9, #e3e3e3);
        border: 1px solid #999;
        border-radius: 3px;
        padding: 5px 8px;
        outline: none;
        white-space: nowrap;
        cursor: pointer;
        text-shadow: 1px 1px #fff;
        font-weight: 700;
        font-size: 10pt;
    }
    
    input[type="file"]:hover::before {
        border-color: black;
    }
    
    input[type="file"]:active::before {
        background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
    }
</style>
@endsection