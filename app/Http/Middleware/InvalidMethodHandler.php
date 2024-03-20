<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InvalidMethodHandler
{
    public function handle(Request $request, Closure $next)
    {
        // Cek jika method HTTP tidak sesuai
        if (!in_array($request->method(), ['GET', 'POST'])) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        // Lanjutkan ke middleware berikutnya
        return $next($request);
    }
}
