<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('check_permission:EDIT_USERS|VIEW_REPORTS')
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        $utilisateur = $request->user();

        if (!$utilisateur) {
            return new JsonResponse([
                'code_http' => 401,
                'code_message' => 'ERR_UNAUTHORIZED',
                'erreurs' => 'Utilisateur non authentifié.'
            ], 401);
        }

        $expectedPermissions = array_filter(array_map('trim', explode('|', $permissions)));
        foreach ($expectedPermissions as $permission) {
            if ($utilisateur->hasPermission($permission)) {
                return $next($request);
            }
        }

        return new JsonResponse([
            'code_http' => 403,
            'code_message' => 'ERR_FORBIDDEN',
            'erreurs' => 'Accès refusé. Permission requise: ' . $permissions
        ], 403);
    }
}
