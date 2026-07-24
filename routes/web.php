<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PotonganController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

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

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route - requires auth & verified
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes - requires auth
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Sistem Penggajian
|--------------------------------------------------------------------------
|
| Semua route di bawah ini memerlukan:
| - Authentication (auth)
| - Email verification (verified)
| - Role admin (role:admin)
|
*/

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Master Data Routes
    |--------------------------------------------------------------------------
    */
    
    // Jabatan Resource Routes
    Route::resource('jabatan', JabatanController::class);
    
    // Karyawan Resource Routes
    Route::resource('karyawan', KaryawanController::class);
    
    // Potongan Resource Routes
    Route::resource('potongan', PotonganController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Penggajian Routes
    |--------------------------------------------------------------------------
    */
    
    // Penggajian Resource Routes
    Route::resource('penggajian', PenggajianController::class);
    
    // Custom Penggajian Routes - Generate Bulk
    Route::prefix('penggajian')->name('penggajian.')->group(function () {
        // GET: Show form generate bulk penggajian
        Route::get('generate-bulk', [PenggajianController::class, 'generateBulk'])
            ->name('generateBulk');
        
        // POST: Process bulk generation
        Route::post('process-bulk-generate', [PenggajianController::class, 'processBulkGenerate'])
            ->name('processBulkGenerate');
        
        // PATCH: Update status penggajian (draft -> final -> dibayar)
        Route::patch('{penggajian}/update-status', [PenggajianController::class, 'updateStatus'])
            ->name('updateStatus');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Slip Gaji PDF Routes
    |--------------------------------------------------------------------------
    */
    
    // Slip Gaji Routes
    Route::prefix('slip-gaji')->name('slip-gaji.')->group(function () {
        // GET: Preview slip gaji PDF di browser
        Route::get('{penggajian}/preview', [SlipGajiController::class, 'preview'])
            ->name('preview');
        
        // GET: Download slip gaji PDF
        Route::get('{penggajian}/download', [SlipGajiController::class, 'download'])
            ->name('download');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Laporan Routes
    |--------------------------------------------------------------------------
    */
    
    // Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        // GET: Show laporan page
        Route::get('/', [LaporanController::class, 'index'])
            ->name('index');
        
        // GET: Export laporan to PDF
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])
            ->name('exportPdf');
        
        // GET: Export laporan to Excel
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])
            ->name('exportExcel');
    });
});

// Auth routes (login, register, password reset, etc.)
require __DIR__.'/auth.php';
