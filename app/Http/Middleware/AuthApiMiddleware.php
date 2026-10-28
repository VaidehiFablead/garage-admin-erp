<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Accept either a valid Passport Bearer token (API guard)
        // or an active web session — so web pages work seamlessly.
        $isApi = false;
        try {
            $isApi = Auth::guard('api')->check();
        } catch (\Throwable $e) {
            $isApi = false;
        }

        $isWeb = false;
        try {
            $isWeb = Auth::guard('web')->check();
        } catch (\Throwable $e) {
            $isWeb = false;
        }

        if (! ($isApi || $isWeb)) {
            return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

