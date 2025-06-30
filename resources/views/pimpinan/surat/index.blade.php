@extends('layouts.app')

@section('content')
<h1 class="text-2xl mb-4">Surat Menunggu Persetujuan</h1>

<ul class="mt-4">
    @foreach ($surats as $surat)
        <li class="mb-2 p-2 border rounded">
            <strong>{{ $surat->judul }}</strong>
            <form method="POST" action="{{ route('pimpinan.surat.setujui', $surat->id) }}" class="inline">
                @csrf
                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Setujui</button>
            </form>
            <form method="POST" action="{{ route('pimpinan.surat.tolak', $surat->id) }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Tolak</button>
            </form>
        </li>
    @endforeach
</ul>
@endsection
