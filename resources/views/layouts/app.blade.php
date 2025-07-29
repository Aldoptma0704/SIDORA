<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Surat Dinas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        [x-cloak] { display: none !important; }

        .sidebar-link {
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            transform: translateX(2px);
        }

        .shadow-custom {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .ql-editor table {
            border-collapse: collapse;
            width: 100%;
        }

        .ql-editor table,
        .ql-editor th,
        .ql-editor td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }

        .ql-editor th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .ql-editor ol,
        .ql-editor ul {
            margin-left: 1.5rem;
            padding-left: 1rem;
        }

        .ql-editor ol {
            list-style-type: decimal;
        }

        .ql-editor ul {
            list-style-type: disc;
        }

        .ql-editor li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>

@stack('scripts')

<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 shadow-custom fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center">
            <!-- Logo & Title -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/logo_lampung.png') }}" class="h-12 w-auto rounded" alt="Logo">
                    <div>
                        <h1 class="text-xl font-bold text-primary-700">Surat Dinas (SIDORA)</h1>
                        <p class="text-sm text-gray-600">Sistem Informasi Dokumen Resmi</p>
                    </div>
                </div>
            </div>

            <!-- User Info & Actions -->
            <div class="flex items-center gap-4">
                <!-- User Profile -->
                <div class="flex items-center gap-3">
                    @if (Auth::user()->profile_photo)
                        <img src="{{ asset('storage/profile_photos/' . Auth::user()->profile_photo) }}" 
                            alt="Foto Profil" 
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 shadow-sm">
                    @else
                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center border-2 border-primary-200">
                            <i class="fas fa-user text-primary-600"></i>
                        </div>
                    @endif

                    <div class="text-right">
                        <span class="block text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                        <a href="{{ route('profile.show') }}" class="text-xs text-primary-600 hover:text-primary-800 hover:underline">Lihat Profil</a>
                    </div>
                </div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt text-lg"></i>
                        <span class="hidden sm:inline ml-1">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex flex-1 min-h-0">
        <!-- Sidebar -->
        <aside class="w-72 bg-white border-r border-gray-200 shadow-custom hidden md:flex md:flex-col fixed top-[80px] bottom-0 left-0 overflow-y-auto">
            <nav class="flex-1 px-6 py-6 space-y-2">
                @if (Auth::user()->role === 'admin')
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Menu Admin</h3>
                    
                    <a href="/admin/dashboard" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->is('admin/dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->is('admin/dashboard') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-tachometer-alt {{ request()->routeIs('admin.dashboard') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Dashboard Admin</span>
                    </a>

                    <a href="{{ route('users.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('users.index') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('users.index') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-users-cog {{ request()->routeIs('users.index') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Kelola Pengguna</span>
                    </a>

                    <a href="{{ route('admin.laporan') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.laporan') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('admin.laporan') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-chart-bar {{ request()->routeIs('admin.laporan') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Laporan Surat</span>
                    </a>

                    <a href="{{ route('admin.disposisi.masuk') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.disposisi.masuk') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('admin.disposisi.masuk') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-inbox {{ request()->routeIs('admin.disposisi.masuk') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Disposisi Masuk</span>
                    </a>

                    <a href="{{ route('admin.disposisi.form') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.disposisi.form') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('admin.disposisi.form') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-paper-plane {{ request()->routeIs('admin.disposisi.form') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Disposisi Surat</span>
                    </a>

                    <a href="{{ route('admin.surat.masuk') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.surat.masuk') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('admin.surat.masuk') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-inbox {{ request()->routeIs('admin.surat.masuk') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Surat Masuk Pimpinan</span>
                    </a>

                @elseif (Auth::user()->role === 'pegawai')
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Menu Pegawai</h3>
                    
                    <a href="{{ route('pegawai.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('pegawai.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('pegawai.dashboard') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-tachometer-alt {{ request()->routeIs('pegawai.dashboard') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <div x-data="{ open: {{ request()->routeIs('surat.index') || request()->routeIs('surat.create') || request()->routeIs('surat.edit') || request()->routeIs('surat.show') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-xl {{ (request()->routeIs('surat.index') || request()->routeIs('surat.create') || request()->routeIs('surat.edit') || request()->routeIs('surat.show')) ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                            <div class="flex items-center">
                                <div class="w-10 h-10 {{ (request()->routeIs('surat.index') || request()->routeIs('surat.create') || request()->routeIs('surat.edit') || request()->routeIs('surat.show')) ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-envelope-open-text {{ (request()->routeIs('surat.index') || request()->routeIs('surat.create') || request()->routeIs('surat.edit') || request()->routeIs('surat.show')) ? 'text-primary-600' : 'text-gray-600' }}"></i>
                                </div>
                                <span class="font-medium">Daftar Surat</span>
                            </div>
                            <i class="fas transition-transform duration-200" :class="{'fa-chevron-up': open, 'fa-chevron-down': !open}"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 space-y-1 bg-gray-50 rounded-lg p-2">
                            <a href="{{ route('surat.index') }}" class="block px-4 py-2 rounded-lg text-sm hover:bg-white hover:shadow-sm {{ !request('jenis') && request()->routeIs('surat.index') ? 'text-primary-600 font-semibold bg-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                <i class="fas fa-list w-4 mr-2"></i>
                                Semua Surat
                            </a>
                            <a href="{{ route('surat.index', ['jenis' => 'masuk']) }}" class="block px-4 py-2 rounded-lg text-sm hover:bg-white hover:shadow-sm {{ request('jenis') == 'masuk' ? 'text-primary-600 font-semibold bg-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                <i class="fas fa-inbox w-4 mr-2"></i>
                                Surat Masuk
                            </a>
                            <a href="{{ route('surat.index', ['jenis' => 'keluar']) }}" class="block px-4 py-2 rounded-lg text-sm hover:bg-white hover:shadow-sm {{ request('jenis') == 'keluar' ? 'text-primary-600 font-semibold bg-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                <i class="fas fa-paper-plane w-4 mr-2"></i>
                                Surat Keluar
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('surat.status_surat') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('surat.status_surat') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('surat.status_surat') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle {{ request()->routeIs('surat.status_surat') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Status Surat</span>
                    </a>

                @elseif (Auth::user()->role === 'pimpinan')
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Menu Pimpinan</h3>
                    
                    <a href="/pimpinan/dashboard" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->is('pimpinan/dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->is('pimpinan/dashboard') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-tachometer-alt {{ request()->is('pimpinan/dashboard') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="/pimpinan/disposisi" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->is('pimpinan/disposisi*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->is('pimpinan/disposisi*') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-paper-plane {{ request()->is('pimpinan/disposisi*') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Disposisi Surat</span>
                    </a>

                    <a href="{{ route('pimpinan.statussurat') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('pimpinan.statussurat') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->routeIs('pimpinan.statussurat') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-table {{ request()->routeIs('pimpinan.statussurat') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Status Surat Balasan</span>
                    </a>

                    <a href="/pimpinan/balasansurat" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->is('pimpinan/balasansurat*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                        <div class="w-10 h-10 {{ request()->is('pimpinan/balasansurat*') ? 'bg-primary-200' : 'bg-gray-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-paper-plane {{ request()->is('pimpinan/balasansurat*') ? 'text-primary-600' : 'text-gray-600' }}"></i>
                        </div>
                        <span class="font-medium">Surat Balasan</span>
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6 pl-72 pt-[80px]">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    window.initCkeditor = function (editorSelector, hiddenInputSelector) {
        const targetElement = document.querySelector(editorSelector);
        const hiddenInput = document.querySelector(hiddenInputSelector);

        if (targetElement && hiddenInput) {
            ClassicEditor
                .create(targetElement, {
                    toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                })
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        hiddenInput.value = editor.getData();
                    });

                    // Sync editor content on page load (for old values)
                    hiddenInput.value = editor.getData();
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });
        }
    };
</script>
</body>
</html>