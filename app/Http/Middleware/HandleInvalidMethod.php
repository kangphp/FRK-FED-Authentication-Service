<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
class HandleInvalidMethod
{
    public function handle(Request $request, Closure $next)
    {
        // Periksa jika method yang digunakan bukan POST
        if ($request->method() !== 'POST') {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        // Lanjutkan ke middleware berikutnya
        return $next($request);
    }
}
