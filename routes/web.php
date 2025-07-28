<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DisposisiController;
//use App\Http\Controllers\Admin\AdminSuratController;
use App\Http\Controllers\Admin\SuratController as AdminSuratController;
use App\Http\Controllers\Pegawai\SuratController as PegawaiSuratController;
use App\Http\Controllers\Pimpinan\SuratController as PimpinanSuratController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pimpinan\SuratController;



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
    Route::get('/laporan-surat', [UserController::class, 'laporan'])->name('admin.laporan');
    Route::get('/disposisi', [DisposisiController::class, 'form'])->name('admin.disposisi.form');
    Route::post('/disposisi', [DisposisiController::class, 'kirim'])->name('admin.disposisi.kirim');
    Route::get('/disposisi-masuk', [AdminSuratController::class, 'disposisiMasuk'])->name('admin.disposisi.masuk');
    Route::get('/disposisi/{id}/form', [AdminSuratController::class, 'formDisposisi'])->name('admin.surat.disposisi.form');
    Route::post('/disposisi/{id}/kirim', [AdminSuratController::class, 'kirimDisposisi'])->name('admin.surat.disposisi.kirim');

    Route::get('/admin/surat/masuk', [App\Http\Controllers\Admin\SuratController::class, 'suratMasuk'])
        ->name('admin.surat.masuk')
        ->middleware(['auth', 'role:admin']);
    Route::get('/surat-dari-pimpinan', [App\Http\Controllers\Admin\AdminSuratController::class, 'index'])->name('admin.surat.dari-pimpinan');
    Route::get('/surat-dari-pimpinan/{id}', [App\Http\Controllers\Admin\AdminSuratController::class, 'lihat'])->name('admin.surat.dari-pimpinan.lihat');

    // routes/web.php

    // Ensure this is within your admin middleware group, e.g.:
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // ... other admin routes ...

        // Route for Admin to download a letter PDF
        Route::get('/surat/{surat}/download', [App\Http\Controllers\Admin\SuratController::class, 'download'])->name('surat.download');

        // ... other admin routes ...
    });
});

// ---------------- Pegawai ----------------
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('pegawai.dashboard');
    Route::get('/status_surat', [PegawaiSuratController::class, 'statusSurat'])->name('surat.status_surat');
    Route::post('/surat', [PegawaiSuratController::class, 'store'])->name('surat.store');
    Route::get('/surat', [PegawaiSuratController::class, 'index'])->name('surat.index');

    Route::get('/surat/create', [PegawaiSuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [PegawaiSuratController::class, 'store'])->name('surat.store');

    Route::get('/surat/{id}/preview', [PegawaiSuratController::class, 'preview'])->name('surat.preview');
    Route::get('/surat/{id}/download', [PegawaiSuratController::class, 'download'])->name('surat.download');
    Route::get('/surat/{id}/edit', [PegawaiSuratController::class, 'edit'])->name('surat.edit');
    Route::put('/surat/{id}', [PegawaiSuratController::class, 'update'])->name('surat.update');
    Route::delete('/surat/{id}', [PegawaiSuratController::class, 'destroy'])->name('surat.destroy');
    Route::delete('/surat/bulk-delete', [PegawaiSuratController::class, 'bulkDelete'])->name('surat.bulk_delete');

    Route::post('/surat/upload-pdf', [PegawaiSuratController::class, 'uploadPdf'])->name('surat.upload_pdf');

    Route::post('/surat/ajukan', [PegawaiSuratController::class, 'ajukan'])->name('surat.ajukan');
});

// ---------------- Pimpinan ----------------
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->group(function () {
    Route::get('/dashboard', [PimpinanSuratController::class, 'index'])->name('pimpinan.dashboard');
    Route::post('/surat/{id}/setujui', [PimpinanSuratController::class, 'setujui'])->name('pimpinan.surat.setujui');
    Route::post('/surat/{id}/tolak', [PimpinanSuratController::class, 'tolak'])->name('pimpinan.surat.tolak');
    Route::get('/disposisi', [PimpinanSuratController::class, 'disposisi'])->name('pimpinan.disposisi');
    Route::get('/surat/{id}/preview', [PimpinanSuratController::class, 'preview'])->name('pimpinan.surat.preview');
    Route::delete('/pimpinan/surat/{id}', [SuratController::class, 'destroy'])->name('pimpinan.surat.destroy');


    Route::get('/balasansurat', [PimpinanSuratController::class, 'balasanSurat'])->name('pimpinan.balasansurat');
    Route::post('/pimpinan/surat/{id}/balas', [SuratController::class, 'kirimBalasan'])->name('pimpinan.surat.balas');
    Route::get('/surat/{surat}/balas', [SuratController::class, 'buatBalasan'])->name('surat.balas');
    Route::post('/surat/balas', [SuratController::class, 'simpanBalasan'])->name('surat.balas.store');
    Route::post('/pimpinan/surat-balasan/simpan', [SuratController::class, 'simpanSuratBalasan'])->name('pimpinan.surat-balasan.simpan');
    Route::post('/pimpinan/surat/balasan', [SuratController::class, 'simpanSuratBalasan'])->name('pimpinan.surat.balasan.simpan');
    Route::get('/pimpinan/status-surat', [SuratController::class, 'statusSurat'])->name('pimpinan.status-surat');
    Route::post('/pimpinan/kirim-surat/{id}', [SuratController::class, 'kirimSurat'])->name('pimpinan.kirim-surat');
    Route::get('/pimpinan/statussurat', [App\Http\Controllers\Pimpinan\SuratController::class, 'statusSurat'])->name('pimpinan.statussurat');
    Route::get('/pimpinan/surat/{id}/view', [App\Http\Controllers\Pimpinan\SuratController::class, 'view'])->name('pimpinan.surat.view');

    // Inside your routes/web.php file, within the appropriate group (e.g., 'pimpinan' middleware group)

    // Route for displaying the edit form
    Route::get('/pimpinan/surat/{surat}/edit', [App\Http\Controllers\Pimpinan\SuratController::class, 'edit'])->name('pimpinan.surat.edit');

    // Route for handling the update submission
    Route::put('/pimpinan/surat/{surat}', [App\Http\Controllers\Pimpinan\SuratController::class, 'update'])->name('pimpinan.surat.update');
    // routes/web.php
    Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
        // ... rute pimpinan lainnya ...

        // Rute baru untuk mengunduh surat oleh pimpinan
        Route::get('/surat/{surat}/download', [App\Http\Controllers\Pimpinan\SuratController::class, 'download'])->name('surat.download');
    });

});


// ---------------- Profile ----------------
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('reset.password.form');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');
