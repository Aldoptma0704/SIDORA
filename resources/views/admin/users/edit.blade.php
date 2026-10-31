@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto" x-data="userEditForm()">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-edit text-blue-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Pengguna</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi dan penugasan peran pengguna <strong>{{ $user->name }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Form Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Informasi Pengguna & Penugasan Struktur</h3>
                <p class="text-sm text-gray-600 mt-0.5">Sesuaikan peran dan unit bagian sesuai bagan organisasi Dinas Tenaga Kerja</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'pimpinan' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800') }}">
                Role Saat Ini: {{ ucfirst($user->role) }}
            </span>
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

            <!-- Bagian 1: Role & Bagian (Dinamis Sesuai Struktur) -->
            <div class="mb-8 p-5 bg-blue-50/50 rounded-2xl border border-blue-100">
                <div class="flex items-center gap-2 mb-4 text-blue-900 font-bold text-sm">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                    <span>Penugasan Peran & Bagian Kerja</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                            Peran Pengguna (Role) <span class="text-red-500">*</span>
                        </label>
                        <select name="role" 
                                id="role" 
                                x-model="selectedRole"
                                @change="onRoleChange()"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white font-medium text-gray-800">
                            <option value="admin">🛡️ Administrator (Subbag Umum & Kepegawaian)</option>
                            <option value="pimpinan">👑 Pimpinan (Kadis, Sekdis, Kasubbag, Kepala Staf, Kabid, Kepala UPTD)</option>
                            <option value="pegawai">👤 Pegawai (Seksi Bidang, Subbag TU UPTD, Staf)</option>
                        </select>
                    </div>

                    <!-- Bagian -->
                    <div>
                        <label for="bagian" class="block text-sm font-semibold text-gray-700 mb-2">
                            Bagian / Subbagian <span class="text-red-500">*</span>
                        </label>
                        <select name="bagian" 
                                id="bagian"
                                x-model="selectedBagian"
                                @change="onBagianChange()"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white font-medium text-gray-800">
                            <option value="">-- Pilih Bagian / Subbagian --</option>
                            <template x-for="item in currentBagianOptions" :key="item.value">
                                <option :value="item.value" x-text="item.label" :selected="item.value === selectedBagian"></option>
                            </template>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" x-show="selectedRole === 'admin'">
                            *Untuk Administrator, bagian ditugaskan pada <strong>Sub Bagian Umum dan Kepegawaian</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Data Pribadi & Identitas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-gray-400 mr-1.5"></i>
                        Nama Lengkap <span class="text-red-500">*</span>
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
                        <i class="fas fa-envelope text-gray-400 mr-1.5"></i>
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $user->email) }}" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="contoh@email.com">
                </div>

                <!-- Jabatan -->
                <div>
                    <label for="position" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-briefcase text-gray-400 mr-1.5"></i>
                        Jabatan Resmi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="position" 
                           id="position"
                           x-model="selectedPosition"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Contoh: Kepala Dinas / Kasubbag / Kepala Seksi / Staf">
                </div>

                <!-- NIP -->
                <div>
                    <label for="nip" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-id-card text-gray-400 mr-1.5"></i>
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

            <!-- Form Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function userEditForm() {
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
        selectedRole: '{{ old('role', $user->role) }}',
        selectedBagian: '{{ old('bagian', $user->bagian) }}',
        selectedPosition: '{{ old('position', $user->position) }}',
        currentBagianOptions: [],

        init() {
            this.updateOptions();
        },

        onRoleChange() {
            this.updateOptions();

            // Khusus admin, langsung otomatis set ke Sub Bagian Umum dan Kepegawaian
            if (this.selectedRole === 'admin') {
                this.selectedBagian = 'Sub Bagian Umum dan Kepegawaian';
                if (!this.selectedPosition) {
                    this.selectedPosition = 'Kasubbag Umum dan Kepegawaian';
                }
            } else {
                // Periksa apakah bagian saat ini masih valid di role baru
                const exists = this.currentBagianOptions.some(item => item.value === this.selectedBagian);
                if (!exists) {
                    this.selectedBagian = '';
                }
            }
        },

        onBagianChange() {
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
