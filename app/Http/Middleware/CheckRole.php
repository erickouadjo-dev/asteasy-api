<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('check_role:ADMIN|SUPER_ADMIN')
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $utilisateur = $request->user();

        if (!$utilisateur) {
            return new JsonResponse([
                'code_http' => 401,
                'code_message' => 'ERR_UNAUTHORIZED',
                'erreurs' => 'Utilisateur non authentifié.'
            ], 401);
        }

        $expectedRoles = array_filter(array_map('trim', explode('|', $roles)));
        foreach ($expectedRoles as $role) {
            if ($utilisateur->hasRole($role)) {
                return $next($request);
            }
        }

        return new JsonResponse([
            'code_http' => 403,
            'code_message' => 'ERR_FORBIDDEN',
            'erreurs' => 'Accès refusé. Rôle requis: ' . $roles
        ], 403);
    }
}
