<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }

        if (!$request->user()->hasRole($role)) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return $next($request);
    }
}