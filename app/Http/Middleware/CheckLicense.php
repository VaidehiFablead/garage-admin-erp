<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CheckLicense
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
