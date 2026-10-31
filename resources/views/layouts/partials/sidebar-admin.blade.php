<nav class="space-y-6">
    <!-- Grup: Utama -->
    <div>
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Utama</p>
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'border-l-4 border-blue-500 bg-slate-800 text-white pl-2' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-pie w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Grup: Surat -->
    <div>
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Surat</p>
        <div class="space-y-1">
            <a href="{{ route('admin.surat.masuk') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.surat.masuk*') ? 'border-l-4 border-blue-500 bg-slate-800 text-white pl-2' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-inbox w-5 text-center {{ request()->routeIs('admin.surat.masuk*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                    <span>Surat Masuk</span>
                </div>
                @if(isset($countAdminSuratMasuk) && $countAdminSuratMasuk > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        {{ $countAdminSuratMasuk }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.laporan') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.laporan*') ? 'border-l-4 border-blue-500 bg-slate-800 text-white pl-2' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-alt w-5 text-center {{ request()->routeIs('admin.laporan*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                    <span>Laporan Surat</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Grup: Manajemen Sistem -->
    <div>
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Manajemen</p>
        <div class="space-y-1">
            <a href="{{ route('admin.users.index') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users*') ? 'border-l-4 border-blue-500 bg-slate-800 text-white pl-2' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-users-cog w-5 text-center {{ request()->routeIs('admin.users*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                    <span>Kelola Pengguna</span>
                </div>
            </a>
        </div>
    </div>
</nav>
