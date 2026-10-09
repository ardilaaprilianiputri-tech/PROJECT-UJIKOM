<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Import class middleware (Perhatikan huruf I besar pada IsPeminjam)
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsPetugas;
use App\Http\Middleware\IsPeminjam;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // Kecualikan route login dari verifikasi CSRF
        $middleware->validateCsrfTokens(except: [
            'login',
            'logout',
        ]);

        // Daftar alias middleware role
        $middleware->alias([
            'admin'      => IsAdmin::class,
            'petugas'    => IsPetugas::class,
            'peminjam'   => IsPeminjam::class,
            'isPeminjam' => IsPeminjam::class,
            'role'       => IsPeminjam::class, 
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();