@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-circle text-primary-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Profil Pengguna</h1>
                <p class="text-gray-600 mt-1">Kelola informasi akun dan tanda tangan digital Anda</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl mb-6 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="font-medium">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('POST')

        {{-- Hidden Inputs untuk Foto Profil yang Sudah Disesuaikan/Dipotong --}}
        <input type="hidden" name="cropped_profile_photo" id="cropped_profile_photo">
        <input type="file" name="profile_photo" id="profile_photo_final" class="hidden" accept="image/png,image/jpeg,image/jpg">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Photo Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-camera text-primary-600"></i> Foto Profil
                    </h3>
                    
                    <div class="text-center py-2">
                        <div class="relative inline-block">
                            <img id="avatar_preview_img" 
                                 src="{{ $user->profile_photo ? asset('storage/profile_photos/' . $user->profile_photo) : asset('img/default-avatar.png') }}" 
                                 alt="Foto Profil" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=200';"
                                 class="w-36 h-36 rounded-full object-cover mx-auto border-4 border-primary-200 shadow-md transition-all duration-300">
                            
                            {{-- Badge Foto Baru --}}
                            <span id="badge_new_photo" class="hidden absolute bottom-1 right-1 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow border-2 border-white">
                                <i class="fas fa-check"></i> Siap Disimpan
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 mt-3">
                            Format JPG, PNG, atau JPEG (Maks. 5MB)
                        </p>

                        {{-- Tombol Pilih & Sesuaikan --}}
                        <div class="mt-4 flex flex-col gap-2">
                            <input type="file" id="profile_photo_picker" accept="image/png,image/jpeg,image/jpg" class="hidden">
                            <button type="button" 
                                    onclick="document.getElementById('profile_photo_picker').click()"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold rounded-xl shadow transition transform hover:scale-[1.02]">
                                <i class="fas fa-image mr-2"></i> Pilih Foto Baru
                            </button>

                            <button type="button" 
                                    id="btn_reopen_crop"
                                    onclick="reopenCropper()"
                                    class="hidden w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-300 transition">
                                <i class="fas fa-crop-alt mr-1.5"></i> Atur Ulang Posisi Gambar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 text-center">
                    <p class="text-[11px] text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i> Anda dapat menggeser posisi foto sebebasnya sebelum disimpan.
                    </p>
                </div>
            </div>

            <!-- User Information Section -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-id-card text-primary-600"></i> Informasi Pengguna
                </h3>

                @if (Auth::user()->role === 'admin')
                    <!-- Admin - Editable Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ $user->name }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                            <input type="text" 
                                   name="position" 
                                   value="{{ $user->position }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('position') border-red-500 @enderror">
                            @error('position')
                                <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                            <input type="text" 
                                   name="nip" 
                                   value="{{ $user->nip }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('nip') border-red-500 @enderror">
                            @error('nip')
                                <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ $user->email }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                @else
                    <!-- Non-Admin - Read Only Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-medium">
                                {{ $user->name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                            <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-medium">
                                {{ $user->position ?: ($user->bagian ?: '-') }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                            <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-medium">
                                {{ $user->nip ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-medium">
                                {{ $user->email }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Digital Signature Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-file-signature text-primary-600"></i> Tanda Tangan Digital
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Upload Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Tanda Tangan Baru</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-signature text-primary-600"></i>
                        </div>
                        <input type="file" 
                               name="signature" 
                               accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('signature') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-2">PNG, JPG, atau GIF (Maks. 2MB)</p>
                        @error('signature')
                            <div class="text-red-500 text-sm mt-2 flex items-center justify-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Preview Section -->
                <div>
                    @if($user->signature)
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan Saat Ini</label>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 flex flex-col items-center justify-center min-h-[160px]">
                            <img src="{{ asset('storage/signatures/' . $user->signature) }}"
                                 alt="Tanda Tangan"
                                 class="max-w-full h-28 object-contain mx-auto rounded border bg-white p-2 shadow-sm">
                            <p class="text-xs text-gray-500 text-center mt-2">Tanda tangan yang aktif digunakan pada surat dinas</p>
                        </div>
                    @else
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview Tanda Tangan</label>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 text-center min-h-[160px] flex flex-col items-center justify-center">
                            <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-signature text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-xs text-gray-500">Belum ada tanda tangan yang diunggah</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4 mt-6">
            <a href="{{ url()->previous() }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            
            <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all shadow-md transform hover:scale-[1.02]">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- ================= MODAL CROPPER FOTO PROFIL ================= --}}
<div id="cropModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden flex flex-col max-h-[92vh]">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fas fa-crop-alt text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold">Sesuaikan Foto Profil</h3>
                    <p class="text-xs text-blue-100">Klik dan geser gambar sebebasnya di dalam bingkai foto</p>
                </div>
            </div>
            <button type="button" onclick="closeCropModal()" class="text-white/80 hover:text-white transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Cropper Work Area (2 Kolom) -->
                <div class="md:col-span-2">
                    <div class="w-full bg-gray-950 rounded-xl overflow-hidden shadow-inner relative flex items-center justify-center" style="height: 380px;">
                        <img id="cropper_image" style="display: block; max-width: 100%;" alt="Gambar untuk dipotong">
                    </div>
                </div>

                <!-- Preview Lingkaran & Toolbar (1 Kolom) -->
                <div class="flex flex-col items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="w-full text-center">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">Hasil Foto Bulat:</span>
                        {{-- Container Live Preview Cropper --}}
                        <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-indigo-500 shadow-md mx-auto bg-white cropper-preview-container"></div>
                        <p class="text-[11px] text-gray-400 mt-2">Tampilan profil Anda</p>
                    </div>

                    <!-- Toolbar Tombol Aksi Geser/Zoom -->
                    <div class="w-full space-y-2 mt-4 pt-3 border-t border-gray-200">
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block text-center">Alat Penyesuaian</span>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" id="btn_zoom_in" title="Perbesar" class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-gray-700 text-xs shadow-sm">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button type="button" id="btn_zoom_out" title="Perkecil" class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-gray-700 text-xs shadow-sm">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button type="button" id="btn_rotate_left" title="Putar Kiri" class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-gray-700 text-xs shadow-sm">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button type="button" id="btn_rotate_right" title="Putar Kanan" class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-gray-700 text-xs shadow-sm">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                        <button type="button" id="btn_reset" class="w-full py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg transition">
                            <i class="fas fa-sync-alt mr-1"></i> Reset Posisi
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-xs flex items-center gap-2">
                <i class="fas fa-hand-paper text-blue-600 text-sm shrink-0"></i>
                <span><strong>Tips:</strong> Klik &amp; tahan pada gambar lalu geser ke segala arah, atau gunakan scroll mouse untuk memperbesar/memperkecil bagian foto yang diinginkan.</span>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" onclick="closeCropModal()" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-200 transition">
                Batal
            </button>
            <button type="button" id="btn_apply_crop" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-xs font-bold rounded-xl shadow-md transition transform hover:scale-105 flex items-center gap-1.5">
                <i class="fas fa-check"></i> Gunakan &amp; Terapkan Foto
            </button>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<style>
    /* Styling cropper view circle */
    .cropper-view-box,
    .cropper-face {
        border-radius: 50% !important;
    }
    .cropper-view-box {
        outline: 2px solid #3b82f6 !important;
        outline-color: rgba(59, 130, 246, 0.8) !important;
    }
    .cropper-preview-container {
        width: 112px !important;
        height: 112px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
        position: relative !important;
    }
    .cropper-preview-container img {
        border-radius: 0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropperInstance = null;
    let currentRawImageSrc = null;

    document.addEventListener('DOMContentLoaded', function () {
        const filePicker = document.getElementById('profile_photo_picker');
        const cropperImage = document.getElementById('cropper_image');
        const cropModal = document.getElementById('cropModal');
        const btnApply = document.getElementById('btn_apply_crop');

        // Toolbar buttons
        document.getElementById('btn_zoom_in').addEventListener('click', function () {
            if (cropperInstance) cropperInstance.zoom(0.1);
        });
        document.getElementById('btn_zoom_out').addEventListener('click', function () {
            if (cropperInstance) cropperInstance.zoom(-0.1);
        });
        document.getElementById('btn_rotate_left').addEventListener('click', function () {
            if (cropperInstance) cropperInstance.rotate(-90);
        });
        document.getElementById('btn_rotate_right').addEventListener('click', function () {
            if (cropperInstance) cropperInstance.rotate(90);
        });
        document.getElementById('btn_reset').addEventListener('click', function () {
            if (cropperInstance) cropperInstance.reset();
        });

        // Saat pengguna memilih file
        filePicker.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                if (!file.type.startsWith('image/')) {
                    alert('Harap pilih file gambar yang valid (JPG, PNG, atau JPEG).');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (evt) {
                    currentRawImageSrc = evt.target.result;
                    openCropper(currentRawImageSrc);
                };
                reader.readAsDataURL(file);
            }
        });

        // Terapkan hasil crop
        btnApply.addEventListener('click', function () {
            if (!cropperInstance) return;

            // Dapatkan canvas potongan 400x400
            const canvas = cropperInstance.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            if (!canvas) {
                alert('Gagal memproses potongan gambar. Silakan coba lagi.');
                return;
            }

            const dataUrl = canvas.toDataURL('image/png');

            // 1. Update preview foto di halaman profil
            const avatarPreview = document.getElementById('avatar_preview_img');
            avatarPreview.src = dataUrl;
            avatarPreview.classList.add('ring-4', 'ring-emerald-400');

            // 2. Tampilkan badge siap disimpan & tombol atur ulang
            document.getElementById('badge_new_photo').classList.remove('hidden');
            document.getElementById('btn_reopen_crop').classList.remove('hidden');

            // 3. Simpan base64 ke hidden input untuk submit
            document.getElementById('cropped_profile_photo').value = dataUrl;

            // 4. Buat File blob juga ke profile_photo_final via DataTransfer jika didukung
            canvas.toBlob(function (blob) {
                if (blob) {
                    try {
                        const file = new File([blob], 'avatar_cropped.png', { type: 'image/png' });
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        document.getElementById('profile_photo_final').files = dt.files;
                    } catch (err) {
                        console.warn('DataTransfer not supported:', err);
                    }
                }
            }, 'image/png');

            closeCropModal();
        });
    });

    function openCropper(imageSrc) {
        const cropModal = document.getElementById('cropModal');
        const cropperImage = document.getElementById('cropper_image');

        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }

        // Tampilkan modal dulu sebelum load gambar agar container punya dimensi nyata
        cropModal.classList.remove('hidden');

        // Reset src dulu agar onload selalu terpanggil (termasuk saat gambar sama)
        cropperImage.src = '';

        cropperImage.onload = function () {
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }

            cropperInstance = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 2,          // Gambar harus memenuhi crop box → tidak ada area hitam
                dragMode: 'move',     // Geser gambar bebas
                autoCropArea: 0.85,   // Crop box 85% ukuran canvas
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: false,  // Crop box tetap di tengah, gambar yang bergerak
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false,
                preview: '.cropper-preview-container',
                ready: function () {
                    // Posisikan crop box tepat di tengah canvas
                    const canvasData   = this.cropper.getCanvasData();
                    const cropBoxData  = this.cropper.getCropBoxData();
                    const newLeft = canvasData.left + (canvasData.width  - cropBoxData.width)  / 2;
                    const newTop  = canvasData.top  + (canvasData.height - cropBoxData.height) / 2;
                    this.cropper.setCropBoxData({
                        left:   Math.max(0, newLeft),
                        top:    Math.max(0, newTop),
                        width:  cropBoxData.width,
                        height: cropBoxData.height
                    });
                }
            });
        };

        // Beri browser 1 frame untuk render modal agar dimensi container sudah tersedia
        requestAnimationFrame(function () {
            cropperImage.src = imageSrc;
        });
    }

    function reopenCropper() {
        if (currentRawImageSrc) {
            openCropper(currentRawImageSrc);
        } else {
            document.getElementById('profile_photo_picker').click();
        }
    }

    function closeCropModal() {
        const cropModal = document.getElementById('cropModal');
        cropModal.classList.add('hidden');
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
    }
</script>
@endpush
@endsection
