<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Surat Dinas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>


    @vite(['resources/css/app.css', 'resources/js/editor.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] { display: none !important; }

        .ql-editor table {
            border-collapse: collapse;
            width: 100%;
        }

        .ql-editor table,
        .ql-editor th,
        .ql-editor td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .ql-editor th {
            background-color: #f9fafb;
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

<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-2">
            <img src="https://storage.googleapis.com/a1aa/image/1a26fe5e-e253-49c5-f177-88a240b1b315.jpg" class="w-8 h-8 rounded" alt="Logo">
            <span class="text-xl font-semibold text-blue-700">SuratDinas</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-1 text-red-600 hover:text-red-800">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 min-h-0">
        <aside class="w-64 bg-white border-r border-gray-200 shadow-sm hidden md:flex md:flex-col">
            <nav class="flex-1 px-4 py-6 space-y-2">
                @if (Auth::user()->role === 'admin')
                    <a href="/admin/dashboard" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3 font-medium">Dashboard Admin</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-users-cog w-5"></i>
                        <span class="ml-3 font-medium">Kelola Pengguna</span>
                    </a>
                    <a href="{{ route('admin.laporan') }}" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span class="ml-3 font-medium">Laporan Surat</span>
                    </a>
                    <a href="{{ route('admin.disposisi.masuk') }}" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-inbox w-5"></i>
                        <span class="ml-3 font-medium">Disposisi Masuk</span>
                    </a>
                    <a href="{{ route('admin.disposisi.form') }}" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-paper-plane w-5"></i>
                        <span class="ml-3 font-medium">Disposisi Surat</span>
                    </a>
                @elseif (Auth::user()->role === 'pegawai')
                    <a href="{{ route('pegawai.dashboard') }}" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                            <span class="flex items-center">
                                <i class="fas fa-envelope-open-text w-5"></i>
                                <span class="ml-3 font-medium">Daftar Surat</span>
                            </span>
                            <i class="fas transition-transform duration-200" :class="{'fa-chevron-up': open, 'fa-chevron-down': !open}"></i>
                        </button>
                        <div x-show="open" x-transition class="mt-2 pl-8 space-y-2">
                            <a href="{{ route('surat.index') }}" class="block px-3 py-1 rounded-md hover:font-semibold {{ !request('jenis') && request()->routeIs('surat.index') ? 'text-blue-600 font-bold' : 'text-gray-600' }}">
                                Semua Surat
                            </a>
                            <a href="{{ route('surat.index', ['jenis' => 'masuk']) }}" class="block px-3 py-1 rounded-md hover:font-semibold {{ request('jenis') == 'masuk' ? 'text-blue-600 font-bold' : 'text-gray-600' }}">
                                Surat Masuk
                            </a>
                            <a href="{{ route('surat.index', ['jenis' => 'keluar']) }}" class="block px-3 py-1 rounded-md hover:font-semibold {{ request('jenis') == 'keluar' ? 'text-blue-600 font-bold' : 'text-gray-600' }}">
                                Surat Keluar
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('surat.status_surat') }}" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-info-circle w-5"></i>
                        <span class="ml-3 font-medium">Status Surat</span>
                    </a>
                @elseif (Auth::user()->role === 'pimpinan')
                    <a href="/pimpinan/dashboard" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>
                    <a href="/pimpinan/disposisi" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-paper-plane w-5"></i>
                        <span class="ml-3 font-medium">Disposisi Surat</span>
                    </a>
                @endif
            </nav>
        </aside>

        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
