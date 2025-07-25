@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header dengan design yang menarik --}}
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Daftar Disposisi Surat</h1>
                            <p class="text-blue-100 text-lg">Surat yang telah disetujui dan didisposisikan</p>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <div class="bg-white bg-opacity-10 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold">{{ $surats->count() }}</div>
                            <div class="text-sm text-blue-100">Total Surat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Message dengan animasi --}}
        @if (session('success'))
            <div class="mb-6 animate-fade-in">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            // Group surat berdasarkan disposisi tujuannya
            $grouped = $surats->groupBy('disposisi');
        @endphp

        {{-- Content Area --}}
        @forelse ($grouped as $tujuan => $listSurat)
            <div class="mb-8 animate-slide-up">
                {{-- Header Grup Disposisi --}}
                <div class="bg-white rounded-t-xl shadow-lg border border-gray-200">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white bg-opacity-20 rounded-full p-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">{{ $tujuan ?? 'Tidak Diketahui' }}</h2>
                                    <p class="text-indigo-100 text-sm">{{ $listSurat->count() }} surat didisposisikan</p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-lg px-3 py-1">
                                <span class="text-white text-sm font-medium">{{ $listSurat->count() }} item</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Responsif --}}
                    <div class="overflow-hidden">
                        {{-- Desktop View --}}
                        <div class="hidden lg:block">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Judul Surat
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Tanggal Disetujui
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Isi Ringkas
                                        </th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($listSurat as $index => $surat)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-25' }}">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 leading-tight">
                                                            {{ $surat->judul }}
                                                        </div>
                                                        @if($surat->nomor_surat)
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                No: {{ $surat->nomor_surat }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($surat->signed_at)->format('d M Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($surat->signed_at)->format('H:i') }} WIB
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-700 leading-relaxed">
                                                    {{ Str::limit(strip_tags($surat->isi), 100) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="{{ route('pimpinan.surat.preview', $surat->id) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Preview
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile View --}}
                        <div class="lg:hidden">
                            <div class="divide-y divide-gray-200">
                                @foreach ($listSurat as $surat)
                                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center mb-2">
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $surat->judul }}
                                                    </h3>
                                                </div>
                                                @if($surat->nomor_surat)
                                                    <p class="text-xs text-gray-500 mb-2">No: {{ $surat->nomor_surat }}</p>
                                                @endif
                                                <p class="text-xs text-gray-600 mb-3 leading-relaxed">
                                                    {{ Str::limit(strip_tags($surat->isi), 80) }}
                                                </p>
                                                <div class="flex items-center justify-between">
                                                    <div class="text-xs text-gray-500">
                                                        <div>{{ \Carbon\Carbon::parse($surat->signed_at)->format('d M Y') }}</div>
                                                        <div>{{ \Carbon\Carbon::parse($surat->signed_at)->format('H:i') }} WIB</div>
                                                    </div>
                                                    <a href="{{ route('pimpinan.surat.preview', $surat->id) }}" 
                                                       class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Preview
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State dengan ilustrasi --}}
            <div class="text-center py-16">
                <div class="bg-white rounded-2xl shadow-xl p-12 mx-auto max-w-md">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Disposisi</h3>
                    <p class="text-gray-500 mb-6">Belum ada surat yang didisposisikan. Surat yang telah disetujui akan muncul di sini.</p>
                    <a href="/pimpinan/dashboard" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @endforelse

        {{-- Back to Dashboard Button --}}
        @if($grouped->count() > 0)
            <div class="mt-8 text-center">
                <a href="/pimpinan/dashboard" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-medium rounded-xl hover:from-gray-700 hover:to-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Custom CSS untuk animasi dan styling tambahan --}}
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }

    .animate-slide-up {
        animation: slide-up 0.6s ease-out;
    }

    .bg-gray-25 {
        background-color: #fafafa;
    }

    /* Hover effect untuk tabel */
    tr:hover .w-2.h-2.bg-blue-500 {
        background-color: #3b82f6;
        transform: scale(1.2);
        transition: all 0.2s ease;
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Custom shadow untuk card */
    .shadow-custom {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection