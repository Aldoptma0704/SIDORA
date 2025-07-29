@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Welcome Section --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full -translate-y-32 translate-x-32 opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-purple-100 to-pink-100 rounded-full translate-y-24 -translate-x-24 opacity-50"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg">
                            <i class="fas fa-tachometer-alt text-white text-3xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                                Dashboard Surat
                            </h1>
                            <p class="text-gray-500 mt-2 text-lg">Kelola dan pantau semua aktivitas surat Anda</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('surat.create') }}" 
                           class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform duration-200"></i> 
                            Buat Surat Baru
                        </a>
                        <a href="#ajukan-persetujuan" 
                           class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl shadow-lg hover:from-green-700 hover:to-green-800 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-paper-plane mr-2 group-hover:translate-x-1 transition-transform duration-200"></i> 
                            Ajukan Persetujuan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Section --}}
        <div class="mb-8">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl mr-4">
                    <i class="fas fa-chart-bar text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Statistik Surat Anda</h2>
                    <p class="text-gray-500">Ringkasan status surat dalam sistem</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Card Menunggu --}}
                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl p-8 text-center border border-gray-100 transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-orange-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-2xl mb-4 group-hover:from-yellow-200 group-hover:to-orange-200 transition-colors duration-300">
                            <i class="fas fa-hourglass-half group-hover:rotate-12 text-yellow-600 text-3xl group-hover:animate-pulse transition-transform duration-200"></i>
                        </div>
                        <div class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Menunggu Persetujuan</div>
                        <div class="text-4xl font-bold text-gray-800 mb-2">{{ $countMenunggu }}</div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-yellow-400 to-orange-400 h-2 rounded-full" style="width: {{ $countMenunggu > 0 ? min(($countMenunggu / ($countMenunggu + $countDisetujui + $countDitolak)) * 100, 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Surat dalam proses review</p>
                    </div>
                </div>

                {{-- Card Disetujui --}}
                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl p-8 text-center border border-gray-100 transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl mb-4 group-hover:from-green-200 group-hover:to-emerald-200 transition-colors duration-300">
                            <i class="fas fa-check-circle text-green-600 text-3xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-200"></i>
                        </div>
                        <div class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Disetujui</div>
                        <div class="text-4xl font-bold text-gray-800 mb-2">{{ $countDisetujui }}</div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-400 to-emerald-400 h-2 rounded-full" style="width: {{ $countDisetujui > 0 ? min(($countDisetujui / ($countMenunggu + $countDisetujui + $countDitolak)) * 100, 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Surat telah disetujui</p>
                    </div>
                </div>

                {{-- Card Ditolak --}}
                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl p-8 text-center border border-gray-100 transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-pink-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-red-100 to-pink-100 rounded-2xl mb-4 group-hover:from-red-200 group-hover:to-pink-200 transition-colors duration-300">
                            <i class="fas fa-times-circle text-red-600 text-3xl group-hover:rotate-12 transition-transform duration-200"></i>
                        </div>
                        <div class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Ditolak</div>
                        <div class="text-4xl font-bold text-gray-800 mb-2">{{ $countDitolak }}</div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-400 to-pink-400 h-2 rounded-full" style="width: {{ $countDitolak > 0 ? min(($countDitolak / ($countMenunggu + $countDisetujui + $countDitolak)) * 100, 100) : 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Surat memerlukan revisi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-br from-purple-100 to-blue-100 rounded-xl mr-4">
                    <i class="fas fa-bolt text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Aksi Cepat</h3>
                    <p class="text-gray-500">Akses cepat ke fitur yang sering digunakan</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('surat.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl hover:from-blue-100 hover:to-purple-100 transition-all duration-200">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-200">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Daftar Surat</div>
                        <div class="text-sm text-gray-600">Lihat semua surat</div>
                    </div>
                </a>
        </div>

        {{-- Summary Card --}}
        @if(($countMenunggu + $countDisetujui + $countDitolak) > 0)
        <div class="mt-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Total Surat: {{ $countMenunggu + $countDisetujui + $countDitolak }}</h3>
                    <p class="text-indigo-100">
                        @php
                            $total = $countMenunggu + $countDisetujui + $countDitolak;
                            $approvalRate = $total > 0 ? round(($countDisetujui / $total) * 100, 1) : 0;
                        @endphp
                        Tingkat persetujuan: {{ $approvalRate }}%
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold">📊</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
@endsection