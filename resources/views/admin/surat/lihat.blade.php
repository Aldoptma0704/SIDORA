@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

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
                        'rejected' => ['bg-red-100 text-red-800', 'fas fa-times-circle']
                    ];
                    $config = $statusConfig[$surat->status] ?? ['bg-gray-100 text-gray-800', 'fas fa-question-circle'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config[0] }}">
                    <i class="{{ $config[1] }} mr-2"></i>
                    {{ ucfirst($surat->status) }}
                </span>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button onclick="printLetter()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
                
                <a href="{{ route('surat.download', $surat->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-download mr-2"></i>
                    Download PDF
                </a>

                @if($user->role === 'admin' || $user->id === $surat->user_id)
                    <a href="{{ route('surats.edit', $surat->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
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
                            @if (isset($surat->pengirim->signature) && $surat->pengirim->signature)
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

    <!-- Letter Information Sidebar -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Letter Details -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                Detail Surat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nomor Surat</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $surat->nomor_surat ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Jenis Surat</label>
                    <p class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $surat->jenis) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tanggal Dibuat</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $surat->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $surat->updated_at->format('d M Y H:i') }}</p>
                </div>
                @if(isset($surat->user))
                <div>
                    <label class="block text-sm font-medium text-gray-500">Dibuat Oleh</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $surat->user->name }}</p>
                </div>
                @endif
                @if(isset($surat->disposisi))
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Disposisi</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $surat->disposisi }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions & Tools -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tools mr-2 text-gray-600"></i>
                    Aksi Cepat
                </h3>
                <div class="space-y-3">
                    <button onclick="copyShareLink()" class="w-full flex items-center px-3 py-2 text-sm text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-link mr-3"></i>
                        Copy Link
                    </button>
                    <button onclick="emailLetter()" class="w-full flex items-center px-3 py-2 text-sm text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-envelope mr-3"></i>
                        Kirim Email
                    </button>
                    <button onclick="archiveLetter()" class="w-full flex items-center px-3 py-2 text-sm text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-archive mr-3"></i>
                        Arsipkan
                    </button>
                </div>
            </div>

            <!-- History -->
            @if(isset($surat->history))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-history mr-2 text-gray-600"></i>
                    Riwayat
                </h3>
                <div class="space-y-3">
                    <!-- Add history items here -->
                    <div class="text-sm text-gray-500">
                        <p>Riwayat perubahan akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>
            @endif
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
    const printWindow = window.open('', '_blank');
    const letterContent = document.getElementById('letterContent').innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print - {{ $surat->judul }}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .prose { max-width: none; }
                @media print {
                    body { margin: 0; padding: 15px; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div style="max-width: 21cm; margin: 0 auto;">
                ${letterContent}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
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