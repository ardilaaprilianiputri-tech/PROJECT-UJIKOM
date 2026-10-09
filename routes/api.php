<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\PengembalianController;
use App\Http\Controllers\API\LaporanController; // <-- TAMBAHAN IMPORT
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsPetugas;
use App\Http\Middleware\IsPeminjam;

// Public Routes (Bisa diakses langsung di Postman tanpa token & tanpa login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// === TAMBAHAN KHUSUS PENGUJIAN TUGAS (Tanpa Auth / Bebas Akses) ===
Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::post('/peminjaman', [PeminjamanController::class, 'store']);
Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);
// ===============================================================

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Grup Admin (Ditambah rute index, show, update, destroy pengembalian)
    Route::middleware(IsAdmin::class)->group(function () {
        Route::apiResource('kategori', KategoriController::class);
        Route::post('/alat', [AdminController::class, 'storeAlat']);
        Route::put('/alat/{id}', [AdminController::class, 'updateAlat']);
        Route::apiResource('users', UserController::class);

        // Manajemen Pengembalian oleh Admin
        Route::get('/pengembalian', [PengembalianController::class, 'index']);
        Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
        Route::put('/pengembalian/{pengembalian}', [PengembalianController::class, 'update']);
        Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);

        // Rute Laporan Peminjaman (Bisa diakses Admin/Petugas)
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']); //
    });

    // Grup Petugas (Ditambah rute store untuk memproses pengembalian alat)
    Route::middleware(IsPetugas::class)->group(function () {
        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
        
        // Input/Proses Pengembalian oleh Petugas
        Route::post('/pengembalian', [PengembalianController::class, 'store']);

        // Rute Laporan Peminjaman (Bisa diakses Petugas juga)
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']); //[cite: 5]
    });

    // Grup Peminjam
    Route::middleware(IsPeminjam::class)->group(function () {
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);
        Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat']);
        
        // Peminjam bisa melihat riwayat/detail pengembalian miliknya sendiri
        Route::get('/pengembalian', [PengembalianController::class, 'index']);
        Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
    });
});