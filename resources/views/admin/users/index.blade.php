@extends('layouts.app')

@section('content')
<div x-data="usersAdmin()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users mr-3 text-blue-600"></i>
                Daftar Pengguna
            </h1>
            <p class="mt-2 text-sm text-gray-600">Kelola pengguna sistem dengan mudah</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                Total: {{ $users->count() }} pengguna
            </span>
            <!-- Bulk Delete Button -->
            <button x-show="selectedItems.length > 0"
                    @click="confirmBulkDelete()"
                    x-cloak
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-red-700 transition-colors">
                <i class="fas fa-trash-alt mr-2"></i>
                Hapus Terpilih (<span x-text="selectedItems.length"></span>)
            </button>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-600"></i>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-1">
                <div class="relative w-full sm:w-64">
                    <input type="text" id="searchInput" placeholder="Cari pengguna..."
                           x-model="searchTerm"
                           @input="filterUsers()"
                           class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <select id="roleFilter"
                        x-model="roleFilter"
                        @change="filterUsers()"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Role</option>
                    <option value="admin">Administrator</option>
                    <option value="pimpinan">Pimpinan</option>
                    <option value="pegawai">Pegawai</option>
                </select>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="fas fa-filter"></i>
                <span>Filter hasil</span>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Form -->
    <form action="{{ route('admin.users.bulk-destroy') }}" method="POST" id="bulkDeleteUsersForm">
        @csrf
        @method('DELETE')
        <template x-for="id in selectedItems" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <!-- Checkbox Select All -->
                            <th scope="col" class="px-6 py-3 text-left w-12">
                                <input type="checkbox"
                                       x-model="selectAll"
                                       @change="toggleSelectAll()"
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       title="Pilih Semua">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-user text-gray-400"></i>
                                    <span>Pengguna</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-shield-alt text-gray-400"></i>
                                    <span>Role</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-building text-gray-400"></i>
                                    <span>Bagian / Jabatan</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-clock text-gray-400"></i>
                                    <span>Bergabung</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150"
                                data-user-name="{{ strtolower($user->name) }}"
                                data-user-email="{{ strtolower($user->email) }}"
                                data-user-role="{{ strtolower($user->role) }}">

                                <!-- Checkbox -->
                                <td class="px-6 py-4">
                                    @if(Auth::id() !== $user->id)
                                        <input type="checkbox"
                                               x-model="selectedItems"
                                               value="{{ $user->id }}"
                                               class="user-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                               @change="selectAll = document.querySelectorAll('.user-checkbox:checked').length === document.querySelectorAll('.user-checkbox').length">
                                    @else
                                        <span class="text-xs text-gray-400 italic" title="Tidak dapat menghapus akun sendiri">—</span>
                                    @endif
                                </td>

                                <!-- User Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/profile_photos/' . $user->profile_photo) }}"
                                                     class="h-10 w-10 rounded-full object-cover"
                                                     alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-envelope mr-1 text-xs"></i>
                                                {{ $user->email }}
                                            </div>
                                            @if($user->nip)
                                                <div class="text-xs text-gray-400">NIP: {{ $user->nip }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleColors = [
                                            'admin'    => 'bg-purple-100 text-purple-800',
                                            'pimpinan' => 'bg-orange-100 text-orange-800',
                                            'pegawai'  => 'bg-green-100 text-green-800'
                                        ];
                                        $roleIcons = [
                                            'admin'    => 'fas fa-shield-alt',
                                            'pimpinan' => 'fas fa-crown',
                                            'pegawai'  => 'fas fa-user-tie'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                        <i class="{{ $roleIcons[$user->role] ?? 'fas fa-user' }} mr-1"></i>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <!-- Bagian / Jabatan -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($user->bagian || $user->position)
                                        <div class="flex flex-col">
                                            @if($user->bagian)
                                                <div class="flex items-center">
                                                    <i class="fas fa-building mr-2 text-gray-400 text-xs"></i>
                                                    <span>{{ $user->bagian }}</span>
                                                </div>
                                            @endif
                                            @if($user->position)
                                                <div class="flex items-center mt-0.5 text-xs text-gray-500">
                                                    <i class="fas fa-id-badge mr-2 text-gray-300 text-xs"></i>
                                                    <span>{{ $user->position }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Belum diatur</span>
                                    @endif
                                </td>

                                <!-- Join Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Edit -->
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg transition-colors"
                                           title="Edit Pengguna">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>

                                        <!-- Delete (tidak bisa hapus diri sendiri) -->
                                        @if(Auth::id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  class="inline-block delete-user-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        @click="confirmSingleDelete($el.closest('form'))"
                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors"
                                                        title="Hapus Pengguna">
                                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic px-3 py-1.5">Akun Anda</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($users, 'links'))
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengguna</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan pengguna pertama ke sistem</p>
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pengguna Pertama
                </a>
            </div>
        @endif
    </div>

    {{-- ===== MODAL: KONFIRMASI HAPUS SATU ===== --}}
    <div x-show="showDeleteSingleConfirm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-cloak>
        <div @click.away="showDeleteSingleConfirm = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Pengguna?</h3>
            <p class="text-sm text-gray-600 mb-6">Tindakan ini tidak dapat dibatalkan. Data pengguna akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button @click="showDeleteSingleConfirm = false"
                        class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                    Batal
                </button>
                <button @click="submitSingleDelete(); showDeleteSingleConfirm = false"
                        class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MODAL: KONFIRMASI HAPUS BANYAK ===== --}}
    <div x-show="showDeleteBulkConfirm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-cloak>
        <div @click.away="showDeleteBulkConfirm = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users-slash text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Pengguna Terpilih?</h3>
            <p class="text-sm text-gray-600 mb-1">Anda akan menghapus <strong x-text="selectedItems.length"></strong> pengguna.</p>
            <p class="text-xs text-red-600 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="showDeleteBulkConfirm = false"
                        class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                    Batal
                </button>
                <button @click="submitBulkDelete()"
                        class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition">
                    Ya, Hapus Semua
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#usersTableBody tr');

    rows.forEach(function(row) {
        const userName  = (row.dataset.userName  || '');
        const userEmail = (row.dataset.userEmail || '');
        const userRole  = (row.dataset.userRole  || '');

        const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm);
        const matchesRole   = roleFilter === '' || userRole === roleFilter;

        row.style.display = (matchesSearch && matchesRole) ? '' : 'none';
    });
}

function usersAdmin() {
    return {
        selectedItems: [],
        selectAll: false,
        showDeleteBulkConfirm: false,
        showDeleteSingleConfirm: false,
        deleteSingleForm: null,
        searchTerm: '',
        roleFilter: '',
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedItems = Array.from(document.querySelectorAll('.user-checkbox')).map(function(cb) { return cb.value; });
            } else {
                this.selectedItems = [];
            }
        },
        confirmBulkDelete() { if (this.selectedItems.length > 0) this.showDeleteBulkConfirm = true; },
        confirmSingleDelete(formEl) { this.deleteSingleForm = formEl; this.showDeleteSingleConfirm = true; },
        submitSingleDelete() { if (this.deleteSingleForm) this.deleteSingleForm.submit(); },
        submitBulkDelete() { document.getElementById('bulkDeleteUsersForm').submit(); }
    };
}
</script>

@push('scripts')
<script>
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
</script>
@endpush
@endsection
