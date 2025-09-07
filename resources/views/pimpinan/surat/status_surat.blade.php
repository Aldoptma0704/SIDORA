@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
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
                        <a href="/pimpinan/balasansurat" 
                           class="inline-flex items-center px-6 py-3 bg-white text-blue-700 font-semibold rounded-xl hover:bg-blue-50 shadow-lg hover:shadow-xl">
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
                <div class="bg-green-100 border-l-4 border-green-500 rounded-lg p-4 shadow-md">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Tab Navigation --}}
        <div x-data="{ tab: 'draft' }">
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="tab = 'draft'" 
                        :class="tab === 'draft' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Draft ({{ $drafts->where('status','draft_pimpinan')->count() }})
                    </button>
                    <button @click="tab = 'approved'" 
                        :class="tab === 'approved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Disetujui ({{ $drafts->where('status','disetujui')->count() }})
                    </button>
                </nav>
            </div>
        
            {{-- Draft --}}
            <div x-show="tab === 'draft'">
                @include('pimpinan.surat.partials.table', [
                    'items' => $drafts->where('status','draft_pimpinan'),
                    'mode' => 'draft'
                ])
            </div>
                
            {{-- Disetujui --}}
            <div x-show="tab === 'approved'">
                @include('pimpinan.surat.partials.table', [
                    'items' => $drafts->where('status','disetujui'),
                    'mode' => 'approved'
                ])
            </div>
        </div>

        {{-- Back to Dashboard --}}
        <div class="mt-8 text-center">
            <a href="/pimpinan/dashboard" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-medium rounded-xl hover:from-gray-700 hover:to-gray-800 shadow-lg">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

{{-- AlpineJS untuk tab --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
