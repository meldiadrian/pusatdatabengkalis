<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeadersMiddleware
 *
 * Menerapkan HTTP Response Security Headers standar industri (OWASP, CWE-1021, BSSN).
 * Memitigasi kerentanan Clickjacking (UI Redressing), MIME Sniffing, dan pembocoran data.
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Mitigasi Clickjacking (CWE-1021)
        // Menolak embedding iframe dari domain luar (hanya izinkan same origin)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Content-Security-Policy frame-ancestors 'self'
        $csp = $response->headers->get('Content-Security-Policy');
        if ($csp) {
            if (!str_contains($csp, 'frame-ancestors')) {
                $response->headers->set('Content-Security-Policy', rtrim($csp, '; ') . "; frame-ancestors 'self'");
            }
        } else {
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        }

        // 2. Mitigasi MIME-type Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Proteksi XSS Filter pada browser lawas
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Kebijakan Referrer (mencegah kebocoran URL path sensitif ke pihak ketiga)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Pembatasan akses fitur browser / hardware yang tidak digunakan
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // 6. HTTP Strict Transport Security (HSTS) jika diakses via HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
