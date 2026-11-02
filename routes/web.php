<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
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
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------------- Admin ----------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::resource('/users', UserController::class);
    Route::match(['delete', 'post'], '/users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::get('/laporan-surat', [UserController::class, 'laporan'])->name('laporan');

    // Surat Masuk (Instansi Luar ke Karyawan)
    Route::get('/surat/masuk', [AdminSuratController::class, 'suratMasuk'])->name('surat.masuk');
    Route::post('/surat/masuk', [AdminSuratController::class, 'storeSuratMasuk'])->name('surat.masuk.store');
    Route::get('/surat/masuk/{id}', [AdminSuratController::class, 'lihatSuratMasuk'])->name('surat.masuk.lihat');
    Route::post('/surat/masuk/{id}/kirim', [AdminSuratController::class, 'kirimSuratMasuk'])->name('surat.masuk.kirim');
    Route::match(['delete', 'post'], '/surat/masuk/bulk-destroy', [AdminSuratController::class, 'bulkDestroySuratMasuk'])->name('surat.masuk.bulk-destroy');
    Route::match(['delete', 'post'], '/surat/masuk/{id}', [AdminSuratController::class, 'destroySuratMasuk'])->name('surat.masuk.destroy');

    // Surat dari Pimpinan / Laporan
    Route::get('/surat-dari-pimpinan', [AdminSuratController::class, 'index'])->name('surat.dari-pimpinan');
    Route::get('/surat-dari-pimpinan/{id}', [AdminSuratController::class, 'lihat'])->name('surat.dari-pimpinan.lihat')->whereNumber('id');
    Route::match(['delete', 'post'], '/surat/bulk-destroy', [AdminSuratController::class, 'bulkDestroySurat'])->name('surat.bulk-destroy');
    Route::match(['delete', 'post'], '/surat/{id}', [AdminSuratController::class, 'destroySurat'])->name('surat.destroy')->whereNumber('id');
    Route::post('/surat/{id}/generate-nomor', [AdminSuratController::class, 'generateNomor'])->name('surat.generate-nomor')->whereNumber('id');
    Route::get('/surat/{surat}/download', [AdminSuratController::class, 'download'])->name('surat.download');
});

// ---------------- Pegawai ----------------
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');
    Route::get('/status-surat', [PegawaiSuratController::class, 'statusSurat'])->name('surat.status');
    Route::get('/surat', [PegawaiSuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/create', [PegawaiSuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [PegawaiSuratController::class, 'store'])->name('surat.store');
    Route::post('/surat/ajukan', [PegawaiSuratController::class, 'ajukan'])->name('surat.ajukan');
    Route::post('/surat/upload-pdf', [PegawaiSuratController::class, 'uploadPdf'])->name('surat.upload-pdf');
    Route::match(['delete', 'post'], '/surat/bulk-delete', [PegawaiSuratController::class, 'bulkDelete'])->name('surat.bulk-delete');
    Route::get('/surat/{id}/preview', [PegawaiSuratController::class, 'preview'])->name('surat.preview')->whereNumber('id');
    Route::get('/surat/{id}/download', [PegawaiSuratController::class, 'download'])->name('surat.download')->whereNumber('id');
    Route::get('/surat/{id}/edit', [PegawaiSuratController::class, 'edit'])->name('surat.edit')->whereNumber('id');
    Route::put('/surat/{id}', [PegawaiSuratController::class, 'update'])->name('surat.update')->whereNumber('id');
    Route::match(['delete', 'post'], '/surat/{id}', [PegawaiSuratController::class, 'destroy'])->name('surat.destroy')->whereNumber('id');
});

// ---------------- Pimpinan ----------------
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [PimpinanSuratController::class, 'index'])->name('dashboard');

    // Surat approval
    Route::post('/surat/{id}/setujui', [PimpinanSuratController::class, 'setujui'])->name('surat.setujui')->whereNumber('id');
    Route::post('/surat/{id}/tolak', [PimpinanSuratController::class, 'tolak'])->name('surat.tolak')->whereNumber('id');

    // Surat balasan
    Route::get('/balasansurat', [PimpinanSuratController::class, 'balasanSurat'])->name('balasan');
    Route::post('/surat/balas', [PimpinanSuratController::class, 'simpanSuratBalasan'])->name('surat.balas.store');

    // Status & disposisi
    Route::get('/status-surat', [PimpinanSuratController::class, 'statusSuratBalasan'])->name('status-surat');
    Route::post('/surat/ajukan', [PimpinanSuratController::class, 'ajukan'])->name('surat.ajukan');
    Route::get('/disposisi', [PimpinanSuratController::class, 'disposisi'])->name('disposisi');

    // Surat manajemen
    Route::match(['delete', 'post'], '/surat/bulk-delete', [PimpinanSuratController::class, 'bulkDelete'])->name('surat.bulk-delete');
    Route::get('/surat/{id}/preview', [PimpinanSuratController::class, 'preview'])->name('surat.preview')->whereNumber('id');
    Route::get('/surat/{surat}/edit', [PimpinanSuratController::class, 'edit'])->name('surat.edit');
    Route::put('/surat/{surat}', [PimpinanSuratController::class, 'update'])->name('surat.update');
    Route::match(['delete', 'post'], '/surat/{id}', [PimpinanSuratController::class, 'destroy'])->name('surat.destroy')->whereNumber('id');
    Route::get('/surat/{surat}/download', [PimpinanSuratController::class, 'download'])->name('surat.download');

    // Surat Masuk Pimpinan
    Route::get('/surat-masuk', [PimpinanSuratController::class, 'suratMasuk'])->name('surat-masuk');
    Route::post('/surat-masuk/upload-pdf', [PimpinanSuratController::class, 'uploadPdf'])->name('surat-masuk.upload-pdf');
    Route::match(['delete', 'post'], '/surat-masuk/bulk-delete', [PimpinanSuratController::class, 'bulkDeleteSuratMasuk'])->name('surat-masuk.bulk-delete');
    Route::match(['delete', 'post'], '/surat-masuk/{id}', [PimpinanSuratController::class, 'destroySuratMasuk'])->name('surat-masuk.destroy')->whereNumber('id');
});

// ---------------- Profile ----------------
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['post', 'put'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Reset password
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('reset.password.form');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');

// Melayani file storage publik secara langsung (menjamin gambar selalu tampil meski symlink Windows bermasalah)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');
