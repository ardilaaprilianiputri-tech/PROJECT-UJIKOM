<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. Import class middleware milikmu di bagian atas
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsPetugas;
use App\Http\Middleware\isPeminjam;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // <--- Ditambahkan agar rute API terbaca
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 2. Kecualikan route login dari verifikasi CSRF (Pencegah Error 419)
        $middleware->validateCsrfTokens(except: [
            'login',
            'logout',
        ]);

        // 3. Tambahkan daftar alias middleware role
        $middleware->alias([
            'admin' => IsAdmin::class,
            'petugas' => IsPetugas::class,
            'peminjam' => isPeminjam::class,
            'role' => isPeminjam::class, 
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();