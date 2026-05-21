<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as AuthGuardMiddleware;
use Laravel\Passport\Http\Middleware\CheckClientCredentials as ClientCredMiddleware;

class MultiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        $auth_guard_middleware  = app()->make(AuthGuardMiddleware::class);

        try {
            $response = $auth_guard_middleware->handle($request, $next, 'api');

        } catch (AuthenticationException $e) {
            $client_cred_middleware = app()->make(ClientCredMiddleware::class);
            $response = $client_cred_middleware->handle($request, $next, ...$scopes);
        }

        return $response;
    }
}
