@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Surat Masuk dari Pimpinan</h1>
                            <p class="text-sm text-gray-600 mt-1">Kelola dan pantau surat yang dikirim oleh pimpinan</p>
                        </div>
                    </div>
                    
                    <!-- Stats Badge -->
                    <div class="hidden sm:flex items-center space-x-4">
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-indigo-800">
                                    {{ $surats->count() }} Surat
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($surats->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="text-center py-16 px-6">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Surat Masuk</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Tidak ada surat yang dikirim oleh pimpinan saat ini. Surat baru akan muncul di sini ketika pimpinan mengirimkan surat.
                    </p>
                    <div class="flex items-center justify-center space-x-4">
                        <button onclick="window.location.reload()" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh Halaman
                        </button>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Filter and Search Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="searchInput" placeholder="Cari surat..." 
                                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <select id="sortSelect" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="newest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="title">Judul A-Z</option>
                        </select>
                    </div>
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold" id="totalCount">{{ $surats->count() }}</span> surat
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Judul Surat</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                        </svg>
                                        <span>No. Surat</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Pengirim</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0 4 4 0 018 0zm0 0a4 4 0 118 0 4 4 0 01-8 0z"></path>
                                        </svg>
                                        <span>Tanggal</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                            @foreach($surats as $surat)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 table-row" data-title="{{ strtolower($surat->judul) }}" data-date="{{ $surat->updated_at->timestamp }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $surat->judul }}</p>
                                                <p class="text-xs text-gray-500 mt-1">ID: #{{ $surat->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($surat->nomor_surat)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $surat->nomor_surat }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm italic">Tidak ada nomor</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $surat->pengirim->name ?? 'Tidak diketahui' }}
                                                </p>
                                                @if($surat->pengirim && $surat->pengirim->bagian)
                                                    <p class="text-xs text-gray-500">{{ $surat->pengirim->bagian }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0 4 4 0 018 0zm0 0a4 4 0 118 0 4 4 0 01-8 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $surat->updated_at->format('d M Y') }}</p>
                                                <p class="text-xs text-gray-500">{{ $surat->updated_at->format('H:i') }} WIB</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('admin.surat.dari-pimpinan.lihat', $surat->id) }}"
                                               class="inline-flex items-center px-3 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:border-indigo-300 transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Menampilkan <span id="visibleCount">{{ $surats->count() }}</span> dari <span id="totalSurats">{{ $surats->count() }}</span> surat
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="exportTable()" 
                                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Custom Styles -->
<style>
    .table-row.hidden {
        display: none;
    }
    
    @media (max-width: 640px) {
        .max-w-7xl {
            max-width: 100%;
            margin: 0 1rem;
        }
        
        table {
            font-size: 0.75rem;
        }
        
        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
</style>

<!-- JavaScript for Enhanced Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const tableBody = document.getElementById('tableBody');
    const totalCount = document.getElementById('totalCount');
    const visibleCount = document.getElementById('visibleCount');
    const rows = Array.from(tableBody.querySelectorAll('.table-row'));
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterAndSort();
    });
    
    // Sort functionality
    sortSelect.addEventListener('change', function() {
        filterAndSort();
    });
    
    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        const sortBy = sortSelect.value;
        
        // Filter rows
        let visibleRows = rows.filter(row => {
            const title = row.dataset.title;
            const isVisible = title.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            return isVisible;
        });
        
        // Sort visible rows
        visibleRows.sort((a, b) => {
            switch(sortBy) {
                case 'newest':
                    return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                case 'oldest':
                    return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                case 'title':
                    return a.dataset.title.localeCompare(b.dataset.title);
                default:
                    return 0;
            }
        });
        
        // Reorder DOM elements
        visibleRows.forEach(row => {
            tableBody.appendChild(row);
        });
        
        // Update counters
        visibleCount.textContent = visibleRows.length;
        
        // Show "no results" message if no visible rows
        const noResultsRow = document.getElementById('noResultsRow');
        if (visibleRows.length === 0 && !noResultsRow) {
            const noResults = document.createElement('tr');
            noResults.id = 'noResultsRow';
            noResults.innerHTML = `
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <p class="text-lg font-medium mb-2">Tidak ada surat ditemukan</p>
                        <p class="text-sm">Coba ubah kata kunci pencarian Anda</p>
                    </div>
                </td>
            `;
            tableBody.appendChild(noResults);
        } else if (visibleRows.length > 0 && noResultsRow) {
            noResultsRow.remove();
        }
    }
    
    // Export functionality
    window.exportTable = function() {
        const visibleRows = rows.filter(row => row.style.display !== 'none');
        let csvContent = "Judul,Nomor Surat,Pengirim,Tanggal\n";
        
        visibleRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const title = cells[0].querySelector('p').textContent.trim();
            const nomor = cells[1].textContent.trim().replace(/\n/g, ' ');
            const pengirim = cells[2].querySelector('p').textContent.trim();
            const tanggal = cells[3].querySelector('p').textContent.trim();
            
            csvContent += `"${title}","${nomor}","${pengirim}","${tanggal}"\n`;
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'surat_masuk_pimpinan_' + new Date().toISOString().slice(0,10) + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
    
    // Auto refresh every 30 seconds
    setInterval(() => {
        // Optional: Add a subtle notification for new data
        console.log('Auto refresh check...');
    }, 30000);
});
</script>
@endsection