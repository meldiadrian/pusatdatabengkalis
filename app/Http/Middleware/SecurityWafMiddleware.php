<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * SecurityWafMiddleware
 *
 * Web Application Firewall (WAF) Middleware.
 * Melindungi aplikasi dari serangan SQL Injection (SQLi) dan
 * memvalidasi HTTP User-Agent untuk memblokir scanner / exploit toolkit berbahaya.
 *
 * Fitur Keamanan:
 * 1. User-Agent Validation (Empty/Missing, Max Length, Control/Null characters, Scanner Blacklist).
 * 2. SQL Injection Inspection (Union, Boolean/Tautology, Stacked Queries, Blind/Time-based, Schema Probing).
 * 3. Multi-pass Decoding & Sanitization (menangkal URL double encoding dan null byte bypass).
 * 4. Audit Trail Logging lengkap (IP, Method, URI, User-Agent, Parameter, Threat Signature).
 * 5. Clean 403 Forbidden Response tanpa membocorkan info sistem internal.
 */
class SecurityWafMiddleware
{
    /**
     * Blacklist User-Agent scanner, exploit toolkit, dan malicious bot.
     */
    private const BLOCKED_USER_AGENTS = [
        // Penetration Testing & Vulnerability Scanners
        'sqlmap',
        'nikto',
        'acunetix',
        'nessus',
        'openvas',
        'w3af',
        'arachni',
        'nmap',
        'masscan',
        'zgrab',
        'dirbuster',
        'gobuster',
        'ffuf',
        'wfuzz',
        'hydra',
        'burpsuite',
        'metasploit',
        'havij',
        'pangolin',
        'commix',
        'whatweb',
        'wpscan',
        'joomscan',
        'scalp',
        'nuclei',

        // Security Reconnaissance, Extension Scanners & Client Manipulation
        'dotgit',
        'findsomething',
        'foxyproxy',
        'retri.js',
        'tampermonkey',
        'wappalyzer',

        // Malicious Scrapers & Exploit Frameworks
        'binlar',
        'casper',
        'cmsworldmap',
        'diavol',
        'dotbot',
        'feedfinder',
        'flicky',
        'ia_archiver',
        'jakarta',
        'kmccrew',
        'miner',
        'morfeus',
        'planetwork',
        'pycurl',
        'skygrid',
        'snoopy',
        'turnitinbot',
        'vikspider',
        'winhttp',
        'zmeu',
        'libwww-perl',
    ];

    /**
     * Pola deteksi signature serangan SQL Injection (SQLi).
     */
    private const SQLI_PATTERNS = [
        // 1. Classic UNION SELECT injection
        '/\bunion\s+(?:all\s+)?select\b/i',

        // 2. Boolean-based / Tautology injections (e.g. ' OR '1'='1', ' OR 1=1, OR true=true)
        '/\b(?:or|and)\s+[\'\"]?([a-zA-Z0-9_-]+)[\'\"]?\s*=\s*[\'\"]?\1[\'\"]?/i',
        '/\b(?:or|and)\s+(?:true\s*=\s*true|\d+\s*=\s*\d+)/i',

        // 3. Comment termination paired with SQL keywords or quotes
        '/(?:--|\#|\/\*).*?(?:select|union|insert|update|delete|drop|alter)/is',
        '/\b(?:select|union|insert|update|delete|drop|truncate|alter|create|exec|declare)\b.*?(?:--|\#|\/\*)/is',
        '/\'\s*(?:--|\#|\/\*)/',

        // 4. Stacked queries (e.g. ; DROP TABLE users; ; DELETE FROM ...)
        '/;\s*(?:select|insert|update|delete|drop|truncate|alter|create|grant|revoke|exec|declare)\b/i',

        // 5. Schema & System Information probing
        '/\b(?:from|join)\s+(?:information_schema|performance_schema|mysql\.|pg_catalog|sys\.objects|sysobjects|sysdatabases)\b/i',

        // 6. Time-based Blind SQLi & Heavy Functions
        '/\b(?:sleep|benchmark|waitfor\s+delay|pg_sleep)\s*\(\s*\d+/i',
        '/\b(?:extractvalue|updatexml)\s*\(/i',
        '/\b(?:schema|database|version|user|current_user|session_user|system_user)\s*\(\s*\)/i',
        '/\b(?:load_file|into\s+outfile|into\s+dumpfile)\b/i',

        // 7. Hex / Char encoding injections
        '/\b(?:unhex|hex)\s*\(/i',
        '/0x[0-9a-fA-F]{8,}/',
        '/\bchar\s*\(\s*\d+\s*(?:,\s*\d+\s*)*\)/i',

        // 8. Conditional blind SQLi statements
        '/\bcase\s+when\b.*?\bthen\b.*?\belse\b/is',
        '/\bif\s*\(\s*\(?\s*(?:select|version|user|\d+\s*=\s*\d+)/i',

        // 9. SQL Data Exfiltration functions
        '/\b(?:group_concat|concat_ws)\s*\(/i',
    ];

    /**
     * Parameter yang dikecualikan dari scanning SQLi (misal field password dan token).
     */
    private const EXCLUDED_PARAMS = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        '_token',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass endpoint health check aplikasi
        if ($request->is('up')) {
            return $next($request);
        }

        // ── 1. PROTEKSI USER-AGENT ───────────────────────────────────────────
        $userAgent = $request->userAgent();

