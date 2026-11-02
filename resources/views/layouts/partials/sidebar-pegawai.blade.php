<nav class="space-y-6">
    <!-- Grup: Utama -->
    <div>
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Utama</p>
        <div class="space-y-1">
            <a href="{{ route('pegawai.dashboard') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pegawai.dashboard') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-pie w-5 text-center {{ request()->routeIs('pegawai.dashboard') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Grup: Surat -->
    <div>
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pengelolaan Surat</p>
        <div class="space-y-1">
            <a href="{{ route('pegawai.surat.create') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pegawai.surat.create') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-plus-circle w-5 text-center {{ request()->routeIs('pegawai.surat.create') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                    <span>Buat Surat</span>
                </div>
            </a>

            <a href="{{ route('pegawai.surat.status') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pegawai.surat.status') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-tasks w-5 text-center {{ request()->routeIs('pegawai.surat.status') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                    <span>Status Surat</span>
                </div>
                @if(isset($countPegawaiMenunggu) && $countPegawaiMenunggu > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                        {{ $countPegawaiMenunggu }}
                    </span>
                @endif
            </a>

            <!-- Submenu Daftar Surat -->
            <div x-data="{ open: {{ (request()->routeIs('pegawai.surat.*') && !request()->routeIs('pegawai.surat.status') && !request()->routeIs('pegawai.surat.create')) ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" 
                        type="button"
                        class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ (request()->routeIs('pegawai.surat.*') && !request()->routeIs('pegawai.surat.status') && !request()->routeIs('pegawai.surat.create')) ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-folder-open w-5 text-center {{ (request()->routeIs('pegawai.surat.*') && !request()->routeIs('pegawai.surat.status') && !request()->routeIs('pegawai.surat.create')) ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        <span>Arsip Surat</span>
                    </div>
                    <i class="fas text-xs transition-transform duration-200" :class="open ? 'fa-chevron-down rotate-180' : 'fa-chevron-down'"></i>
                </button>
                <div x-show="open" x-transition class="pl-8 pr-2 space-y-1 pt-1">
                    <a href="{{ route('pegawai.surat.index') }}" 
                       class="block px-3 py-2 rounded-md text-xs font-medium transition-colors {{ (!request('jenis') && request()->routeIs('pegawai.surat.index')) ? 'bg-primary-100/60 text-primary-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        Semua Surat
                    </a>
                    <a href="{{ route('pegawai.surat.index', ['jenis' => 'masuk']) }}" 
                       class="block px-3 py-2 rounded-md text-xs font-medium transition-colors {{ (request('jenis') === 'masuk' && request()->routeIs('pegawai.surat.index')) ? 'bg-primary-100/60 text-primary-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        Surat Masuk
                    </a>
                    <a href="{{ route('pegawai.surat.index', ['jenis' => 'keluar']) }}" 
                       class="block px-3 py-2 rounded-md text-xs font-medium transition-colors {{ (request('jenis') === 'keluar' && request()->routeIs('pegawai.surat.index')) ? 'bg-primary-100/60 text-primary-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        Surat Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
