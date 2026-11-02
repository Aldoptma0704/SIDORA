@php
    $isKadis = Auth::user()->isKepalaDinas();
@endphp
<nav class="space-y-6">
    <!-- Grup: Utama -->
    <div>
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Utama</p>
        <div class="space-y-1">
            <a href="{{ route('pimpinan.dashboard') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pimpinan.dashboard') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-pie w-5 text-center {{ request()->routeIs('pimpinan.dashboard') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Grup: Surat & Persetujuan -->
    <div>
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Surat & Disposisi</p>
        <div class="space-y-1">
            <a href="{{ route('pimpinan.surat-masuk') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pimpinan.surat-masuk*') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-inbox w-5 text-center {{ request()->routeIs('pimpinan.surat-masuk*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                    <span>Surat Masuk</span>
                </div>
                @if(isset($countPimpinanMenunggu) && $countPimpinanMenunggu > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800" title="{{ $countPimpinanMenunggu }} surat perlu ditinjau">
                        {{ $countPimpinanMenunggu }}
                    </span>
                @endif
            </a>

            <a href="{{ route('pimpinan.disposisi') }}" 
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pimpinan.disposisi*') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-paper-plane w-5 text-center {{ request()->routeIs('pimpinan.disposisi*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                    <span>Disposisi Surat</span>
                </div>
            </a>

            @if(!$isKadis)
                <a href="{{ route('pimpinan.balasan') }}" 
                   class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pimpinan.balasan*') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-reply w-5 text-center {{ request()->routeIs('pimpinan.balasan*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        <span>Surat Balasan</span>
                    </div>
                </a>

                <a href="{{ route('pimpinan.status-surat') }}" 
                   class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pimpinan.status-surat*') ? 'border-l-4 border-primary-600 bg-primary-50 text-primary-700 font-semibold pl-2' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-list-check w-5 text-center {{ request()->routeIs('pimpinan.status-surat*') ? 'text-primary-600' : 'text-gray-400' }}"></i>
                        <span>Status Surat Balasan</span>
                    </div>
                </a>
            @endif
        </div>
    </div>
</nav>
