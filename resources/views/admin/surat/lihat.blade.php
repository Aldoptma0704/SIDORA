@php
    use Illuminate\Support\Facades\Auth;
    use SimpleSoftwareIO\QrCode\Facades\QrCode; // Pastikan ini di-import
    $user = Auth::user(); // User yang sedang login
@endphp

@if (isset($is_pdf) && $is_pdf)
{{-- ================================================================= --}}
{{-- Bagian Rendering PDF/Print Friendly (HTML/CSS murni untuk output konsisten) --}}
{{-- ================================================================= --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Preview Surat - {{ $surat->judul }}</title>
    <style>
        /* CSS khusus untuk DomPDF dan print browser */
        @page {
            margin: 20mm; /* Atur margin halaman A4 */
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
        }
        .header-section, .info-section, .content-section, .signature-section, .qr-section {
            page-break-inside: avoid; /* Hindari pemisahan halaman di dalam bagian ini */
        }
        .header-logo-container {
            width: 15%;
            text-align: left;
            vertical-align: middle;
            padding-right: 15px;
        }
        .header-text-container {
            width: 85%;
            text-align: center;
            vertical-align: middle;
        }
        .header-text h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .header-text h2 {
            font-size: 12pt;
            font-weight: 600;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .header-text p {
            font-size: 9pt;
            margin: 0;
        }
        .hr-line {
            border-top: 3px solid black;
            margin-top: 16px;
            margin-bottom: 16px;
        }
        .letter-info td {
            padding-right: 8px;
            white-space: nowrap;
        }
        .signature-block {
            margin-top: 40px;
            text-align: right;
        }
        .signature-block p {
            margin: 0;
        }
        .signature-image {
            height: 80px;
            width: 150px; /* Lebar eksplisit untuk tanda tangan */
            display: block;
            margin-left: auto;
            margin-right: 0;
        }
        /* Gaya untuk konten dari Quill editor */
        .ql-editor p {
            margin-bottom: 1em;
        }
        .ql-editor ol, .ql-editor ul {
            margin-left: 1.5em;
            margin-bottom: 1em;
        }
        .qr-code {
            display: block;
            margin-top: 10px;
            margin-left: auto; /* Untuk rata kanan */
            margin-right: 0;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
            <tr>
                <td class="header-logo-container">
                    <img src="data:image/png;base64,{{ $base64Logo }}" alt="Logo" style="height: 90px; width: auto; max-width: 90px;">
                </td>
                <td class="header-text-container">
                    <h1 class="header-text">PEMERINTAH PROVINSI LAMPUNG</h1>
                    <h2 class="header-text">DINAS TENAGA KERJA</h2>
                    <p class="header-text">
                        Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856 <br>
                        Laman: <a href="https://disnaker.lampungprov.go.id" style="color: #2563eb; text-decoration: underline;">https://disnaker.lampungprov.go.id</a> |
                        Pos-el: <a href="mailto:lampungnaker@gmail.com" style="color: #2563eb; text-decoration: underline;">lampungnaker@gmail.com</a>
                    </p>
                </td>
            </tr>
        </table>
        <hr class="hr-line">
    </div>

    @if ($surat->jenis === 'keluar_full')
        <div class="header-section">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
                <tr>
                    <td class="header-logo-container">
                        @if ($base64InstansiLogo)
                            <img src="data:image/png;base64,{{ $base64InstansiLogo }}" alt="Logo Instansi" style="height: 90px; width: auto; max-width: 90px;">
                        @endif
                    </td>
                    <td class="header-text-container">
                        <h1 class="header-text">{!! $surat->nama_instansi !!}</h1>
                        <p class="header-text">{!! $surat->kontak_instansi !!}</p>
                        <p class="header-text">{!! $surat->alamat_instansi !!}</p>
                    </td>
                </tr>
            </table>
            <hr class="hr-line">
        </div>
    @endif

    <div class="info-section">
        <p style="text-align: right; margin-bottom: 16px;">Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

        <table class="letter-info" style="margin-bottom: 16px;">
            <tr><td style="width: 100px;">Nomor</td><td>: {{ $surat->nomor_surat ?? '-' }}</td></tr>
            <tr><td>Sifat</td><td>: {{ $surat->sifat ?? '-' }}</td></tr>
            <tr><td>Lampiran</td><td>: {{ $surat->lampiran ?? '-' }}</td></tr>
            <tr><td>Hal</td><td>: {{ $surat->judul }}</td></tr>
        </table>

        <p style="margin-bottom: 8px;">Yth. {{ $surat->tujuan ?? '...........................................' }}</p>
        <p style="margin-bottom: 16px;">Di — {{ strtoupper('Bandar Lampung') }}</p>

        <p style="margin-bottom: 16px;">Sehubungan dengan hal tersebut, kami sampaikan bahwa:</p>
    </div>

    <div class="content-section">
        <div class="ql-editor" style="margin-bottom: 16px;">{!! $surat->isi !!}</div>
        <p style="margin-top: 24px;">Demikian atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
    </div>

    <div class="signature-section">
        <div class="signature-block">
            <p>{{ $surat->$surat->pengirim->position ?? 'Jabatan' }}</p>
            <div style="margin-top: 16px;">
                @if (in_array($surat->status, ['disetujui', 'dikirim', 'draft_pimpinan']))
                    <div style="margin-bottom: 8px;">
                        <img src="{{ asset('storage/signatures/' . $surat->pengirim->signature) }}" alt="Tanda Tangan" class="w-32 h-auto">
                    </div>
                @else
                    <div style="font-style: italic; color: #6b7280;">Belum ditandatangani</div>
                @endif
                <div>
                    <p style="font-weight: bold;">{{ $surat->pengirim->name ?? 'Nama Pejabat' }}</p>
                    <p>NIP. {{ $surat->pengirim->nip ?? '..........' }}</p>
                </div>
            </div>
        </div>
    </div>


</body>
</html>

@else
{{-- ================================================================= --}}
{{-- Bagian Tampilan Web (Menggunakan layouts.app dan Tailwind CSS) --}}
{{-- ================================================================= --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-file-alt mr-3 text-blue-600"></i>
                Preview Surat
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $surat->judul }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
            <!-- Status Badge -->
            @if(isset($surat->status))
                @php
                    $statusConfig = [
                        'draft' => ['bg-gray-100 text-gray-800', 'fas fa-edit'],
                        'pending' => ['bg-yellow-100 text-yellow-800', 'fas fa-clock'],
                        'approved' => ['bg-green-100 text-green-800', 'fas fa-check-circle'],
                        'rejected' => ['bg-red-100 text-red-800', 'fas fa-times-circle'],
                        'dikirim' => ['bg-blue-100 text-blue-800', 'fas fa-paper-plane'], // Tambahkan status 'dikirim'
                        'draft_pimpinan' => ['bg-purple-100 text-purple-800', 'fas fa-file-alt'], // Tambahkan status 'draft_pimpinan'
                    ];
                    $config = $statusConfig[$surat->status] ?? ['bg-gray-100 text-gray-800', 'fas fa-question-circle'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config[0] }}">
                    <i class="{{ $config[1] }} mr-2"></i>
                    {{ ucfirst(str_replace('_', ' ', $surat->status)) }} {{-- Format status untuk tampilan --}}
                </span>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button onclick="printLetter()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
                
                <a href="{{ route('admin.surat.download', $surat->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-download mr-2"></i>
                    Download PDF
                </a>

                @if($user->role === 'admin' || $user->id === $surat->user_id)
                    <a href="{{ route('admin.surat.dari-pimpinan.lihat', $surat->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                @endif

                <a href="{{ route('admin.surat.masuk') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Success Notification --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-600"></i>
            <div>
                <p class="font-semibold">Berhasil</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Letter Preview Container -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <!-- Toolbar -->
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Zoom:</span>
                <div class="flex items-center space-x-2">
                    <button onclick="zoomOut()" class="p-1 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <span id="zoomLevel" class="text-sm text-gray-600">100%</span>
                    <button onclick="zoomIn()" class="p-1 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="toggleFullscreen()" class="p-2 text-gray-500 hover:text-gray-700" title="Toggle Fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>

        <!-- Letter Content -->
        <div id="letterContainer" class="bg-gray-100 p-8">
            <div id="letterContent" class="bg-white p-8 mx-auto text-sm text-black leading-relaxed shadow-lg" style="max-width: 21cm; min-height: 29.7cm; transform-origin: top center; transition: transform 0.3s ease;">

                {{-- Header Logo & Agency Information --}}
                <div class="flex items-center justify-center mb-6">
                    <div class="shrink-0">
                        <img src="{{ asset('img/logo_lampung.png') }}" alt="Logo" class="h-20 w-auto">
                    </div>
                    <div class="ml-6 text-center">
                        <h1 class="text-lg font-bold uppercase tracking-wide">PEMERINTAH PROVINSI LAMPUNG</h1>
                        <h2 class="text-md font-semibold uppercase text-gray-800">DINAS TENAGA KERJA</h2>
                        <div class="text-xs text-gray-600 mt-2 space-y-1">
                            <p>Jl. Gatot Subroto No.28 Kotak Pos 78 Telp. (0721) 252065, Fax. 262856</p>
                            <p>
                                Laman: <a href="https://disnaker.lampungprov.go.id" class="text-blue-600 hover:underline" target="_blank">https://disnaker.lampungprov.go.id</a> |
                                Pos-el: <a href="mailto:lampungnaker@gmail.com" class="text-blue-600 hover:underline">lampungnaker@gmail.com</a>
                            </p>
                        </div>
                    </div>
                </div>
                <hr class="border-t-3 border-black my-6">
                
                {{-- Institution Header for Outgoing Letters --}}
                @if ($surat->jenis === 'keluar_full')
                    <div class="flex items-center justify-center mb-6">
                        @if ($surat->logo_instansi)
                            <div class="shrink-0">
                                <img src="{{ asset('storage/logo/' . $surat->logo_instansi) }}" alt="Logo Instansi" class="h-20 w-auto">
                            </div>
                        @endif
                        <div class="ml-6 text-center">
                            <h1 class="text-lg font-bold uppercase">{!! $surat->nama_instansi !!}</h1>
                            <div class="text-xs text-gray-600 mt-2">
                                <p>{!! $surat->kontak_instansi !!}</p>
                                <p>{!! $surat->alamat_instansi !!}</p>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t-3 border-black my-6">
                @endif

                {{-- Letter Date --}}
                <div class="text-right mb-6">
                    <p class="text-sm">Bandar Lampung, {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y') }}</p>
                </div>

                {{-- Letter Information --}}
                <div class="mb-6">
                    <table class="w-full text-sm">
                        <tr class="align-top">
                            <td class="w-20 py-1">Nomor</td>
                            <td class="w-4 py-1">:</td>
                            <td class="py-1 font-medium">{{ $surat->nomor_surat ?? '-' }}</td>
                        </tr>
                        <tr class="align-top">
                            <td class="py-1">Sifat</td>
                            <td class="py-1">:</td>
                            <td class="py-1">{{ $surat->sifat ?? '-' }}</td>
                        </tr>
                        <tr class="align-top">
                            <td class="py-1">Lampiran</td>
                            <td class="py-1">:</td>
                            <td class="py-1">{{ $surat->lampiran ?? '-' }}</td>
                        </tr>
                        <tr class="align-top">
                            <td class="py-1">Hal</td>
                            <td class="py-1">:</td>
                            <td class="py-1 font-medium">{{ $surat->judul }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Recipient --}}
                <div class="mb-6">
                    <p class="mb-2">Kepada Yth.</p>
                    <p class="mb-1 font-medium">{{ $surat->tujuan ?? '........................................' }}</p>
                    <p class="text-sm text-gray-600">di Tempat</p>
                </div>

                {{-- Letter Greeting --}}
                <p class="mb-6">Dengan hormat,</p>

                {{-- Letter Content --}}
                <div class="mb-8 prose prose-sm max-w-none">
                    <div class="ql-editor p-0">{!! $surat->isi !!}</div>
                </div>

                {{-- Letter Closing --}}
                <p class="mb-8">Demikian surat ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>

                {{-- Signature Section --}}
                <div class="flex justify-end">
                    <div class="text-center">
                        {{-- Position --}}
                        <p class="font-semibold mb-4">{{ $surat->pengirim->position ?? 'Kepala Dinas' }}</p>

                        {{-- Signature --}}
                        <div class="mb-4 flex justify-center">
                            @if (in_array($surat->status, ['disetujui', 'dikirim', 'draft_pimpinan']))
                                <img src="{{ asset('storage/signatures/' . $surat->pengirim->signature) }}" alt="Tanda Tangan" class="w-32 h-auto">
                            @else
                                <div class="w-32 h-16 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <span class="text-xs text-gray-400">Tanda tangan</span>
                                </div>
                            @endif
                        </div>

                        {{-- Name and NIP --}}
                        <div class="border-t border-gray-300 pt-2">
                            <p class="font-semibold">{{ $surat->pengirim->name ?? 'Nama Penandatangan' }}</p>
                            <p class="text-sm text-gray-600">NIP. {{ $surat->pengirim->nip ?? '199999999999999999' }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let currentZoom = 100;

        // Zoom functionality
        function zoomIn() {
            if (currentZoom < 200) {
                currentZoom += 10;
                updateZoom();
            }
        }

        function zoomOut() {
            if (currentZoom > 50) {
                currentZoom -= 10;
                updateZoom();
            }
        }

        function updateZoom() {
            const letterContent = document.getElementById('letterContent');
            const zoomLevel = document.getElementById('zoomLevel');
            
            letterContent.style.transform = `scale(${currentZoom / 100})`;
            zoomLevel.textContent = currentZoom + '%';
            
            // Adjust container height to prevent overlap
            const container = document.getElementById('letterContainer');
            container.style.paddingBottom = `${8 * (currentZoom / 100)}rem`;
        }

        // Fullscreen toggle
        function toggleFullscreen() {
            const container = document.getElementById('letterContainer');
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            
            if (!document.fullscreenElement) {
                container.requestFullscreen().then(() => {
                    icon.className = 'fas fa-compress';
                    container.classList.add('fixed', 'inset-0', 'z-50', 'overflow-auto');
                });
            } else {
                document.exitFullscreen().then(() => {
                    icon.className = 'fas fa-expand';
                    container.classList.remove('fixed', 'inset-0', 'z-50', 'overflow-auto');
                });
            }
        }

        // Print functionality
        function printLetter() {
            // Redirect to the same URL with is_pdf=true to get the print-friendly version
            const printUrl = window.location.href.includes('?') ? 
                             window.location.href + '&is_pdf=true' : 
                             window.location.href + '?is_pdf=true';
            
            const printWindow = window.open(printUrl, '_blank');
            printWindow.onload = function() {
                // Wait for the content to load, then print
                setTimeout(() => {
                    printWindow.focus();
                    printWindow.print();
                    // Optional: close the window after printing, but might not work in all browsers
                    // printWindow.close(); 
                }, 500); // Small delay to ensure rendering
            };
        }

        // Copy share link
        function copyShareLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showNotification('Link berhasil disalin ke clipboard', 'success');
            }).catch(() => {
                showNotification('Gagal menyalin link', 'error');
            });
        }

        // Email letter
        function emailLetter() {
            const subject = encodeURIComponent('Surat: {{ $surat->judul }}');
            const body = encodeURIComponent(`Berikut adalah link untuk melihat surat:\n\n${window.location.href}`);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        }

        // Archive letter
        function archiveLetter() {
            if (confirm('Apakah Anda yakin ingin mengarsipkan surat ini?')) {
                // Add archive functionality here
                showNotification('Surat berhasil diarsipkan', 'success');
            }
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Auto-hide success alert
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.bg-green-50');
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'p':
                        e.preventDefault();
                        printLetter();
                        break;
                    case '+':
                    case '=':
                        e.preventDefault();
                        zoomIn();
                        break;
                    case '-':
                        e.preventDefault();
                        zoomOut();
                        break;
                }
            }
            
            if (e.key === 'Escape' && document.fullscreenElement) {
                document.exitFullscreen();
            }
        });
    </script>
    @endsection
@endif
