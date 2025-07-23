@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-bold mb-6">Profil Pengguna</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('POST')

        @if (Auth::user()->role === 'admin')
        <div>
            @if ($user->profile_photo)
                <img src="{{ asset('storage/profile_photos/' . $user->profile_photo) }}" alt="Foto Profil" class="w-24 h-24 rounded-full mt-2">
            @endif
            <label for="profile_photo">Foto Profil</label>
            <input type="file" name="profile_photo" id="profile_photo">
        </div>

        <br>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Nama</label>
            <input type="text" name="name" value="{{ $user->name }}"
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror">
            @error('name')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Jabatan</label>
            <input type="text" name="position" value="{{ $user->position }}"
                   class="w-full border rounded p-2 @error('position') border-red-500 @enderror">
            @error('position')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">NIP</label>
            <input type="text" name="nip" value="{{ $user->nip }}"
                   class="w-full border rounded p-2 @error('nip') border-red-500 @enderror">
            @error('nip')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" value="{{ $user->email }}"
                   class="w-full border rounded p-2 @error('email') border-red-500 @enderror">
            @error('email')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        @else
        <div>
            @if ($user->profile_photo)
                <img src="{{ asset('storage/profile_photos/' . $user->profile_photo) }}" alt="Foto Profil" class="w-24 h-24 rounded-full mt-2">
            @endif
            <label for="profile_photo">Foto Profil</label>
            <input type="file" name="profile_photo" id="profile_photo">
        </div>

        <br>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Nama</label>
            <p class="p-2 border rounded bg-gray-100">{{ $user->name }}</p>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Jabatan</label>
            <p class="p-2 border rounded bg-gray-100">{{ $user->position }}</p>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">NIP</label>
            <p class="p-2 border rounded bg-gray-100">{{ $user->nip }}</p>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Email</label>
            <p class="p-2 border rounded bg-gray-100">{{ $user->email }}</p>
        </div>
        @endif

        <div class="mb-4">
            <label class="block font-semibold mb-1">Upload Tanda Tangan</label>
            <input type="file" name="signature" accept="image/*"
                   class="w-full border rounded p-2 @error('signature') border-red-500 @enderror">
            @error('signature')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            @if($user->signature)
                <div class="mt-3">
                    <p class="text-sm mb-1">Tanda tangan saat ini:</p>
                    <img src="{{ asset('storage/signatures/' . $user->signature) }}" alt="Tanda Tangan"
                         class="w-48 border rounded">
                </div>
            @endif
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
