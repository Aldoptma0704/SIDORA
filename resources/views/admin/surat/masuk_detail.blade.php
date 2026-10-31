@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8" x-data="{ openKirimModal: false }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-3 text-green-800">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    <div>
                        <p class="font-semibold text-sm">Berhasil!</p>
                        <p class="text-xs text-green-700 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Top Navigation & Action Buttons --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <a href="{{ route('admin.surat.masuk') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Surat Masuk
            </a>

            <div class="flex items-center space-x-2">
                @if($surat->file_path)
                    <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl shadow-sm transition">
                        <i class="fas fa-external-link-alt mr-1.5"></i> Buka Berkas Asli
                    </a>
                @endif

                <button @click="openKirimModal = true" type="button" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <i class="fas fa-paper-plane mr-1.5"></i> {{ $surat->disposisiKepada ? 'Teruskan / Ganti Penerima' : 'Kirim ke Karyawan' }}
                </button>

                <form action="{{ route('admin.surat.masuk.destroy', $surat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat masuk ini? Berkas arsip juga akan terhapus.');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        <i class="fas fa-trash-alt mr-1.5"></i> Hapus
                    </button>
                </form>
            </div>
        </div>

        {{-- Detail Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="p-6 sm:p-8">
                
                {{-- Badge & Judul --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-800 rounded-lg">
                        <i class="fas fa-inbox mr-1"></i> Surat Masuk
                    </span>
                    @if($surat->sifat)
                        <span class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg">
                            Sifat: {{ $surat->sifat }}
                        </span>
                    @endif
                    @if($surat->disposisiKepada)
                        <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-lg">
                            <i class="fas fa-check-circle mr-1"></i> Terkirim ke Karyawan
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-lg">
                            <i class="fas fa-clock mr-1"></i> Belum Dikirim ke Karyawan
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-6 leading-tight">
                    {{ $surat->judul }}
                </h1>

                {{-- Two Column Info Box --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-gray-50 rounded-2xl border border-gray-200 mb-6 text-sm">
                    {{-- Left Column: Pengirim & Surat --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 pb-1 border-b border-gray-200">
                            Informasi Pengirim & Surat
                        </h4>
                        <div>
                            <span class="text-gray-500 text-xs">Asal Instansi / Pengirim:</span>
                            <p class="font-bold text-gray-900 mt-0.5">{{ $surat->nama_instansi ?: '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs">Nomor Surat Masuk:</span>
                            <p class="font-medium text-gray-900 mt-0.5">{{ $surat->nomor_surat ?: '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs">Tanggal Diterima / Diunggah:</span>
                            <p class="font-medium text-gray-900 mt-0.5">
                                {{ $surat->created_at ? $surat->created_at->locale('id')->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                            </p>
                        </div>
                        @if($surat->lampiran)
                            <div>
                                <span class="text-gray-500 text-xs">Lampiran:</span>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $surat->lampiran }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Right Column: Tujuan Pengguna (Pegawai) --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 pb-1 border-b border-gray-200">
                            Tujuan Pengguna (Karyawan Disnaker)
                        </h4>
                        @if($surat->disposisiKepada)
                            <div class="p-3 bg-white rounded-xl border border-emerald-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($surat->disposisiKepada->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $surat->disposisiKepada->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $surat->disposisiKepada->email }}</p>
                                        <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 rounded-full">
                                            {{ $surat->disposisiKepada->bagian ?? 'Pegawai' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-white rounded-xl border border-dashed border-amber-300 text-center">
                                <p class="text-xs font-semibold text-amber-800 mb-2">Surat ini belum dikirimkan ke karyawan.</p>
                                <button @click="openKirimModal = true" type="button" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm">
                                    <i class="fas fa-paper-plane mr-1.5"></i> Tentukan Karyawan Penerima
                                </button>
                            </div>
                        @endif

                        <div class="pt-2">
                            <span class="text-gray-500 text-xs">Diunggah Oleh:</span>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $surat->user->name ?? 'Admin SIDORA' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Keterangan / Catatan Isi --}}
                @if($surat->isi)
                    <div class="mb-8">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Ringkasan / Keterangan Isi Surat:
                        </h4>
                        <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-800 leading-relaxed border border-gray-100">
                            {!! nl2br(e($surat->isi)) !!}
                        </div>
                    </div>
                @endif

                {{-- Berkas File Viewer --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center justify-between">
                        <span>Berkas Surat Masuk:</span>
                        @if($surat->file_path)
                            <a href="{{ asset('storage/' . $surat->file_path) }}" download class="text-xs font-semibold text-primary-600 hover:underline">
                                <i class="fas fa-download mr-1"></i> Unduh File
                            </a>
                        @endif
                    </h4>

                    @if($surat->file_path)
                        @php
                            $ext = strtolower(pathinfo($surat->file_path, PATHINFO_EXTENSION));
                        @endphp

                        @if($ext === 'pdf')
                            <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                                <iframe src="{{ asset('storage/' . $surat->file_path) }}" class="w-full h-[750px] border-0"></iframe>
                            </div>
                        @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                            <div class="text-center p-4 bg-gray-100 rounded-xl border border-gray-200">
                                <img src="{{ asset('storage/' . $surat->file_path) }}" alt="Surat Masuk" class="max-h-[800px] mx-auto rounded-lg shadow-sm">
                            </div>
                        @else
                            <div class="p-8 text-center bg-gray-50 rounded-xl border border-gray-200">
                                <i class="fas fa-file-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm font-semibold text-gray-700">Berkas format {{ strtoupper($ext) }}</p>
                                <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 mt-3 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold rounded-lg shadow-sm">
                                    <i class="fas fa-download mr-1.5"></i> Unduh Berkas untuk Membaca
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-6 text-center bg-gray-50 rounded-xl text-gray-500 text-sm">
                            Tidak ada berkas yang dilampirkan.
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>

    <!-- MODAL KIRIM / TERUSKAN SURAT -->
    <div x-show="openKirimModal" 
         x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
         x-cloak>
        <div @click.away="openKirimModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left border border-gray-100">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                        <i class="fas fa-paper-plane text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Kirim Surat ke Karyawan</h3>
                        <p class="text-xs text-gray-500">Pilih karyawan/pegawai yang dituju</p>
                    </div>
                </div>
                <button @click="openKirimModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <form action="{{ route('admin.surat.masuk.kirim', $surat->id) }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Pilih Karyawan Tujuan: <span class="text-red-500">*</span>
                    </label>
                    <select name="pegawai_id" class="w-full px-3.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition" required>
                        <option value="">-- Pilih Pegawai Penerima --</option>
                        @foreach(\App\Models\User::where('role', 'pegawai')->orderBy('name')->get() as $pegawai)
                            <option value="{{ $pegawai->id }}" {{ $surat->disposisi_user_id == $pegawai->id ? 'selected' : '' }}>
                                {{ $pegawai->name }} — {{ $pegawai->email }} ({{ $pegawai->bagian ?? 'Pegawai' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-500 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle text-emerald-500"></i>
                        Pilihan menampilkan Nama, Email, dan Bagian pegawai.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <button @click="openKirimModal = false" type="button" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md">
                        <i class="fas fa-paper-plane mr-1.5"></i> Kirim Surat
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
