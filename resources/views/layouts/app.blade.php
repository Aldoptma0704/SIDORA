<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIDORA - Sistem Informasi Dokumen Resmi')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Free 6.4.0 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    
    <!-- Alpine.js 3.x -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Quill 2.0.2 Rich Text Editor (Cepat, Stabil, Mendukung Rata Kiri/Tengah/Kanan/Justify, Indent/Tab, Spasi, & Poin) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        [x-cloak] { display: none !important; }

        .sidebar-link {
            transition: all 0.15s ease-in-out;
        }

        .ql-editor table {
            border-collapse: collapse;
            width: 100%;
        }

        .ql-editor table,
        .ql-editor th,
        .ql-editor td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }

        .ql-editor th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        /* Perataan Teks (Alignment) Standar CKEditor & Quill */
        .text-align-left, .ql-align-left, .ck-align-left, [style*="text-align: left"], [style*="text-align:left"] {
            text-align: left !important;
        }
        .text-align-center, .ql-align-center, .ck-align-center, [style*="text-align: center"], [style*="text-align:center"] {
            text-align: center !important;
        }
        .text-align-right, .ql-align-right, .ck-align-right, [style*="text-align: right"], [style*="text-align:right"] {
            text-align: right !important;
        }
        .text-align-justify, .ql-align-justify, .ck-align-justify, [style*="text-align: justify"], [style*="text-align:justify"] {
            text-align: justify !important;
        }

        /* Header Kop Surat Rapi & Tanpa Spasi Berlebih */
        .kop-header-text {
            white-space: normal !important;
            text-align: center !important;
            line-height: 1.25 !important;
        }
        .kop-header-text .kop-nama p,
        .kop-header-text .kop-nama h1,
        .kop-header-text .kop-nama h2,
        .kop-header-text .kop-nama div,
        .kop-header-text .kop-nama span {
            margin: 0 0 2px 0 !important;
            padding: 0 !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            white-space: normal !important;
            text-align: center !important;
        }
        .kop-header-text p {
            white-space: normal !important;
        }

        /* Spasi Paragraf Standar Naskah Dinas */
        .ql-editor p,
        .ck-content p {
            margin-top: 0;
            margin-bottom: 0.25rem;
            line-height: 1.35;
        }

        /* Indentasi (Tab/Spasi Paragraf) */
        .ck-indent-1, .ql-indent-1 { margin-left: 1.5rem !important; }
        .ck-indent-2, .ql-indent-2 { margin-left: 3rem !important; }
        .ck-indent-3, .ql-indent-3 { margin-left: 4.5rem !important; }
        .ck-indent-4, .ql-indent-4 { margin-left: 6rem !important; }

        /* Jarak Poin & Paragraf */
        .ql-editor ol,
        .ck-content ol {
            list-style-type: decimal !important;
            margin-left: 1.5rem !important;
            padding-left: 0.25rem !important;
            margin-top: 0.2rem !important;
            margin-bottom: 0.35rem !important;
        }

        .ql-editor ul,
        .ck-content ul {
            list-style-type: disc !important;
            margin-left: 1.5rem !important;
            padding-left: 0.25rem !important;
            margin-top: 0.2rem !important;
            margin-bottom: 0.35rem !important;
        }

        .ql-editor li,
        .ck-content li {
            margin-bottom: 0.15rem !important;
            line-height: 1.35 !important;
            display: list-item !important;
        }

        /* Memisahkan paragraf berikutnya agar tidak menempel / tidak dianggap bagian poin terakhir */
        .ql-editor ol + p,
        .ql-editor ul + p,
        .ck-content ol + p,
        .ck-content ul + p {
            margin-top: 0.5rem !important;
            clear: both !important;
            display: block !important;
        }

        /* Styling Quill Editor agar tampak modern dan menyatu dengan form */
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: #e5e7eb !important;
            background-color: #f9fafb;
            padding: 0.65rem 0.85rem !important;
            font-family: inherit;
        }

        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: #e5e7eb !important;
            background-color: #ffffff;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .ql-editor {
            min-height: 250px;
            padding: 1.25rem !important;
            line-height: 1.35;
            color: #1f2937;
            cursor: text !important;
        }

        .ql-editor:focus {
            outline: none !important;
        }

        .ql-editor.ql-blank::before {
            font-style: normal !important;
            color: #9ca3af !important;
            left: 1.25rem !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col text-gray-800 antialiased" x-data="{ mobileSidebarOpen: false }">
    @auth
        <!-- Header Atas (Tinggi 64px) -->
        @include('layouts.partials.header')

        <div class="flex flex-1 min-h-0">
            <!-- Overlay Backdrop Mobile Drawer -->
            <div x-show="mobileSidebarOpen" 
                 @click="mobileSidebarOpen = false" 
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 md:hidden" 
                 x-cloak></div>

            <!-- Sidebar Dinamis Berdasarkan Role -->
            @php
                $isAdmin = Auth::user()->role === 'admin';
                $sidebarStyle = $isAdmin ? 'bg-slate-900 border-r border-slate-800 text-slate-300' : 'bg-white border-r border-gray-200 text-gray-700';
            @endphp
            <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                   class="w-64 fixed top-16 bottom-0 left-0 z-40 md:z-30 transition-transform duration-200 ease-in-out overflow-y-auto px-4 py-5 {{ $sidebarStyle }} shadow-sm">
                @if (Auth::user()->role === 'admin')
                    @include('layouts.partials.sidebar-admin')
                @elseif (Auth::user()->role === 'pegawai')
                    @include('layouts.partials.sidebar-pegawai')
                @elseif (Auth::user()->role === 'pimpinan')
                    @include('layouts.partials.sidebar-pimpinan')
                @endif
            </aside>

            <!-- Container Konten Utama & Footer -->
            <div class="flex-1 flex flex-col md:pl-64 pt-16 min-h-screen w-full transition-all duration-200">
                <!-- Konten Halaman -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @yield('content')
                </main>

                <!-- Footer Satu Baris Ringan -->
                @include('layouts.partials.footer')
            </div>
        </div>
    @else
        <!-- Konten Tamu (Guest) -->
        <main class="flex-1">
            @yield('content')
        </main>
    @endauth

    <!-- Helper Inisialisasi Rich Text Editor (Quill.js - Cepat, Stabil & Tanpa Masalah Lisensi) -->
    <script>
        window.initCkeditor = function (editorSelector, hiddenInputSelector) {
            const targetElement = document.querySelector(editorSelector);
            const hiddenInput = document.querySelector(hiddenInputSelector);

            if (!targetElement) return;

            // Jika instance sudah terpasang, jangan inisialisasi ulang
            if (targetElement.quillInstance) {
                return;
            }

            if (typeof Quill === 'undefined') {
                console.error('Library Quill tidak ditemukan.');
                return;
            }

            // Cek apakah ini editor untuk input header kecil atau isi surat utama
            const isSmallHeader = editorSelector.includes('nama_instansi') || 
                                  editorSelector.includes('kontak_instansi') || 
                                  editorSelector.includes('alamat_instansi');

            let toolbarOptions;
            if (isSmallHeader) {
                toolbarOptions = [
                    ['bold', 'italic'],
                    [{ 'align': '' }, { 'align': 'center' }, { 'align': 'right' }],
                    ['clean']
                ];
            } else {
                toolbarOptions = [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'align': '' }, { 'align': 'center' }, { 'align': 'right' }, { 'align': 'justify' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['clean']
                ];
            }

            const quill = new Quill(targetElement, {
                theme: 'snow',
                placeholder: isSmallHeader ? 'Ketik data di sini...' : 'Ketik isi surat dinas di sini...',
                modules: {
                    toolbar: toolbarOptions,
                    keyboard: {
                        bindings: {
                            tab: {
                                key: 9,
                                handler: function (range, context) {
                                    this.quill.format('indent', '+1');
                                    return false;
                                }
                            },
                            shiftTab: {
                                key: 9,
                                shiftKey: true,
                                handler: function (range, context) {
                                    this.quill.format('indent', '-1');
                                    return false;
                                }
                            }
                        }
                    }
                }
            });

            targetElement.quillInstance = quill;
            targetElement.ckeditorInstance = quill; // Kompatibilitas referensi lama

            // Sinkronisasi data awal jika hidden input sudah ada nilainya (misal saat edit)
            if (hiddenInput && hiddenInput.value && hiddenInput.value.trim() !== '') {
                quill.root.innerHTML = hiddenInput.value;
            } else if (targetElement.innerHTML && targetElement.innerHTML.trim() !== '') {
                if (hiddenInput) hiddenInput.value = targetElement.innerHTML;
            }

            // Sinkronisasi data saat user mengetik
            quill.on('text-change', function () {
                if (hiddenInput) {
                    const html = quill.root.innerHTML;
                    hiddenInput.value = (html === '<p><br></p>') ? '' : html;
                }
            });
        };
    </script>
    @stack('scripts')
</body>
</html>
