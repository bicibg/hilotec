<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate limits admin login attempts to prevent brute-force attacks.
 *
 * Default: 5 POST attempts per minute per IP.
 *
 * To use with Filament, add to AdminPanelProvider middleware array,
 * or apply on the login route directly.
 */
class ThrottleAdminLogin
{
    public function __construct(private RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            $key = 'admin-login:' . $request->ip();

            if ($this->limiter->tooManyAttempts($key, 5)) {
                $seconds = $this->limiter->availableIn($key);

                abort(429, "Too many login attempts. Please try again in {$seconds} seconds.");
            }

            $this->limiter->hit($key, 60);
        }

        return $next($request);
    }
}
