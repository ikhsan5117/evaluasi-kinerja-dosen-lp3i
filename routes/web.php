<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\DosenEvaluasiController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\MahasiswaHistoryController;
use App\Http\Controllers\MahasiswaKuesionerController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MasterDataImportController;
use Illuminate\Support\Facades\Route;

// Public / Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Autocomplete API endpoints for dynamic login
Route::get('/auth/autocomplete/kelas', [AuthController::class, 'autocompleteKelas'])->name('auth.autocomplete.kelas');
Route::get('/auth/autocomplete/mahasiswa', [AuthController::class, 'autocompleteMahasiswa'])->name('auth.autocomplete.mahasiswa');
Route::get('/auth/autocomplete/dosen', [AuthController::class, 'autocompleteDosen'])->name('auth.autocomplete.dosen');
Route::get('/auth/autocomplete/user', [AuthController::class, 'autocompleteUser'])->name('auth.autocomplete.user');

// Authenticated Routes (All Roles)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // === ADMIN ROUTES ===
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::get('/master', [MasterDataController::class, 'index'])->name('master.index');
        Route::get('/master/template/{type}', [MasterDataImportController::class, 'downloadTemplate'])->name('master.template.download');
        
        // Master Data Import Excel
        Route::post('/master/import/mahasiswa', [MasterDataImportController::class, 'importMahasiswa'])->name('master.import.mahasiswa');
        Route::post('/master/import/dosen', [MasterDataImportController::class, 'importDosen'])->name('master.import.dosen');
        Route::post('/master/import/matkul', [MasterDataImportController::class, 'importMataKuliah'])->name('master.import.matkul');
        Route::post('/master/import/kelas', [MasterDataImportController::class, 'importKelas'])->name('master.import.kelas');
        Route::post('/master/import/periode', [MasterDataImportController::class, 'importPeriode'])->name('master.import.periode');

        Route::post('/master/dosen', [MasterDataController::class, 'storeDosen'])->name('master.dosen.store');
        Route::put('/master/dosen/{dosen}', [MasterDataController::class, 'updateDosen'])->name('master.dosen.update');
        Route::delete('/master/dosen/{dosen}', [MasterDataController::class, 'destroyDosen'])->name('master.dosen.destroy');

        Route::post('/master/mahasiswa', [MasterDataController::class, 'storeMahasiswa'])->name('master.mahasiswa.store');
        Route::put('/master/mahasiswa/{mahasiswa}', [MasterDataController::class, 'updateMahasiswa'])->name('master.mahasiswa.update');
        Route::delete('/master/mahasiswa/{mahasiswa}', [MasterDataController::class, 'destroyMahasiswa'])->name('master.mahasiswa.destroy');

        Route::post('/master/periode', [MasterDataController::class, 'storePeriode'])->name('master.periode.store');
        Route::put('/master/periode/{periode}', [MasterDataController::class, 'updatePeriode'])->name('master.periode.update');
        Route::delete('/master/periode/{periode}', [MasterDataController::class, 'destroyPeriode'])->name('master.periode.destroy');

        Route::post('/master/matkul', [MasterDataController::class, 'storeMataKuliah'])->name('master.matkul.store');
        Route::put('/master/matkul/{matkul}', [MasterDataController::class, 'updateMataKuliah'])->name('master.matkul.update');
        Route::delete('/master/matkul/{matkul}', [MasterDataController::class, 'destroyMataKuliah'])->name('master.matkul.destroy');

        Route::post('/master/kelas', [MasterDataController::class, 'storeKelas'])->name('master.kelas.store');
        Route::put('/master/kelas/{kela}', [MasterDataController::class, 'updateKelas'])->name('master.kelas.update');
        Route::delete('/master/kelas/{kela}', [MasterDataController::class, 'destroyKelas'])->name('master.kelas.destroy');

        // Kuesioner & Pertanyaan
        Route::get('/kuesioner', [KuesionerController::class, 'index'])->name('kuesioner.index');
        Route::get('/kuesioner/template-pertanyaan', [KuesionerController::class, 'downloadTemplate'])->name('kuesioner.template.download');
        Route::post('/kuesioner', [KuesionerController::class, 'store'])->name('kuesioner.store');
        Route::get('/kuesioner/{kuesioner}', [KuesionerController::class, 'show'])->name('kuesioner.show');
        Route::put('/kuesioner/{kuesioner}', [KuesionerController::class, 'update'])->name('kuesioner.update');
        Route::delete('/kuesioner/{kuesioner}', [KuesionerController::class, 'destroy'])->name('kuesioner.destroy');
        Route::patch('/kuesioner/{kuesioner}/toggle', [KuesionerController::class, 'toggleStatus'])->name('kuesioner.toggle');

        Route::post('/kuesioner/{kuesioner}/pertanyaan', [KuesionerController::class, 'storePertanyaan'])->name('pertanyaan.store');
        Route::post('/kuesioner/{kuesioner}/import-pertanyaan', [KuesionerController::class, 'importPertanyaan'])->name('kuesioner.import.pertanyaan');
        Route::put('/pertanyaan/{pertanyaan}', [KuesionerController::class, 'updatePertanyaan'])->name('pertanyaan.update');
        Route::delete('/pertanyaan/{pertanyaan}', [KuesionerController::class, 'destroyPertanyaan'])->name('pertanyaan.destroy');

        // Laporan & Export
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export_excel');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export_pdf');
    });

    // === DOSEN ROUTES ===
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');
        Route::get('/evaluasi', [DosenEvaluasiController::class, 'index'])->name('evaluasi.index');
        Route::get('/evaluasi/{kelas}', [DosenEvaluasiController::class, 'detail'])->name('evaluasi.detail');
    });

    // === MAHASISWA ROUTES ===
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kuesioner', [MahasiswaKuesionerController::class, 'index'])->name('kuesioner.index');
        Route::get('/kuesioner/{kelas}/isi', [MahasiswaKuesionerController::class, 'fill'])->name('kuesioner.fill');
        Route::post('/kuesioner/{kelas}/isi', [MahasiswaKuesionerController::class, 'store'])->name('kuesioner.store');
        Route::get('/kuesioner/{kelas}/sukses', [MahasiswaKuesionerController::class, 'success'])->name('kuesioner.success');

        Route::get('/history', [MahasiswaHistoryController::class, 'index'])->name('history.index');
        Route::get('/history/{evaluasi}', [MahasiswaHistoryController::class, 'show'])->name('history.show');
    });
});
