@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-edit text-blue-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Pengguna</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi pengguna {{ $user->name }}</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Form Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Pengguna</h3>
            <p class="text-sm text-gray-600 mt-1">Lengkapi form di bawah untuk memperbarui data pengguna</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <h4 class="text-sm font-semibold text-red-800">Terdapat kesalahan pada form:</h4>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Content -->
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>
                            Nama Lengkap
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $user->name) }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                            Alamat Email
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $user->email) }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="contoh@email.com">
                    </div>

                    <!-- NIP -->
                    <div>
                        <label for="nip" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-card text-gray-400 mr-2"></i>
                            NIP (Nomor Induk Pegawai)
                        </label>
                        <input type="text" 
                               name="nip" 
                               id="nip"
                               value="{{ old('nip', $user->nip) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Masukkan NIP">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Jabatan -->
                    <div>
                        <label for="position" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-briefcase text-gray-400 mr-2"></i>
                            Jabatan
                        </label>
                        <input type="text" 
                               name="position" 
                               id="position"
                               value="{{ old('position', $user->position) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Masukkan jabatan">
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-shield text-gray-400 mr-2"></i>
                            Peran Pengguna
                        </label>
                        <select name="role" 
                                id="role" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="">-- Pilih Peran --</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                Administrator
                            </option>
                            <option value="pegawai" {{ old('role', $user->role) == 'pegawai' ? 'selected' : '' }}>
                                Pegawai
                            </option>
                            <option value="pimpinan" {{ old('role', $user->role) == 'pimpinan' ? 'selected' : '' }}>
                                Pimpinan
                            </option>
                        </select>
                    </div>

                    <!-- Bagian -->
                    <div>
                        <label for="bagian" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building text-gray-400 mr-2"></i>
                            Bagian/Unit Kerja
                        </label>
                        <select name="bagian" 
                                id="bagian"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="">-- Pilih Bagian --</option>
                            
                            <optgroup label="Sub Bagian Tata Usaha">
                                <option value="Sub Bagian Umum dan Kepegawaian" 
                                        {{ old('bagian', $user->bagian) == 'Sub Bagian Umum dan Kepegawaian' ? 'selected' : '' }}>
                                    Sub Bagian Umum dan Kepegawaian
                                </option>
                                <option value="Substansi Keuangan dan Aset" 
                                        {{ old('bagian', $user->bagian) == 'Substansi Keuangan dan Aset' ? 'selected' : '' }}>
                                    Substansi Keuangan dan Aset
                                </option>
                                <option value="Substansi Perencanaan" 
                                        {{ old('bagian', $user->bagian) == 'Substansi Perencanaan' ? 'selected' : '' }}>
                                    Substansi Perencanaan
                                </option>
                            </optgroup>
                            
                            <optgroup label="Bidang Operasional">
                                <option value="Penempatan dan Perluasan Kesempatan Kerja" 
                                        {{ old('bagian', $user->bagian) == 'Penempatan dan Perluasan Kesempatan Kerja' ? 'selected' : '' }}>
                                    Penempatan dan Perluasan Kesempatan Kerja
                                </option>
                                <option value="Pelatihan dan Produktivitas Tenaga Kerja" 
                                        {{ old('bagian', $user->bagian) == 'Pelatihan dan Produktivitas Tenaga Kerja' ? 'selected' : '' }}>
                                    Pelatihan dan Produktivitas Tenaga Kerja
                                </option>
                                <option value="Bidang Pengawasan Ketenagakerjaan" 
                                        {{ old('bagian', $user->bagian) == 'Bidang Pengawasan Ketenagakerjaan' ? 'selected' : '' }}>
                                    Bidang Pengawasan Ketenagakerjaan
                                </option>
                                <option value="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja" 
                                        {{ old('bagian', $user->bagian) == 'Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja' ? 'selected' : '' }}>
                                    Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja
                                </option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                
                <div class="flex gap-3">
                    <button type="reset" 
                            class="inline-flex items-center px-6 py-3 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-semibold rounded-lg transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </button>
                    
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Pengguna
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Custom focus styles */
    input:focus, select:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Optgroup styling */
    optgroup {
        font-weight: 600;
        color: #374151;
    }
    
    optgroup option {
        padding-left: 1rem;
        font-weight: 400;
    }
</style>
@endsection