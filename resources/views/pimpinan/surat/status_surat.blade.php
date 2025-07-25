@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Status Surat Balasan</h1>
                            <p class="text-blue-100 text-lg">Kelola dan pantau surat balasan Anda</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white bg-opacity-10 rounded-lg p-3 text-center">
                            <div class="text-2xl font-bold">{{ $drafts->count() }}</div>
                            <div class="text-sm text-blue-100">Total Draft</div>
                        </div>
                        <a href="/pimpinan/balasansurat" 
                           class="inline-flex items-center px-6 py-3 bg-white text-blue-700 font-semibold rounded-xl hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Buat Surat Balasan Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
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

        {{-- Main Content --}}
        @if($drafts->count() > 0)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                {{-- Desktop Table View --}}
                <div class="hidden lg:block">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center space-x-2">
                                            <span>#</span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Judul Surat</span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                            </svg>
                                            <span>Isi Ringkas</span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center justify-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Status</span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center justify-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                            <span>Aksi</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($drafts as $index => $surat)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                                                    <span class="text-blue-700 font-semibold text-sm">{{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3 group-hover:scale-110 transition-transform duration-200"></div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 leading-tight">
                                                        {{ $surat->judul }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Dibuat: {{ \Carbon\Carbon::parse($surat->created_at)->format('d M Y, H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-700 leading-relaxed">
                                                {{ Str::limit(strip_tags($surat->isi), 120) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $status = $surat->status_balasan ?? 'draft';
                                                $statusColors = [
                                                    'draft' => 'bg-yellow-100 text-yellow-800',
                                                    'pending' => 'bg-blue-100 text-blue-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusIcons = [
                                                    'draft' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
                                                    'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    'approved' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    'rejected' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? $statusColors['draft'] }}">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcons[$status] ?? $statusIcons['draft'] }}"/>
                                                </svg>
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                {{-- Tombol View --}}
                                                <a href="{{ route('pimpinan.surat.view', $surat->id) }}"
                                                   class="inline-flex items-center px-3 py-2 bg-gray-500 text-white text-xs font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View
                                                </a>

                                                {{-- Tombol Kirim --}}
                                                <form action="{{ route('pimpinan.kirim-surat', $surat->id) }}" method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin mengirim surat ini ke Admin?')" 
                                                      class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                        </svg>
                                                        Kirim
                                                    </button>
                                                </form>

                                                {{-- Tombol Delete --}}
                                                <form action="{{ route('pimpinan.surat.destroy', $surat->id) }}" method="POST" 
                                                      onsubmit="return confirm('Yakin ingin menghapus surat ini?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile Card View --}}
                <div class="lg:hidden">
                    <div class="divide-y divide-gray-200">
                        @foreach($drafts as $index => $surat)
                            <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-700 font-semibold text-sm">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    </div>
                                    @php
                                        $status = $surat->status_balasan ?? 'draft';
                                        $statusColors = [
                                            'draft' => 'bg-yellow-100 text-yellow-800',
                                            'pending' => 'bg-blue-100 text-blue-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? $statusColors['draft'] }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">{{ $surat->judul }}</h3>
                                    <p class="text-xs text-gray-600 mb-3 leading-relaxed">
                                        {{ Str::limit(strip_tags($surat->isi), 100) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Dibuat: {{ \Carbon\Carbon::parse($surat->created_at)->format('d M Y, H:i') }}
                                    </p>
                                </div>

                                <div class="flex space-x-2">
                                    <a href="{{ route('pimpinan.surat.view', $surat->id) }}"
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-500 text-white text-xs font-medium rounded-lg hover:bg-gray-600 transition-all duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>

                                    <form action="{{ route('pimpinan.kirim-surat', $surat->id) }}" method="POST" 
                                          onsubmit="return confirm('Kirim surat ini ke Admin?')" 
                                          class="flex-1">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-all duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                            Kirim
                                        </button>
                                    </form>

                                    {{-- Tombol Delete --}}
                                    <form action="{{ route('pimpinan.surat.destroy', $surat->id) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus surat ini?')"
                                          class="flex-none">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-all duration-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-16">
                <div class="bg-white rounded-2xl shadow-xl p-12 mx-auto max-w-md">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Surat Balasan</h3>
                    <p class="text-gray-500 mb-6">Mulai buat surat balasan pertama Anda untuk membalas surat masuk.</p>
                    <a href="/pimpinan/balasansurat" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Buat Surat Balasan Baru
                    </a>
                </div>
            </div>
        @endif

        {{-- Back to Dashboard --}}
        <div class="mt-8 text-center">
            <a href="/pimpinan/dashboard" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-medium rounded-xl hover:from-gray-700 hover:to-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
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

    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }

    /* Hover effects */
    .group:hover .w-2.h-2.bg-blue-500 {
        background-color: #3b82f6;
        transform: scale(1.2);
        transition: all 0.2s ease;
    }

    /* Smooth transitions */
    * {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-duration: 200ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endsection