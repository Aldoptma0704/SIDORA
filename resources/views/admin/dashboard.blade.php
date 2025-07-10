@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Dashboard Admin</h1>
    <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded shadow hover:bg-green-700 transition">
        <i class="fas fa-user-plus mr-2"></i> Tambah Pengguna
    </a>
</div>

<table class="min-w-full bg-white border border-gray-200 rounded shadow overflow-hidden">
    <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
        <tr>
            <th class="px-4 py-2 border-b">Nama</th>
            <th class="px-4 py-2 border-b">Email</th>
            <th class="px-4 py-2 border-b">Role</th>
            <th class="px-4 py-2 border-b">Bagian</th>
            <th class="px-4 py-2 border-b">Aksi</th>
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
@endsection
