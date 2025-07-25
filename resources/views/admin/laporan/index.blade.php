@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-chart-line mr-3 text-blue-600"></i>
                    Laporan Aktivitas Surat
                </h1>
                <p class="mt-2 text-sm text-gray-600">Monitor dan analisis aktivitas surat dalam sistem</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center space-x-4 text-sm">
                    <div class="bg-blue-50 px-3 py-1 rounded-full">
                        <i class="fas fa-file-alt text-blue-600 mr-1"></i>
                        <span class="text-blue-800 font-medium">Total: {{ $surats->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $statusCounts = $surats->groupBy('status')->map->count();
        $jenisCounts = $surats->groupBy('jenis')->map->count();
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Surat -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Surat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $surats->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Disetujui -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600">{{ $statusCounts['disetujui'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $statusCounts['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600">{{ $statusCounts['ditolak'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari judul surat atau pembuat..." 
                           class="pl-10 pr-4 py-2 w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Status Filter -->
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>

                <!-- Jenis Filter -->
                <select id="jenisFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisCounts->keys() as $jenis)
                        <option value="{{ $jenis }}">{{ ucfirst($jenis) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="flex items-center space-x-2">
                <input type="date" id="dateFrom" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <span class="text-gray-500">s/d</span>
                <input type="date" id="dateTo" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <button onclick="clearFilters()" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($surats->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(0)">
                                <div class="flex items-center space-x-1">
                                    <span>No</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(1)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-file-alt text-gray-400"></i>
                                    <span>Judul Surat</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(2)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-tag text-gray-400"></i>
                                    <span>Jenis</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(3)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-info-circle text-gray-400"></i>
                                    <span>Status</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(4)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-user text-gray-400"></i>
                                    <span>Dibuat Oleh</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(5)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    <span>Tanggal</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="reportTableBody">
                        @foreach ($surats as $index => $surat)
                            <tr class="hover:bg-gray-50 transition-colors duration-150" 
                                data-judul="{{ strtolower($surat->judul) }}" 
                                data-user="{{ strtolower($surat->user->name ?? '') }}" 
                                data-status="{{ strtolower($surat->status) }}" 
                                data-jenis="{{ strtolower($surat->jenis) }}"
                                data-date="{{ $surat->created_at->format('Y-m-d') }}">
                                
                                <!-- Number -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Title -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $surat->judul }}</div>
                                    @if($surat->deskripsi)
                                        <div class="text-sm text-gray-500 truncate max-w-xs" title="{{ $surat->deskripsi }}">
                                            {{ Str::limit($surat->deskripsi, 50) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $jenisColors = [
                                            'masuk' => 'bg-blue-100 text-blue-800',
                                            'keluar' => 'bg-purple-100 text-purple-800',
                                            'internal' => 'bg-indigo-100 text-indigo-800'
                                        ];
                                        $jenisIcons = [
                                            'masuk' => 'fas fa-arrow-down',
                                            'keluar' => 'fas fa-arrow-up',
                                            'internal' => 'fas fa-exchange-alt'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jenisColors[$surat->jenis] ?? 'bg-gray-100 text-gray-800' }}">
                                        <i class="{{ $jenisIcons[$surat->jenis] ?? 'fas fa-file' }} mr-1"></i>
                                        {{ ucfirst($surat->jenis) }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'disetujui' => ['bg-green-100 text-green-800', 'fas fa-check-circle'],
                                            'ditolak' => ['bg-red-100 text-red-800', 'fas fa-times-circle'],
                                            'pending' => ['bg-yellow-100 text-yellow-800', 'fas fa-clock']
                                        ];
                                        $config = $statusConfig[$surat->status] ?? ['bg-gray-100 text-gray-800', 'fas fa-question-circle'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }}">
                                        <i class="{{ $config[1] }} mr-1"></i>
                                        {{ ucfirst($surat->status) }}
                                    </span>
                                </td>

                                <!-- Creator -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($surat->user)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-xs">
                                                    {{ strtoupper(substr($surat->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $surat->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $surat->user->bagian ?? 'Tidak diketahui' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Tidak diketahui</span>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        <div>
                                            <div class="font-medium">{{ $surat->created_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $surat->created_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination placeholder -->
            @if(method_exists($surats, 'links'))
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $surats->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <i class="fas fa-file-alt text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data surat</h3>
                <p class="text-gray-500 mb-6">Data surat akan muncul di sini setelah ada aktivitas surat dalam sistem</p>
            </div>
        @endif
    </div>
</div>

<script>
// Search and Filter functionality
let originalData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Store original table data
    const rows = document.querySelectorAll('#reportTableBody tr');
    originalData = Array.from(rows).map(row => ({
        element: row,
        judul: row.dataset.judul,
        user: row.dataset.user,
        status: row.dataset.status,
        jenis: row.dataset.jenis,
        date: row.dataset.date
    }));

    // Add event listeners
    document.getElementById('searchInput').addEventListener('input', filterData);
    document.getElementById('statusFilter').addEventListener('change', filterData);
    document.getElementById('jenisFilter').addEventListener('change', filterData);
    document.getElementById('dateFrom').addEventListener('change', filterData);
    document.getElementById('dateTo').addEventListener('change', filterData);
});

function filterData() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const jenisFilter = document.getElementById('jenisFilter').value.toLowerCase();
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = '';

    let visibleCount = 0;
    originalData.forEach((item, index) => {
        const matchesSearch = !searchTerm || item.judul.includes(searchTerm) || item.user.includes(searchTerm);
        const matchesStatus = !statusFilter || item.status === statusFilter;
        const matchesJenis = !jenisFilter || item.jenis === jenisFilter;
        
        let matchesDate = true;
        if (dateFrom || dateTo) {
            const itemDate = new Date(item.date);
            if (dateFrom) matchesDate = matchesDate && itemDate >= new Date(dateFrom);
            if (dateTo) matchesDate = matchesDate && itemDate <= new Date(dateTo);
        }

        if (matchesSearch && matchesStatus && matchesJenis && matchesDate) {
            // Update row number
            const firstCell = item.element.querySelector('td');
            firstCell.textContent = ++visibleCount;
            tbody.appendChild(item.element);
        }
    });

    if (visibleCount === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-search text-3xl mb-2 block"></i>
                    Tidak ada data yang sesuai dengan filter
                </td>
            </tr>
        `;
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('jenisFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    filterData();
}

// Sort functionality
let sortDirection = {};

function sortTable(columnIndex) {
    const tbody = document.getElementById('reportTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
    
    const direction = sortDirection[columnIndex] === 'asc' ? 'desc' : 'asc';
    sortDirection[columnIndex] = direction;

    rows.sort((a, b) => {
        let aVal = a.children[columnIndex].textContent.trim();
        let bVal = b.children[columnIndex].textContent.trim();

        // Handle numeric sorting for first column
        if (columnIndex === 0) {
            aVal = parseInt(aVal);
            bVal = parseInt(bVal);
        }

        if (direction === 'asc') {
            return aVal > bVal ? 1 : -1;
        } else {
            return aVal < bVal ? 1 : -1;
        }
    });

    // Re-number rows and append to tbody
    tbody.innerHTML = '';
    rows.forEach((row, index) => {
        row.children[0].textContent = index + 1;
        tbody.appendChild(row);
    });
}

// Action functions
function viewDetail(id) {
    // Implement view detail functionality
    alert('Lihat detail surat ID: ' + id);
}

function downloadPDF(id) {
    // Implement PDF download functionality
    alert('Download PDF surat ID: ' + id);
}

function exportReport() {
    // Implement Excel export functionality
    alert('Export laporan ke Excel');
}
</script>
@endsection