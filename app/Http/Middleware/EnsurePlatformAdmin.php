<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isPlatformAdmin()) {
            return response()->json(['message' => 'Access denied. Platform admin only.'], 403);
        }

        return $next($request);
    }
}
