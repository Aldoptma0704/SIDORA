<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Surat Dinas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .input-group {
            position: relative;
        }
        .input-label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            background-color: white;
            padding: 0 4px;
            color: #6B7280;
            transition: all 0.2s;
            pointer-events: none;
        }
        .input-field:focus + .input-label,
        .input-field:not(:placeholder-shown) + .input-label {
            top: -8px;
            left: 8px;
            font-size: 12px;
            color: #2563EB;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-200 to-blue-300 flex items-center justify-center p-4">

    <form method="POST" action="{{ route('login.process') }}" class="bg-white shadow-xl rounded-xl p-6 w-full max-w-sm animate-fadeIn">
        @csrf
        <h2 class="text-3xl font-bold text-center text-blue-600 mb-6">Login Sistem Surat Dinas</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 text-sm px-3 py-2 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-5 input-group">
            <input type="email" name="email" id="email" required placeholder=" " class="input-field shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
            <label for="email" class="input-label">Email</label>
        </div>

        <div class="mb-6 input-group">
            <input type="password" name="password" id="password" required placeholder=" " class="input-field shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
            <label for="password" class="input-label">Password</label>
        </div>

        <button id="loginBtn" type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200">
            <span id="loginText">Login</span>
            <span id="loadingSpinner" class="hidden ml-2 inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
        </button>
    </form>

    <script>
        // Loading spinner saat tombol ditekan
        document.getElementById('loginBtn').addEventListener('click', function() {
            document.getElementById('loginText').innerText = 'Logging in...';
            document.getElementById('loadingSpinner').classList.remove('hidden');
        });
    </script>

    <style>
        /* Animasi fadeIn saat halaman muncul */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }
    </style>

</body>
</html>
