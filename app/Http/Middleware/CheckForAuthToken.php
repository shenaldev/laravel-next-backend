<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class CheckForAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $COOKIE_NAME = env('AUTH_COOKIE_NAME');

        if (!$COOKIE_NAME) {
            throw new Exception('ENV variable AUTH_COOKIE_NAME not set');
        }

        $token = $request->cookie($COOKIE_NAME);
        $decToken = false;

        try {
            $decToken = Crypt::decryptString($token);
        } catch (DecryptException $e) {
            $decToken = false;
        }

        if ($decToken) {
            $request->headers->set('Authorization', 'Bearer ' . $decToken);
        }

        return $next($request);
    }
}
