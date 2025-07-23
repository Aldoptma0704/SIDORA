@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow"
     x-data="suratForm()">

    <h2 class="text-xl font-semibold mb-6 text-gray-700">Buat Surat Balasan</h2>

    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p class="font-bold">Terjadi Kesalahan Validasi</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form action="{{ route('pimpinan.surat-balasan.simpan') }}" method="POST">
    @csrf
    <input type="hidden" name="surat_asal_id" value="{{ $suratAsal->id ?? '' }}">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jenis Surat</label>
            <select name="jenis" x-model="jenis" class="w-full mt-1 p-2 border-2 border-gray-300 rounded shadow-sm" required>
                {{-- Opsi default yang dinonaktifkan --}}
                <option value="" disabled>-- Pilih Jenis Surat --</option>
                <option value="keluar_full">Surat Keluar (Dengan Kop Surat)</option>
                <option value="keluar">Surat Keluar (Template Standar)</option>
            </select>
        </div>

        {{-- Seluruh form di bawah ini hanya akan muncul jika 'jenis' sudah dipilih --}}
        <div x-show="jenis" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0">

            {{-- KOP SURAT (Hanya untuk 'keluar_full') --}}
            <div x-show="jenis === 'keluar_full'" x-transition class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Header Surat</p>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Logo Instansi</label>
                    <input type="file" name="logo_instansi_file" accept="image/*" class="w-full mt-1 p-2 border rounded shadow-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Instansi</label>
                        <div id="editor_nama_instansi" class="w-full border p-2 min-h-[60px]">{!! old('nama_instansi') !!}</div>
                        <input type="hidden" name="nama_instansi" value="{{ old('nama_instansi') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. Telepon / Kontak</label>
                        <div id="editor_kontak_instansi" class="w-full border p-2 min-h-[60px]">{!! old('kontak_instansi') !!}</div>
                        <input type="hidden" name="kontak_instansi" value="{{ old('kontak_instansi') }}">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Alamat Instansi</label>
                    <div id="editor_alamat_instansi" class="w-full border p-2 min-h-[60px]">{!! old('alamat_instansi') !!}</div>
                    <input type="hidden" name="alamat_instansi" value="{{ old('alamat_instansi') }}">
                </div>
            </div>

            {{-- ISI SURAT (Bagian ini umum untuk semua jenis surat keluar) --}}
            <div class="border-t border-gray-200 pt-4 mt-4">
                <p class="text-lg font-semibold text-gray-600 mb-4">Detail Surat Keluar</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sifat</label>
                        <input type="text" name="sifat" value="{{ old('sifat', 'Biasa') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lampiran</label>
                        <input type="text" name="lampiran" value="{{ old('lampiran', '-') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hal (Perihal)</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" class="w-full mt-1 p-2 border rounded shadow-sm">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Tujuan Surat</label>
                    <textarea name="tujuan" rows="3" class="w-full mt-1 p-2 border rounded shadow-sm">{{ old('tujuan') }}</textarea>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Isi Surat</label>
                    <div id="editor_isi" class="w-full border p-2 min-h-[200px]">{!! old('isi') !!}</div>
                    <input type="hidden" name="isi" id="isi_hidden" value="{{ old('isi') }}">
                </div>
            </div>

            @if(isset($pimpinan))
                <div class="mb-4 border p-3 bg-gray-50 rounded">
                    <strong>Penandatangan Otomatis:</strong>
                    <p><strong>Nama:</strong> {{ $pimpinan->nama_lengkap ?? 'Belum diisi' }}</p>
                    <p><strong>Jabatan:</strong> {{ $pimpinan->jabatan ?? 'Belum diisi' }}</p>
                    <p><strong>Pangkat & NIP:</strong> {{ $pimpinan->pangkat_nip ?? 'Belum diisi' }}</p>

                    @if($pimpinan->ttd)
                        <p><strong>Tanda Tangan:</strong><br>
                        <img src="{{ asset('storage/ttd/' . $pimpinan->ttd) }}" alt="Tanda Tangan" style="height: 100px;"></p>
                    @endif
                </div>
            @endif


            <div class="flex justify-end mt-6">
                <a href="{}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Surat</button>
            </div>
        </div>
    </form>
</div>

<script>
    function suratForm() {
        return {
            // Nilai awal 'jenis' adalah kosong, atau nilai lama jika ada error validasi
            jenis: '{{ old('jenis', '') }}',
            editorsInitialized: {
                isi: false,
                header: false
            },
            
            init() {
                // Jika halaman dimuat dengan nilai 'jenis' (karena error validasi),
                // langsung inisialisasi editor yang sesuai.
                if (this.jenis) {
                    this.initAllEditors();
                }

                // Awasi perubahan pada dropdown 'jenis'
                this.$watch('jenis', () => {
                    this.initAllEditors();
                });
            },

            initAllEditors() {
                // Jangan lakukan apa-apa jika belum ada jenis yang dipilih
                if (!this.jenis) {
                    return;
                }

                this.$nextTick(() => {
                    // Inisialisasi editor isi surat (hanya sekali)
                    if (!this.editorsInitialized.isi) {
                        this.createEditor('#editor_isi', '#isi_hidden');
                        this.editorsInitialized.isi = true;
                    }

                    // Inisialisasi editor header jika jenisnya 'keluar_full' (hanya sekali)
                    if (this.jenis === 'keluar_full' && !this.editorsInitialized.header) {
                        this.createEditor('#editor_nama_instansi', 'input[name=nama_instansi]');
                        this.createEditor('#editor_kontak_instansi', 'input[name=kontak_instansi]');
                        this.createEditor('#editor_alamat_instansi', 'input[name=alamat_instansi]');
                        this.editorsInitialized.header = true;
                    }
                });
            },

            // Fungsi pembantu untuk membuat instance CKEditor
            createEditor(editorSelector, hiddenInputSelector) {
                const element = document.querySelector(editorSelector);
                if (!element) return;

                // Menggunakan fungsi initCkeditor global Anda
                window.initCkeditor(editorSelector, hiddenInputSelector);
            }
        }
    }
</script>
@endsection
