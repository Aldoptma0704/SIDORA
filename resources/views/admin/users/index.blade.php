@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📋 Daftar Pengguna</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            ➕ Tambah Pengguna
        </a>
    </div>

    <table class="min-w-full bg-white border border-gray-200 rounded shadow overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nama</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Email</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Role</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Bagian</th>
                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm text-gray-700">
        @foreach ($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-2 border-b">{{ $user->name }}</td>
                <td class="px-4 py-2 border-b">{{ $user->email }}</td>
                <td class="px-4 py-2 border-b capitalize">{{ $user->role }}</td>
                <td class="px-4 py-2 border-b">{{ $user->bagian ?? '-' }}</td>
                <td class="px-4 py-2 border-b space-x-2">
                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-3 py-1 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1 text-sm text-white bg-red-600 hover:bg-red-700 rounded-md shadow">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
