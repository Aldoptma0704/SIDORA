@extends('layouts.app')

@section('content')
<h1 class="text-2xl mb-4">Dashboard Admin</h1>
<a href="{{ route('users.create') }}" class="bg-green-500 text-white px-3 py-2 rounded">Tambah Pengguna</a>

<table class="table-auto w-full mt-4">
    <thead>
        <tr>
            <th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td>
                <a href="{{ route('users.edit', $user) }}" class="text-blue-500">Edit</a>
                <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
