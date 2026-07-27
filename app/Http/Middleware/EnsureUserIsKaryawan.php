<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsKaryawan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'karyawan') {
            abort(403, 'Unauthorized. Hanya karyawan yang dapat mengakses halaman ini.');
        }

        // Pastikan user memiliki data karyawan
        if (!$request->user()->karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        return $next($request);
    }
}
