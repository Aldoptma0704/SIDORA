@extends('layouts.app') {{-- Ganti jika layout-mu beda --}}
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">Ganti Password</h2>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <div class="mb-4">
            <label for="current_password" class="block text-gray-700">Password Lama</label>
            <input type="password" name="current_password" id="current_password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300">
        </div>

        <div class="mb-4">
            <label for="new_password" class="block text-gray-700">Password Baru</label>
            <input type="password" name="new_password" id="new_password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300">
        </div>

        <div class="mb-6">
            <label for="new_password_confirmation" class="block text-gray-700">Konfirmasi Password Baru</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300">
        </div>

        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Ganti Password</button>
    </form>
</div>
@endsection
