<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds comprehensive security headers to all HTTP responses.
 *
 * Register in bootstrap/app.php:
 *   $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
 *
 * CSP is NOT applied to admin/livewire routes (Filament needs inline styles/scripts).
 * Edit publicCsp() to match your site's CDN, analytics, and third-party domains.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        // --- Core security headers ---

        // Prevent clickjacking - only this site can frame itself
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing - stops browsers executing uploaded files as scripts
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS Protection (legacy browsers that don't support CSP)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Don't leak full URLs to external sites via Referer header
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Force HTTPS for 1 year, include subdomains, allow HSTS preload list submission
        if ($request->secure() || config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Disable browser features the site doesn't need
        $response->headers->set('Permissions-Policy', implode(', ', [
            'camera=()',
            'microphone=()',
            'geolocation=()',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
        ]));

        // Cross-origin isolation
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // --- Content Security Policy (public pages only) ---
        if (! $request->is('admin/*') && ! $request->is('livewire/*')) {
            $response->headers->set('Content-Security-Policy', $this->publicCsp());
        }

        // --- Admin-specific headers ---
        if ($request->is('admin/*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        }

        return $response;
    }

    /**
     * Content Security Policy for public-facing pages.
     *
     * CUSTOMIZE THIS for your site:
     * - Add CDN domains to script-src/style-src
     * - Add analytics domains to connect-src
     * - Add payment frames to frame-src
     * - Remove Google domains if you don't use Analytics/reCAPTCHA
     */
    private function publicCsp(): string
    {
        return implode('; ', [
            // Default: only same-origin resources
            "default-src 'self'",

            // Scripts: self + Google (Analytics, reCAPTCHA, Tag Manager)
            // Add your CDN domains here if needed
            "script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://www.google-analytics.com https://www.googletagmanager.com",

            // Styles: self + Google Fonts + inline (needed for Tailwind/Alpine)
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",

            // Images: self + data URIs + any HTTPS source
            "img-src 'self' data: https:",

            // Fonts: self + Google Fonts CDN
            "font-src 'self' https://fonts.gstatic.com",

            // Frames: only Google (reCAPTCHA challenge iframe)
            "frame-src 'self' https://www.google.com",

            // AJAX/fetch connections
            "connect-src 'self' https://www.google-analytics.com https://www.google.com",

            // Forms can ONLY submit to same origin - prevents form hijacking
            "form-action 'self'",

            // Locks <base> tag to prevent base URI injection attacks
            "base-uri 'self'",

            // No plugins (Flash, Java applets, etc.)
            "object-src 'none'",

            // Only this site can embed this page in a frame
            "frame-ancestors 'self'",

            // Auto-upgrade HTTP to HTTPS
            "upgrade-insecure-requests",
        ]);
    }
}
