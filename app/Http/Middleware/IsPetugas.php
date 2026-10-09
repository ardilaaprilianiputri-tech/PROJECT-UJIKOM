<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPetugas
{
    public function handle(Request $request, Closure $next): Response
    {
        // Izinkan jika role user adalah 'petugas' ATAU 'admin'
        if ($request->user() && in_array($request->user()->role, ['petugas', 'admin'])) {
            return $next($request);
        }

        return response()->json(['message' => 'Akses ditolak. Anda bukan Petugas.'], 403);
    }
}