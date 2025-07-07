@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow" x-data="{
    quill: null,
    init() {
        this.initQuill();
    },
    initQuill() {
        if (this.quill) return;
        this.quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{'list': 'ordered'}, {'list': 'bullet'}],
                    ['clean']
                ]
            }
        });

        // Ambil isi lama dari hidden input (HTML format)
        const isiInput = document.getElementById('isi');
        const raw = isiInput.dataset.raw || '';
        this.quill.root.innerHTML = raw;

        document.querySelector('form').addEventListener('submit', () => {
            isiInput.value = this.quill.root.innerHTML;
        });
    }
}" x-init="init()">
    <h2 class="text-xl font-bold mb-4 text-gray-700">[TEST] Edit Surat Keluar</h2>

    <form action="#" method="POST">
        @csrf
        {{-- Hidden input untuk simpan isi surat --}}
        <input type="hidden" name="isi" id="isi" data-raw="{!! '<p>Isi surat sebelumnya...</p>' !!}">

        <div class="mb-4">
            <label for="nomor_surat" class="block text-sm font-medium text-gray-700">Nomor Surat</label>
            <input type="text" id="nomor_surat" name="nomor_surat" class="w-full border p-2 rounded" value="123/TEST/VII/2025">
        </div>

        <div class="mb-4">
            <label for="editor" class="block text-sm font-medium text-gray-700">Isi Surat</label>
            <div id="editor" class="bg-white border p-2 rounded min-h-[200px]"></div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Quill CSS & JS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endpush
