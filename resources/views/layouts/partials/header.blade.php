<header class="h-16 bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-40 px-4 sm:px-6 shadow-sm">
    <div class="h-full flex items-center justify-between gap-4">
        <!-- Sisi Kiri: Hamburger Mobile & Brand -->
        <div class="flex items-center gap-3">
            <!-- Tombol Hamburger Mobile -->
            <button @click="mobileSidebarOpen = !mobileSidebarOpen" 
                    type="button" 
                    aria-label="Buka Menu"
                    class="md:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                <i class="fas fa-bars text-lg"></i>
            </button>

            <!-- Brand Logo & Text -->
            <a href="{{ Auth::check() ? route(Auth::user()->role . '.dashboard') : route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('img/logo_lampung.png') }}" class="h-9 w-auto rounded" alt="Logo Lampung">
                <div class="flex flex-col">
                    <span class="text-base sm:text-lg font-bold text-primary-700 tracking-tight leading-none group-hover:text-primary-800">
                        SIDORA
                    </span>
                    <span class="text-[11px] text-gray-500 hidden sm:block leading-tight mt-0.5">
                        Sistem Informasi Dokumen Resmi
                    </span>
                </div>
            </a>
        </div>

        <!-- Sisi Kanan: Tanggal Hari Ini & Profil Dropdown -->
        <div class="flex items-center gap-4">
            <!-- Tanggal Hari Ini -->
            <div class="hidden lg:flex items-center gap-2 text-xs text-gray-500 border-r border-gray-200 pr-4">
                <i class="fas fa-calendar-alt text-gray-400"></i>
                <span>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
            </div>

            <!-- Profil Dropdown (Alpine.js) -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        type="button" 
                        class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none">
                    @if (Auth::user()->profile_photo)
                        <img src="{{ asset('storage/profile_photos/' . Auth::user()->profile_photo) }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="w-8 h-8 rounded-full object-cover border border-gray-200">
                    @else
                        <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-xs border border-primary-200">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="text-left hidden md:block">
                        <div class="text-xs font-semibold text-gray-800 leading-tight">
                            {{ Str::limit(Auth::user()->name, 18) }}
                        </div>
                        <div class="text-[10px] text-gray-500 capitalize leading-tight">
                            {{ Auth::user()->role }}
                        </div>
                    </div>

                    <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-0.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <!-- Menu Dropdown -->
                <div x-show="open" 
                     @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100" 
                     x-transition:enter-start="transform opacity-0 scale-95" 
                     x-transition:enter-end="transform opacity-100 scale-100" 
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="transform opacity-100 scale-100" 
                     x-transition:leave-end="transform opacity-0 scale-95" 
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5 z-50 focus:outline-none" 
                     x-cloak>
                    <!-- Detail Pengguna -->
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                        <span class="inline-block mt-1.5 px-2 py-0.5 text-[10px] font-medium rounded-full 
                            @if(Auth::user()->role === 'admin') bg-blue-100 text-blue-800
                            @elseif(Auth::user()->role === 'pimpinan') bg-indigo-100 text-indigo-800
                            @else bg-slate-100 text-slate-800 @endif capitalize">
                            {{ Auth::user()->role }}
                        </span>
                    </div>

                    <!-- Tautan Profil -->
                    <div class="py-1">
                        <a href="{{ route('profile.show') }}" 
                           class="flex items-center gap-2.5 px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 hover:text-primary-700 transition-colors">
                            <i class="fas fa-user-circle text-gray-400 w-4 text-center"></i>
                            <span>Profil Saya</span>
                        </a>
                    </div>

                    <!-- Logout Button -->
                    <div class="pt-1 border-t border-gray-100">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors text-left">
                                <i class="fas fa-sign-out-alt text-red-500 w-4 text-center"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
