<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PotonganController;
use App\Http\Controllers\PenggajianController;
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
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});

// Auth routes (login, register, password reset, etc.)
require __DIR__.'/auth.php';
