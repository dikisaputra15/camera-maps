<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PelangganController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('auth.login');
});

Route::get('home', [DashboardController::class, 'index'])->name('home')->middleware(['auth']);
Route::resource('users', UserController::class)->middleware(['auth']);
Route::resource('pelanggan', PelangganController::class)->middleware(['auth']);
Route::get('searchdatapelanggan', [PelangganController::class, 'formsearch'])->middleware(['auth']);
Route::post('/search-pelanggan', [PelangganController::class, 'searchPelanggan'])->name('search.pelanggan')->middleware(['auth']);
Route::get('search-pelanggan/{id}/formupload', [PelangganController::class, 'formupload'])->middleware(['auth']);
Route::put('/pelanggans/{id}/update-gambar', [PelangganController::class, 'updateImages'])->name('pelanggans.update_images')->middleware(['auth']);
Route::put('/pelanggan/{id}/update-verified', [PelangganController::class, 'updateVerified'])->name('pelanggan.updateVerified');
Route::post('/pelangganimport', [PelangganController::class, 'import'])->name('plg.import')->middleware(['auth']);
Route::get('/pelangganexport', [PelangganController::class, 'exportExcel'])->name('plg.export')->middleware(['auth']);

