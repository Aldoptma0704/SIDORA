<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Surat Dinas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
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

    <!-- Body Layout -->
    <div class="flex flex-1 min-h-0">
        <!-- Sidebar -->
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
                @elseif (Auth::user()->role === 'pegawai')
                    <a href="/pegawai/dashboard" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('surat.index') }}" class="flex items-center px-3 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                        <i class="fas fa-envelope-open-text w-5"></i>
                        <span class="ml-3 font-medium">Daftar Surat</span>
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

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
