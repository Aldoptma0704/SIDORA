@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">📄 Daftar Surat</h1>

{{-- Flash Message --}}
@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Tabs --}}
<div class="flex space-x-4 mb-6">
    <a href="?filter=menunggu" class="px-4 py-2 rounded {{ request('filter') === 'menunggu' || !request('filter') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">Menunggu</a>
    <a href="?filter=disetujui" class="px-4 py-2 rounded {{ request('filter') === 'disetujui' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">Disetujui</a>
    <a href="?filter=ditolak" class="px-4 py-2 rounded {{ request('filter') === 'ditolak' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }}">Ditolak</a>
</div>

{{-- Surat berdasarkan filter --}}
<ul class="space-y-4">
    @php
        $filter = request('filter') ?? 'menunggu';
        $filteredSurats = $surats->filter(fn($s) => strtolower($s->status) === strtolower($filter));
    @endphp

    @forelse ($filteredSurats as $surat)
        <li class="p-4 bg-white shadow border rounded hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-blue-700 mb-1">
                        {{ $surat->judul }}
                    </h2>
                    <p class="text-sm text-gray-600 mb-2">Dibuat: {{ $surat->created_at->format('d M Y') }}</p>
                    <p class="text-sm text-gray-500">Status: 
                        @if($surat->status === 'disetujui')
                            <span class="text-green-600 font-semibold">Disetujui</span>
                        @elseif($surat->status === 'ditolak')
                            <span class="text-red-600 font-semibold">Ditolak</span>
                        @else
                            <span class="text-gray-600">Menunggu</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        {!! Str::limit(strip_tags($surat->isi), 30) !!}
                    </p>
                </div>

                <div class="ml-4 flex flex-col gap-2 items-end">
                    <a href="{{ route('pimpinan.surat.preview', $surat->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm">👁️ Lihat</a>

                    @if($surat->status === 'menunggu')
                        {{-- Tombol Setujui & Disposisi --}}
                        <div x-data="{ open: false }" x-cloak>
                            <button @click="open = true"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                ✅ Setujui & Disposisi
                            </button>

                            <!-- Modal -->
                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white p-6 rounded shadow-md max-w-lg w-full">
                                    <h3 class="text-lg font-semibold mb-4">Disposisi ke Bidang</h3>
                                    <form method="POST" action="{{ route('pimpinan.surat.setujui', $surat->id) }}">
                                        @csrf

                                        <label class="block mb-2 text-sm font-medium">Pilih Bidang/Subbidang:</label>
                                        <select name="disposisi" class="w-full p-2 border rounded mb-4" required>
                                            <optgroup label="Sekretariat">
                                                <option value="Sub Bagian Umum dan Kepegawaian">Sub Bagian Umum dan Kepegawaian</option>
                                                <option value="Substansi Keuangan dan Aset">Substansi Keuangan dan Aset</option>
                                                <option value="Substansi Perencanaan">Substansi Perencanaan</option>
                                            </optgroup>
                                            <optgroup label="Penempatan dan Perluasan Kesempatan Kerja">
                                                <option value="Seksi Penempatan Tenaga Kerja Dalam Negeri">Seksi Penempatan Tenaga Kerja Dalam Negeri</option>
                                                <option value="Substansi Penempatan Tenaga Kerja Luar Negeri">Substansi Penempatan Tenaga Kerja Luar Negeri</option>
                                                <option value="Seksi Pengembangan Informasi Pasar Kerja">Seksi Pengembangan Informasi Pasar Kerja</option>
                                            </optgroup>
                                            <optgroup label="Pelatihan dan Produktivitas Tenaga Kerja">
                                                <option value="Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja">Substansi Pembinaan, Pelatihan dan Pemagangan Tenaga Kerja</option>
                                                <option value="Seksi Pengembangan Produktivitas Tenaga Kerja">Seksi Pengembangan Produktivitas Tenaga Kerja</option>
                                                <option value="Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja">Seksi Pembinaan Lembaga Pelatihan Tenaga Kerja</option>
                                            </optgroup>
                                            <optgroup label="Bidang Pengawasan Ketenagakerjaan">
                                                <option value="Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan">Seksi Pengawasan Norma Kerja Jamsostek, Pekerja Perempuan dan Anak, Seksi Penegakan Hukum dan Penindakan</option>
                                                <option value="Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja">Substansi  Pengawasan Norma Keselamatan dan Kesehatan Kerja</option>
                                            </optgroup>
                                            <optgroup label="Bidang Hubungan Industrial dan Perlindungan Tenaga Kerja">
                                                <option value="Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial">Seksi Pembinaan Organisasi Pekerja Pengusaha dan Lembaga Hubungan Industrial</option>
                                                <option value="Seksi Pembinaan Syarat Kerja dan Jamsostek">Seksi Pembinaan Syarat Kerja dan Jamsostek</option>
                                                <option value="Substansi Penyelesaian Perselisihan Hubungan Industrial">Substansi Penyelesaian Perselisihan Hubungan Industrial</option>
                                            </optgroup>
                                        </select>
                                        
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" @click="open = false" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Kirim Disposisi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Tolak --}}
                        <div x-data="{ openTolak: false }" x-cloak>
                            <button @click="openTolak = true"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                ❌ Tolak
                            </button>

                            <!-- Modal Tolak -->
                            <div x-show="openTolak" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white p-6 rounded shadow-md max-w-lg w-full">
                                    <h3 class="text-lg font-semibold mb-4 text-red-600">Tolak Surat</h3>
                                    <form method="POST" action="{{ route('pimpinan.surat.tolak', $surat->id) }}">
                                        @csrf
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Alasan Penolakan:</label>
                                        <textarea name="alasan_penolakan" rows="4" class="w-full p-2 border rounded mb-4" required></textarea>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" @click="openTolak = false" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Tolak Surat</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif($surat->status === 'ditolak' && $surat->alasan_penolakan)
                        <span class="text-sm text-red-600 italic">Alasan: {{ $surat->alasan_penolakan }}</span>
                    @elseif($surat->status === 'disetujui' && $surat->disposisi)
                        <span class="text-sm text-green-600 italic">Disposisi: {{ $surat->disposisi }}</span>
                    @endif
                </div>
            </div>
        </li>
    @empty
        <li class="text-gray-500 text-sm">Tidak ada surat {{ $filter }}.</li>
    @endforelse
</ul>
@endsection
