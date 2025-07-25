@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-share-alt mr-3 text-indigo-600"></i>
                    Daftar Surat untuk Disposisi
                </h1>
                <p class="mt-2 text-sm text-gray-600">Kelola dan monitor disposisi surat yang memerlukan tindak lanjut</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalSurat = $surats->count();
        $disposisiCount = $surats->whereNotNull('disposisi')->where('disposisi', '!=', '')->count();
        $pendingCount = $totalSurat - $disposisiCount;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Surat -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Surat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSurat }}</p>
                </div>
            </div>
        </div>

        <!-- Sudah Disposisi -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sudah Disposisi</p>
                    <p class="text-2xl font-bold text-green-600">{{ $disposisiCount }}</p>
                </div>
            </div>
        </div>

        <!-- Belum Disposisi -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Belum Disposisi</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $pendingCount }}</p>
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
                    <input type="text" id="searchInput" placeholder="Cari judul surat..." 
                           class="pl-10 pr-4 py-2 w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Status Filter -->
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="sudah">Sudah Disposisi</option>
                    <option value="belum">Belum Disposisi</option>
                </select>
            </div>

            <button onclick="clearFilters()" class="inline-flex items-center px-3 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Clear Filter
            </button>
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
                                    <span>Detail Surat</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable(2)">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-share-alt text-gray-400"></i>
                                    <span>Status Disposisi</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    <span>Tanggal</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="disposisiTableBody">
                        @foreach ($surats as $index => $surat)
                            @php
                                $hasDisposisi = !empty($surat->disposisi);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-150" 
                                data-judul="{{ strtolower($surat->judul) }}" 
                                data-status="{{ $hasDisposisi ? 'sudah' : 'belum' }}">
                                
                                <!-- Number -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Letter Details -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-file-alt text-white"></i>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 mb-1">
                                                {{ $surat->judul }}
                                            </div>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                @if(isset($surat->nomor_surat))
                                                    <span class="flex items-center">
                                                        <i class="fas fa-hashtag mr-1"></i>
                                                        {{ $surat->nomor_surat }}
                                                    </span>
                                                @endif
                                                @if(isset($surat->jenis))
                                                    <span class="flex items-center">
                                                        <i class="fas fa-tag mr-1"></i>
                                                        {{ ucfirst($surat->jenis) }}
                                                    </span>
                                                @endif
                                                @if(isset($surat->user))
                                                    <span class="flex items-center">
                                                        <i class="fas fa-user mr-1"></i>
                                                        {{ $surat->user->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Disposition Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hasDisposisi)
                                        <div class="space-y-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Sudah Disposisi
                                            </span>
                                            <div class="bg-gray-50 p-3 rounded-lg border-l-4 border-green-500">
                                                <p class="text-sm text-gray-700 font-medium mb-1">Disposisi:</p>
                                                <p class="text-sm text-gray-600">{{ $surat->disposisi }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Belum Disposisi
                                        </span>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        <div>
                                            @if(isset($surat->created_at))
                                                <div class="font-medium">{{ $surat->created_at->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $surat->created_at->format('H:i') }}</div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- View Detail -->
                                        <button onclick="viewDetail({{ $surat->id }})" 
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors duration-200"
                                                title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if($hasDisposisi)
                                            <!-- Edit Disposition -->
                                            <button onclick="editDisposisi({{ $surat->id }})" 
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-600 bg-yellow-50 hover:bg-yellow-100 rounded-md transition-colors duration-200"
                                                    title="Edit Disposisi">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @else
                                            <!-- Add Disposition -->
                                            <button onclick="addDisposisi({{ $surat->id }})" 
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 rounded-md transition-colors duration-200"
                                                    title="Tambah Disposisi">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @endif

                                        <!-- Print -->
                                        <button onclick="printDisposisi({{ $surat->id }})" 
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-md transition-colors duration-200"
                                                title="Print Disposisi">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
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
                    <i class="fas fa-share-alt text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada surat untuk disposisi</h3>
                <p class="text-gray-500 mb-6">Surat yang memerlukan disposisi akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>

<!-- Disposition Modal -->
<div id="disposisiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Tambah Disposisi</h3>
                <button onclick="closeDisposisiModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="disposisiForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Disposisi:</label>
                    <textarea id="disposisiText" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Masukkan instruksi disposisi..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDisposisiModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition-colors duration-200">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Search and Filter functionality
let originalData = [];
let currentSuratId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Store original table data
    const rows = document.querySelectorAll('#disposisiTableBody tr');
    originalData = Array.from(rows).map(row => ({
        element: row,
        judul: row.dataset.judul,
        status: row.dataset.status
    }));

    // Add event listeners
    document.getElementById('searchInput').addEventListener('input', filterData);
    document.getElementById('statusFilter').addEventListener('change', filterData);
});

function filterData() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();

    const tbody = document.getElementById('disposisiTableBody');
    tbody.innerHTML = '';

    let visibleCount = 0;
    originalData.forEach((item, index) => {
        const matchesSearch = !searchTerm || item.judul.includes(searchTerm);
        const matchesStatus = !statusFilter || item.status === statusFilter;

        if (matchesSearch && matchesStatus) {
            // Update row number
            const firstCell = item.element.querySelector('td');
            firstCell.textContent = ++visibleCount;
            tbody.appendChild(item.element);
        }
    });

    if (visibleCount === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
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
    filterData();
}

// Sort functionality
let sortDirection = {};

function sortTable(columnIndex) {
    const tbody = document.getElementById('disposisiTableBody');
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

// Modal functions
function addDisposisi(suratId) {
    currentSuratId = suratId;
    document.getElementById('modalTitle').textContent = 'Tambah Disposisi';
    document.getElementById('disposisiText').value = '';
    document.getElementById('disposisiModal').classList.remove('hidden');
}

function editDisposisi(suratId) {
    currentSuratId = suratId;
    document.getElementById('modalTitle').textContent = 'Edit Disposisi';
    // Get current disposition text (you'd need to fetch this from your data)
    document.getElementById('disposisiText').value = '';
    document.getElementById('disposisiModal').classList.remove('hidden');
}

function closeDisposisiModal() {
    document.getElementById('disposisiModal').classList.add('hidden');
    currentSuratId = null;
}

// Form submission
document.getElementById('disposisiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const disposisiText = document.getElementById('disposisiText').value;
    
    if (!disposisiText.trim()) {
        alert('Silakan masukkan disposisi terlebih dahulu');
        return;
    }

    // Here you would send the data to your backend
    console.log('Saving disposition for letter:', currentSuratId, 'Text:', disposisiText);
    
    // Close modal and refresh page or update UI
    closeDisposisiModal();
    alert('Disposisi berhasil disimpan');
});

// Action functions
function viewDetail(id) {
    // Implement view detail functionality
    window.location.href = `/surats/${id}`;
}

function printDisposisi(id) {
    // Implement print functionality
    window.open(`/surats/${id}/print-disposisi`, '_blank');
}

// Close modal when clicking outside
document.getElementById('disposisiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDisposisiModal();
    }
});
</script>
@endsection