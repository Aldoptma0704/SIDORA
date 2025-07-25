@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Disposisi Surat</h1>
                        <p class="text-sm text-gray-600">Kelola dan kirim disposisi surat kepada pegawai yang dituju</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('admin.disposisi.kirim') }}" method="POST" class="space-y-6 p-6">
                @csrf

                <!-- Alert Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informasi Disposisi</h3>
                            <p class="text-sm text-blue-700 mt-1">Pastikan memilih surat dan pegawai tujuan dengan benar sebelum mengirim disposisi.</p>
                        </div>
                    </div>
                </div>

                <!-- Pilih Surat -->
                <div class="space-y-2">
                    <label for="surat_id" class="block text-sm font-semibold text-gray-900">
                        <span class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Pilih Surat</span>
                        </span>
                    </label>
                    <select name="surat_id" id="surat_id" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white">
                        <option value="" disabled selected>-- Pilih surat yang akan didisposisikan --</option>
                        @foreach ($surats as $surat)
                            <option value="{{ $surat->id }}" class="py-2">
                                <span class="font-medium">{{ $surat->judul }}</span>
                                <span class="text-gray-500">(Status: {{ ucfirst($surat->status) }})</span>
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500">Pilih surat dari daftar yang tersedia untuk didisposisikan</p>
                </div>

                <!-- Pilih Pegawai -->
                <div class="space-y-2">
                    <label for="disposisi_user_id" class="block text-sm font-semibold text-gray-900">
                        <span class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Pegawai Tujuan Disposisi</span>
                        </span>
                    </label>
                    <select name="disposisi_user_id" id="disposisi_user_id" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-white">
                        <option value="" disabled selected>-- Pilih pegawai tujuan disposisi --</option>
                        @foreach ($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" class="py-2">
                                <span class="font-medium">{{ $pegawai->name }}</span>
                                <span class="text-gray-600">- {{ $pegawai->bagian }}</span>
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500">Pilih pegawai yang akan menerima disposisi surat</p>
                </div>

                <!-- Catatan Disposisi (Optional) -->
                <div class="space-y-2">
                    <label for="catatan" class="block text-sm font-semibold text-gray-900">
                        <span class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            <span>Catatan Disposisi</span>
                            <span class="text-xs text-gray-400 font-normal">(Opsional)</span>
                        </span>
                    </label>
                    <textarea name="catatan" id="catatan" rows="4" 
                              placeholder="Masukkan catatan atau instruksi khusus untuk disposisi ini..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"></textarea>
                    <p class="text-xs text-gray-500">Berikan catatan atau instruksi tambahan jika diperlukan</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Disposisi
                    </button>
                </div>
            </form>
        </div>

        <!-- Additional Info Card -->
        <div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Panduan Disposisi</h4>
                    <ul class="mt-2 text-sm text-gray-600 space-y-1">
                        <li>• Pastikan surat yang dipilih memiliki status yang sesuai untuk didisposisikan</li>
                        <li>• Pilih pegawai berdasarkan bidang keahlian dan tanggung jawab yang relevan</li>
                        <li>• Gunakan catatan untuk memberikan instruksi yang jelas dan spesifik</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    select option {
        padding: 8px 12px;
    }
    
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    @media (max-width: 640px) {
        .max-w-4xl {
            max-width: 100%;
            margin: 0 1rem;
        }
    }
</style>

<!-- JavaScript for Enhanced UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const suratSelect = document.getElementById('surat_id');
    const pegawaiSelect = document.getElementById('disposisi_user_id');
    const submitButton = document.querySelector('button[type="submit"]');
    
    // Add loading state to submit button
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengirim Disposisi...
        `;
    });
    
    // Validate form before submit
    function validateForm() {
        const isValid = suratSelect.value && pegawaiSelect.value;
        submitButton.disabled = !isValid;
        
        if (isValid) {
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
    
    suratSelect.addEventListener('change', validateForm);
    pegawaiSelect.addEventListener('change', validateForm);
    
    // Initial validation
    validateForm();
});
</script>
@endsection