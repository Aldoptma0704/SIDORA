<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DisposisiController;
use App\Http\Controllers\Admin\SuratController as AdminSuratController;
use App\Http\Controllers\Pegawai\SuratController as PegawaiSuratController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\Pimpinan\SuratController as PimpinanSuratController;
use App\Http\Controllers\ProfileController;

// ---------------- Halaman Awal / Login ----------------
Route::get('/', fn() => redirect()->route('login'));
Route::get('/home', function () {
    if (auth()->check()) {
        return redirect('/' . auth()->user()->role . '/dashboard');
    }
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------------- Admin ----------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::resource('/users', UserController::class);
    Route::get('/laporan-surat', [UserController::class, 'laporan'])->name('laporan');

    // Disposisi
    Route::get('/disposisi', [DisposisiController::class, 'form'])->name('disposisi.form');
    Route::post('/disposisi', [DisposisiController::class, 'kirim'])->name('disposisi.kirim');
    Route::get('/disposisi-masuk', [AdminSuratController::class, 'disposisiMasuk'])->name('disposisi.masuk');
    Route::get('/disposisi/{id}/form', [AdminSuratController::class, 'formDisposisi'])->name('surat.disposisi.form');
    Route::post('/disposisi/{id}/kirim', [AdminSuratController::class, 'kirimDisposisi'])->name('surat.disposisi.kirim');

    // Surat
    Route::get('/surat/masuk', [AdminSuratController::class, 'suratMasuk'])->name('surat.masuk');
    Route::get('/surat-dari-pimpinan', [AdminSuratController::class, 'index'])->name('surat.dari-pimpinan');
    Route::get('/surat-dari-pimpinan/{id}', [AdminSuratController::class, 'lihat'])->name('surat.dari-pimpinan.lihat');
    Route::get('/surat/{surat}/download', [AdminSuratController::class, 'download'])->name('surat.download');
});

// ---------------- Pegawai ----------------
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');
    Route::get('/status-surat', [PegawaiSuratController::class, 'statusSurat'])->name('surat.status');
    Route::get('/surat', [PegawaiSuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/create', [PegawaiSuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [PegawaiSuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{id}/preview', [PegawaiSuratController::class, 'preview'])->name('surat.preview');
    Route::get('/surat/{id}/download', [PegawaiSuratController::class, 'download'])->name('surat.download');
    Route::get('/surat/{id}/edit', [PegawaiSuratController::class, 'edit'])->name('surat.edit');
    Route::put('/surat/{id}', [PegawaiSuratController::class, 'update'])->name('surat.update');
    Route::delete('/surat/{id}', [PegawaiSuratController::class, 'destroy'])->name('surat.destroy');
    Route::delete('/surat/bulk-delete', [PegawaiSuratController::class, 'bulkDelete'])->name('surat.bulk-delete');
    Route::post('/surat/upload-pdf', [PegawaiSuratController::class, 'uploadPdf'])->name('surat.upload-pdf');
    Route::post('/surat/ajukan', [PegawaiSuratController::class, 'ajukan'])->name('surat.ajukan');
});

// ---------------- Pimpinan ----------------
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [PimpinanSuratController::class, 'index'])->name('dashboard');

    // Surat approval
    Route::post('/surat/{id}/setujui', [PimpinanSuratController::class, 'setujui'])->name('surat.setujui');
    Route::post('/surat/{id}/tolak', [PimpinanSuratController::class, 'tolak'])->name('surat.tolak');

    // Surat balasan
    Route::get('/balasansurat', [PimpinanSuratController::class, 'balasanSurat'])->name('balasan');
    Route::get('/surat/{id}/balas', [PimpinanSuratController::class, 'buatBalasan'])->name('surat.balas');
    Route::post('/surat/{id}/balas', [PimpinanSuratController::class, 'kirimBalasan'])->name('surat.balas.kirim');
    Route::post('/surat/balas', [PimpinanSuratController::class, 'simpanSuratBalasan'])->name('surat.balas.store');

    // Surat manajemen
    Route::get('/surat/{id}/preview', [PimpinanSuratController::class, 'preview'])->name('surat.preview');
    Route::get('/surat/{id}/view', [PimpinanSuratController::class, 'view'])->name('surat.view');
    Route::get('/surat/{surat}/edit', [PimpinanSuratController::class, 'edit'])->name('surat.edit');
    Route::put('/surat/{surat}', [PimpinanSuratController::class, 'update'])->name('surat.update');
    Route::delete('/surat/{id}', [PimpinanSuratController::class, 'destroy'])->name('surat.destroy');
    Route::get('/surat/{surat}/download', [PimpinanSuratController::class, 'download'])->name('surat.download');

    // Status & disposisi
    Route::get('/status-surat', [PimpinanSuratController::class, 'statusSuratBalasan'])->name('status-surat');
    Route::get('/disposisi', [PimpinanSuratController::class, 'disposisi'])->name('disposisi');
    Route::post('/kirim-surat/{id}', [PimpinanSuratController::class, 'kirimSurat'])->name('kirim-surat');
});

// ---------------- Profile ----------------
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['post', 'put'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Reset password
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('reset.password.form');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');
