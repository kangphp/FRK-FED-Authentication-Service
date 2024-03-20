<?php

namespace App\Http\Middleware;

use Closure;

class DefaultResponseMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Periksa jika status respons adalah 404 (Not Found)
        if ($response->status() == 404) {
            // Jika status respons adalah 404, kembalikan respons default
            return response()->json([
                'result' => false,
                'message' => 'Your URL that you requested is not found',
            ], 404);
        }

        return $response;
    }
}
