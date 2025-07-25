@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-plus text-green-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Pengguna Baru</h1>
                <p class="text-gray-600 mt-1">Lengkapi formulir di bawah untuk menambahkan pengguna baru ke sistem SIDORA</p>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-red-800 font-semibold mb-2">Terjadi kesalahan pada formulir:</h3>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 text-sm flex items-center gap-2">
                                <i class="fas fa-circle text-xs"></i>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Personal Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-blue-600"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Informasi Personal</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                           placeholder="contoh@example.com">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        Jabatan
                    </label>
                    <input type="text" 
                           name="position" 
                           id="position" 
                           value="{{ old('position') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('position') border-red-500 @enderror"
                           placeholder="Masukkan jabatan">
                    @error('position')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- NIP -->
                <div>
                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                        NIP (Nomor Induk Pegawai)
                    </label>
                    <input type="text" 
                           name="nip" 
                           id="nip" 
                           value="{{ old('nip') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('nip') border-red-500 @enderror"
                           placeholder="Masukkan NIP">
                    @error('nip')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Role & Department Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-purple-600"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Role & Bagian</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Peran (Role) <span class="text-red-500">*</span>
                    </label>
                    <select name="role" 
                            id="role" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('role') border-red-500 @enderror">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                            🛡️ Administrator
                        </option>
                        <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>
                            👤 Pegawai
                        </option>
                        <option value="pimpinan" {{ old('role') == 'pimpinan' ? 'selected' : '' }}>
                            👑 Pimpinan
                        </option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="bagian" class="block text-sm font-medium text-gray-700 mb-2">
                        Bagian/Subbagian
                    </label>
                    <select name="bagian" 
                            id="bagian"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bagian') border-red-500 @enderror">
                        <option value="">-- Pilih Bagian --</option>
                        <optgroup label="Bagian Sekretariat">
                            <option value="Sub Bagian Umum dan Kepegawaian" {{ old('bagian') == 'Sub Bagian Umum dan Kepegawaian' ? 'selected' : '' }}>
                                Sub Bagian Umum dan Kepegawaian
                            </option>
                            <option value="Substansi Keuangan dan Aset" {{ old('bagian') == 'Substansi Keuangan dan Aset' ? 'selected' : '' }}>
                                Substansi Keuangan dan Aset
                            </option>
                            <option value="Substansi Perencanaan" {{ old('bagian') == 'Substansi Perencanaan' ? 'selected' : '' }}>
                                Substansi Perencanaan
                            </option>
                        </optgroup>
                        <optgroup label="Bidang Operasional">
                            <option value="Penempatan dan Perluasan Kesempatan Kerja" {{ old('bagian') == 'Penempatan dan Perluasan Kesempatan Kerja' ? 'selected' : '' }}>
                                Penempatan dan Perluasan Kesempatan Kerja
                            </option>
                            <option value="Pelatihan dan Produktivitas Tenaga Kerja" {{ old('bagian') == 'Pelatihan dan Produktivitas Tenaga Kerja' ? 'selected' : '' }}>
                                Pelatihan dan Produktivitas Tenaga Kerja
                            </option>
                            <option value="Bidang Pengawasan Ketenagakerjaan" {{ old('bagian') == 'Bidang Pengawasan Ketenagakerjaan' ? 'selected' : '' }}>
                                Bidang Pengawasan Ketenagakerjaan
                            </option>
                            <option value="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja" {{ old('bagian') == 'Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja' ? 'selected' : '' }}>
                                Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja
                            </option>
                        </optgroup>
                    </select>
                    @error('bagian')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Security Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-orange-600"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Keamanan Akun</h2>
            </div>

            <div class="max-w-md">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror"
                               placeholder="Masukkan password">
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="toggleIcon"></i>
                        </button>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">
                        Password minimal 8 karakter, kombinasi huruf dan angka
                    </p>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('users.index') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            
            <button type="submit"
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i>
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<style>
    .shadow-custom {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endsection