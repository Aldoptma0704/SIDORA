@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto" x-data="userForm()">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-plus text-green-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Pengguna Baru</h1>
                <p class="text-gray-600 mt-1">Lengkapi formulir di bawah sesuai dengan struktur organisasi Dinas Tenaga Kerja Provinsi Lampung</p>
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
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Role & Bagian Section (Pertama-tama Pilih Role) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-purple-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">1. Peran & Penugasan Struktur</h2>
                    <p class="text-xs text-gray-500">Pilih Role terlebih dahulu untuk menyesuaikan Bagian/Subbagian</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Role (Pertama Dipilih) -->
                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                        Peran (Role) Pengguna <span class="text-red-500">*</span>
                    </label>
                    <select name="role" 
                            id="role" 
                            x-model="selectedRole" 
                            @change="onRoleChange()"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium text-gray-800 bg-white @error('role') border-red-500 @enderror">
                        <option value="">-- Pilih Role Terlebih Dahulu --</option>
                        <option value="admin">🛡️ Administrator (Subbag Umum & Kepegawaian)</option>
                        <option value="pimpinan">👑 Pimpinan (Kadis, Sekdis, Kasubbag, Kepala Staf, Kabid, Kepala UPTD)</option>
                        <option value="pegawai">👤 Pegawai (Seksi Bidang, Subbag TU UPTD, Staf)</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Bagian/Subbagian (Dinamis Sesuai Role) -->
                <div>
                    <label for="bagian" class="block text-sm font-semibold text-gray-700 mb-2">
                        Bagian / Subbagian <span class="text-red-500">*</span>
                    </label>
                    <select name="bagian" 
                            id="bagian"
                            x-model="selectedBagian"
                            @change="onBagianChange()"
                            :disabled="!selectedRole"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-800 bg-white disabled:bg-gray-100 disabled:text-gray-400 @error('bagian') border-red-500 @enderror">
                        <option value="" x-text="!selectedRole ? '-- Pilih Role Terlebih Dahulu --' : '-- Pilih Bagian / Subbagian --'"></option>
                        
                        {{-- Opsi dinamis dimuat oleh AlpineJS/JS --}}
                        <template x-for="item in currentBagianOptions" :key="item.value">
                            <option :value="item.value" x-text="item.label" :selected="item.value === selectedBagian"></option>
                        </template>
                    </select>
                    <p class="text-xs text-gray-500 mt-1" x-show="selectedRole === 'admin'">
                        *Untuk Role Admin, bagian otomatis ditujukan ke <strong>Sub Bagian Umum dan Kepegawaian</strong>.
                    </p>
                    @error('bagian')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 2. Personal Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">2. Informasi Personal Pegawai</h2>
                    <p class="text-xs text-gray-500">Identitas pegawai dan jabatan resmi</p>
                </div>
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
                           placeholder="Contoh: Dr. H. Agus Nompitu, S.E., M.T.P.">
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
                        Email Resmi <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                           placeholder="contoh@disnaker.lampungprov.go.id">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Position / Jabatan -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        Jabatan Resmi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="position" 
                           id="position" 
                           x-model="selectedPosition"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('position') border-red-500 @enderror"
                           placeholder="Contoh: Kepala Dinas / Kasubbag / Kepala Seksi / Staf">
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
                           placeholder="Contoh: 19750812 199903 1 002">
                    @error('nip')
                        <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 3. Security Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-orange-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">3. Keamanan Akun</h2>
                    <p class="text-xs text-gray-500">Kata sandi masuk ke aplikasi SIDORA</p>
                </div>
            </div>

            <div class="max-w-md">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               id="password" 
                               required
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror"
                               placeholder="Masukkan password">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">
                        Password minimal 6 karakter
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
            <a href="{{ route('admin.users.index') }}" 
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
function userForm() {
    const dataStruktur = {
        admin: [
            { value: 'Sub Bagian Umum dan Kepegawaian', label: 'Sub Bagian Umum dan Kepegawaian', defaultPos: 'Kasubbag Umum dan Kepegawaian' }
        ],
        pimpinan: [
            // Pimpinan Utama & Sekretariat
            { value: 'Kepala Dinas Tenaga Kerja', label: 'Kepala Dinas Tenaga Kerja', defaultPos: 'Kepala Dinas' },
            { value: 'Sekretariat', label: 'Sekretaris Dinas (Kepala Sekretariat)', defaultPos: 'Sekretaris Dinas' },
            { value: 'Sub Bagian Umum dan Kepegawaian', label: 'Sub Bagian Umum dan Kepegawaian (Kasubbag)', defaultPos: 'Kasubbag Umum dan Kepegawaian' },
            { value: 'Staf Perencana Sekretariat', label: 'Kepala Staf Perencana', defaultPos: 'Kepala Staf Perencana' },
            { value: 'Keuangan dan Aset Sekretariat', label: 'Kepala Staf Keuangan', defaultPos: 'Kepala Staf Keuangan' },
            { value: 'Kepegawaian Sekretariat', label: 'Kepala Staf Kepegawaian', defaultPos: 'Kepala Staf Kepegawaian' },
            
            // Kepala Bidang (Kabid)
            { value: 'Bidang Penempatan dan Perluasan Kesempatan Kerja', label: 'Kepala Bidang Penempatan dan Perluasan Kesempatan Kerja', defaultPos: 'Kepala Bidang Penempatan dan Perluasan Kesempatan Kerja' },
            { value: 'Bidang Pelatihan dan Produktivitas Tenaga Kerja', label: 'Kepala Bidang Pelatihan dan Produktivitas Tenaga Kerja', defaultPos: 'Kepala Bidang Pelatihan dan Produktivitas Tenaga Kerja' },
            { value: 'Bidang Pengawasan Ketenagakerjaan', label: 'Kepala Bidang Pengawasan Ketenagakerjaan', defaultPos: 'Kepala Bidang Pengawasan Ketenagakerjaan' },
            { value: 'Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja', label: 'Kepala Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja', defaultPos: 'Kepala Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja' },
            
            // Kepala UPTD
            { value: 'UPTD BLK Bandar Lampung', label: 'Kepala UPTD BLK Bandar Lampung', defaultPos: 'Kepala UPTD BLK Bandar Lampung' },
            { value: 'UPTD BLK Metro', label: 'Kepala UPTD BLK Metro', defaultPos: 'Kepala UPTD BLK Metro' },
            { value: 'UPTD BLK Kalianda', label: 'Kepala UPTD BLK Kalianda', defaultPos: 'Kepala UPTD BLK Kalianda' },
            { value: 'UPTD BLK Way Abung', label: 'Kepala UPTD BLK Way Abung', defaultPos: 'Kepala UPTD BLK Way Abung' },
            { value: 'UPTD Balai Pengembangan Produktivitas Daerah', label: 'Kepala UPTD Balai Pengembangan Produktivitas Daerah', defaultPos: 'Kepala UPTD Balai Pengembangan Produktivitas Daerah' },
            { value: 'UPTD Balai Keselamatan dan Kesehatan Kerja', label: 'Kepala UPTD Balai Keselamatan dan Kesehatan Kerja (K3)', defaultPos: 'Kepala UPTD Balai Keselamatan dan Kesehatan Kerja' }
        ],
        pegawai: [
            // Staf Langsung Sekretariat
            { value: 'Sub Bagian Umum dan Kepegawaian', label: 'Staf Subbag Umum dan Kepegawaian', defaultPos: 'Staf Subbag Umum dan Kepegawaian' },
            { value: 'Staf Perencana Sekretariat', label: 'Staf Perencana', defaultPos: 'Staf Perencana' },
            { value: 'Keuangan dan Aset Sekretariat', label: 'Staf Keuangan dan Aset', defaultPos: 'Staf Keuangan dan Aset' },
            { value: 'Kepegawaian Sekretariat', label: 'Staf Kepegawaian', defaultPos: 'Staf Kepegawaian' },

            // Seksi Bidang Penempatan & Perluasan
            { value: 'Seksi Penempatan Tenaga Kerja Dalam Negeri', label: 'Seksi Penempatan Tenaga Kerja Dalam Negeri', defaultPos: 'Seksi Penempatan Tenaga Kerja Dalam Negeri' },
            { value: 'Seksi Pengembangan Informasi Pasar Kerja', label: 'Seksi Pengembangan Informasi Pasar Kerja', defaultPos: 'Seksi Pengembangan Informasi Pasar Kerja' },

            // Seksi Bidang Pelatihan & Produktivitas
            { value: 'Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja', label: 'Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja', defaultPos: 'Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja' },
            { value: 'Seksi Pengembangan Produktivitas Tenaga Kerja', label: 'Seksi Pengembangan Produktivitas Tenaga Kerja', defaultPos: 'Seksi Pengembangan Produktivitas Tenaga Kerja' },

            // Seksi Bidang Pengawasan Ketenagakerjaan
            { value: 'Seksi Pengawasan Norma Kerja, Jamsostek, Pekerja Perempuan dan Anak', label: 'Seksi Pengawasan Norma Kerja, Jamsostek, Perempuan & Anak', defaultPos: 'Seksi Pengawasan Norma Kerja, Jamsostek, Pekerja Perempuan dan Anak' },
            { value: 'Seksi Penegakan Hukum dan Penindakan', label: 'Seksi Penegakan Hukum dan Penindakan', defaultPos: 'Seksi Penegakan Hukum dan Penindakan' },

            // Seksi Bidang Hubungan Industrial & Perlindungan
            { value: 'Seksi Pembinaan Organisasi Pekerja, Pengusaha dan Lembaga HI', label: 'Seksi Pembinaan Organisasi Pekerja, Pengusaha & Lembaga HI', defaultPos: 'Seksi Pembinaan Organisasi Pekerja, Pengusaha dan Lembaga HI' },
            { value: 'Seksi Pembinaan Syarat Kerja dan Jamsostek', label: 'Seksi Pembinaan Syarat Kerja dan Jamsostek', defaultPos: 'Seksi Pembinaan Syarat Kerja dan Jamsostek' },

            // Subbag Tata Usaha UPTD
            { value: 'Subbag Tata Usaha BLK Bandar Lampung', label: 'Subbag Tata Usaha BLK Bandar Lampung', defaultPos: 'Subbag Tata Usaha BLK Bandar Lampung' },
            { value: 'Subbag Tata Usaha BLK Metro', label: 'Subbag Tata Usaha BLK Metro', defaultPos: 'Subbag Tata Usaha BLK Metro' },
            { value: 'Subbag Tata Usaha BLK Kalianda', label: 'Subbag Tata Usaha BLK Kalianda', defaultPos: 'Subbag Tata Usaha BLK Kalianda' },
            { value: 'Subbag Tata Usaha BLK Way Abung', label: 'Subbag Tata Usaha BLK Way Abung', defaultPos: 'Subbag Tata Usaha BLK Way Abung' },
            { value: 'Subbag Tata Usaha Balai Pengembangan Produktivitas Daerah', label: 'Subbag Tata Usaha Balai Pengembangan Produktivitas Daerah', defaultPos: 'Subbag Tata Usaha Balai Pengembangan Produktivitas Daerah' },
            { value: 'Subbag Tata Usaha Balai Keselamatan dan Kesehatan Kerja', label: 'Subbag Tata Usaha Balai Keselamatan dan Kesehatan Kerja (K3)', defaultPos: 'Subbag Tata Usaha Balai Keselamatan dan Kesehatan Kerja' }
        ]
    };

    return {
        selectedRole: '{{ old('role', '') }}',
        selectedBagian: '{{ old('bagian', '') }}',
        selectedPosition: '{{ old('position', '') }}',
        showPassword: false,
        currentBagianOptions: [],

        init() {
            if (this.selectedRole) {
                this.updateOptions();
            }
        },

        onRoleChange() {
            this.selectedBagian = '';
            this.updateOptions();

            // Khusus admin, langsung otomatis set ke Sub Bagian Umum dan Kepegawaian
            if (this.selectedRole === 'admin') {
                this.selectedBagian = 'Sub Bagian Umum dan Kepegawaian';
                if (!this.selectedPosition) {
                    this.selectedPosition = 'Kasubbag Umum dan Kepegawaian';
                }
            }
        },

        onBagianChange() {
            // Otomatis isi jabatan resmi sesuai bagian/subbagian yang dipilih
            const currentItem = this.currentBagianOptions.find(item => item.value === this.selectedBagian);
            if (currentItem && currentItem.defaultPos) {
                this.selectedPosition = currentItem.defaultPos;
            }
        },

        updateOptions() {
            if (this.selectedRole && dataStruktur[this.selectedRole]) {
                this.currentBagianOptions = dataStruktur[this.selectedRole];
            } else {
                this.currentBagianOptions = [];
            }
        }
    };
}
</script>
@endsection