        // 1a. Blokir jika User-Agent kosong / hanya whitespace (kecuali saat unit testing)
        if (empty($userAgent) || trim($userAgent) === '') {
            if (!app()->environment('testing')) {
                $this->logSecurityThreat($request, 'EMPTY_USER_AGENT', 'User-Agent header is missing or empty.');
                return $this->blockResponse($request, 'Akses ditolak: User-Agent tidak valid.');
            }
        } else {
            // 1b. Blokir jika panjang User-Agent abnormal (> 500 karakter)
            if (strlen($userAgent) > 500) {
                $this->logSecurityThreat($request, 'OVERSIZED_USER_AGENT', 'User-Agent exceeds maximum allowed length.');
                return $this->blockResponse($request, 'Akses ditolak: User-Agent melampaui batas yang diizinkan.');
            }

            // 1c. Blokir karakter kontrol / CRLF injection / null bytes dalam User-Agent
            if (preg_match('/[\r\n\0]/', $userAgent)) {
                $this->logSecurityThreat($request, 'MALFORMED_USER_AGENT', 'User-Agent contains control/CRLF/null characters.');
                return $this->blockResponse($request, 'Akses ditolak: Format User-Agent berbahaya.');
            }

            // 1d. Cek blacklist signature scanner / exploit tools
            $lowerUserAgent = strtolower($userAgent);
            foreach (self::BLOCKED_USER_AGENTS as $blocked) {
                if (str_contains($lowerUserAgent, $blocked)) {
                    $this->logSecurityThreat($request, 'MALICIOUS_USER_AGENT', "Blocked tool signature detected: {$blocked}");
                    return $this->blockResponse($request, 'Akses ditolak: Scanner atau automated tools terdeteksi.');
                }
            }
        }

        // ── 2. PROTEKSI SQL INJECTION (SQLi) ──────────────────────────────────

        // 2a. Scan URI & Query String (raw & URL decoded)
        $rawUri = $request->getRequestUri();
        if ($this->detectSqlInjection($rawUri, $matchedPattern)) {
            $this->logSecurityThreat($request, 'SQLI_IN_URI', "Matched pattern: {$matchedPattern} in URI: {$rawUri}");
            return $this->blockResponse($request, 'Akses ditolak: Pola serangan SQL Injection terdeteksi pada URL.');
        }

        // 2b. Scan seluruh input request (GET, POST, Route Parameters) secara rekursif
        $allInputs = $request->all();
        if ($this->scanInputsForSqli($allInputs, $violatingParam, $matchedPattern)) {
            $this->logSecurityThreat($request, 'SQLI_IN_INPUT', "Matched pattern: {$matchedPattern} in param: {$violatingParam}");
            return $this->blockResponse($request, 'Akses ditolak: Pola serangan SQL Injection terdeteksi pada data input.');
        }

        return $next($request);
    }

    /**
     * Rekursif scan seluruh input array/string untuk mendeteksi SQL Injection.
     */
    private function scanInputsForSqli(array $inputs, ?string &$violatingParam = null, ?string &$matchedPattern = null): bool
    {
        foreach ($inputs as $key => $value) {
            // Lewati field kredensial dan token
            if (in_array($key, self::EXCLUDED_PARAMS, true)) {
                continue;
            }

            if (is_array($value)) {
                if ($this->scanInputsForSqli($value, $violatingParam, $matchedPattern)) {
                    $violatingParam = $key . '.' . $violatingParam;
                    return true;
                }
            } elseif (is_string($value)) {
                if ($this->detectSqlInjection($value, $matchedPattern)) {
                    $violatingParam = (string) $key;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Evaluasi apakah string mengandung pola SQL Injection.
     * Menggunakan normalisasi null byte dan multi-pass URL decoding untuk menangkal bypass.
     */
    private function detectSqlInjection(string $input, ?string &$matchedPattern = null): bool
    {
        // Normalisasi null bytes
        $cleaned = str_replace(["\0", "\x00"], '', $input);

        // Multi-pass URL decoding (menangani double encoding seperti %2527 -> %27 -> ')
        $decodedOnce = urldecode($cleaned);
        $decodedTwice = urldecode($decodedOnce);

        $variants = [
            $cleaned,
            $decodedOnce,
            $decodedTwice,
        ];

        foreach ($variants as $variant) {
            foreach (self::SQLI_PATTERNS as $pattern) {
                if (preg_match($pattern, $variant)) {
                    $matchedPattern = $pattern;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Catat log insiden keamanan ke sistem log aplikasi.
     */
    private function logSecurityThreat(Request $request, string $threatType, string $details): void
    {
        Log::critical("[SECURITY WAF BLOCKED] {$threatType}", [
            'threat_type' => $threatType,
            'details'     => $details,
            'ip'          => $request->ip(),
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
            'user_agent'  => $request->userAgent() ?? 'EMPTY',
            'timestamp'   => now()->toIso8601String(),
        ]);
    }

    /**
     * Respon terstandarisasi HTTP 403 Forbidden.
     */
    private function blockResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error'   => 'Forbidden',
                'message' => $message,
                'code'    => 403,
            ], 403);
        }

        return response(
            "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>403 Forbidden - Akses Ditolak</title><meta name='robots' content='noindex,nofollow'><style>body{font-family:system-ui,-apple-system,sans-serif;text-align:center;padding:60px 20px;background:#0f172a;color:#e2e8f0}h1{font-size:32px;color:#f87171;margin-bottom:12px}p{font-size:16px;color:#94a3b8;max-width:500px;margin:0 auto 20px}.card{background:#1e293b;border:1px solid #334155;border-radius:12px;padding:32px;display:inline-block;box-shadow:0 10px 25px -5px rgba(0,0,0,0.5)}</style></head><body><div class='card'><h1>403 Forbidden</h1><p>{$message}</p><p style='font-size:12px;color:#64748b'>Permintaan Anda telah diblokir oleh sistem keamanan demi menjaga integritas data.</p></div></body></html>",
            403,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }
}
