<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Pegawai\SuratController as PegawaiSuratController;
use App\Http\Controllers\Pimpinan\SuratController as PimpinanSuratController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Halaman Awal / Login
Route::get('/', function () {
    return redirect()->route('login');
});

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
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('/users', UserController::class);
});

// ---------------- Pegawai ----------------
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('pegawai.dashboard');
    Route::get('/status_surat', [PegawaiDashboardController::class, 'statusSurat'])->name('surat.status_surat');
    Route::post('/surat', [PegawaiSuratController::class, 'store'])->name('surat.store');
    Route::get('/surat', [PegawaiSuratController::class, 'index'])->name('surat.index');

    Route::get('/surat/create', [PegawaiSuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [PegawaiSuratController::class, 'store'])->name('surat.store');

    Route::get('/surat/{id}/preview', [PegawaiSuratController::class, 'preview'])->name('surat.preview');
    Route::get('/surat/{id}/download', [PegawaiSuratController::class, 'download'])->name('surat.download');
    Route::get('/surat/{id}/edit', [PegawaiSuratController::class, 'edit'])->name('surat.edit');
    Route::put('/surat/{id}', [PegawaiSuratController::class, 'update'])->name('surat.update'); 
    Route::delete('/surat/bulk-delete', [PegawaiSuratController::class, 'bulkDelete'])->name('surat.bulk_delete');

    Route::post('/surat/upload-pdf', [PegawaiSuratController::class, 'uploadPdf'])->name('surat.upload_pdf');
});

// ---------------- Pimpinan ----------------
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->group(function () {
    Route::get('/dashboard', [PimpinanSuratController::class, 'index'])->name('pimpinan.dashboard');
    Route::post('/surat/{id}/setujui', [PimpinanSuratController::class, 'setujui'])->name('pimpinan.surat.setujui');
    Route::post('/surat/{id}/tolak', [PimpinanSuratController::class, 'tolak'])->name('pimpinan.surat.tolak');
    Route::get('/disposisi', [PimpinanSuratController::class, 'disposisi'])->name('pimpinan.disposisi');
});
