<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * BlockDangerousFileUpload
 *
 * Middleware keamanan berlapis untuk mencegah upload file berbahaya
 * seperti PHP webshell, script executable, dan file konfigurasi server.
 *
 * Diletakkan SEBELUM request masuk ke controller Filament / Livewire,
 * sebagai lapisan pertahanan pertama (first line of defense).
 */
class BlockDangerousFileUpload
{
    /**
     * Ekstensi file yang DILARANG diupload ke sistem ini.
     * Mencakup: PHP variants, script server-side, executable, konfigurasi server.
     */
    private const BLOCKED_EXTENSIONS = [
        // PHP dan variannya — paling berbahaya (webshell / RCE)
        'php', 'php2', 'php3', 'php4', 'php5', 'php6', 'php7', 'php8',
        'phtml', 'phar', 'phps', 'php-s', 'pht', 'pgif',

        // Script server-side lain
        'asp', 'aspx', 'ascx', 'ashx', 'asmx', 'axd',
        'jsp', 'jspx', 'jspf',
        'cfm', 'cfml',
        'cgi', 'pl', 'py', 'rb', 'lua',
        'sh', 'bash', 'zsh', 'fish', 'ksh',

        // Executable & binary berbahaya
        'exe', 'bat', 'cmd', 'com', 'scr', 'pif', 'vbs', 'vbe',
        'ws', 'wsf', 'wsc', 'wsh', 'ps1', 'ps1xml', 'ps2', 'ps2xml',
        'msi', 'dll', 'so', 'dylib',

        // Konfigurasi server yang bisa di-hijack
        'htaccess', 'htpasswd',
        'ini', 'conf', 'config', 'cfg',

        // File web yang bisa eksekusi JavaScript (XSS)
        'svg', 'xml', 'xsl', 'xslt',
        'html', 'htm', 'shtml', 'xhtml',
        'js', 'mjs', 'cjs', 'ts',

        // Lainnya yang berpotensi berbahaya
        'jar', 'war', 'ear',
        'swf', 'fla',
    ];

    /**
     * MIME types yang DIIZINKAN (whitelist ketat).
     * Hanya PDF, Excel, JPEG, PNG.
     */
    private const ALLOWED_MIMES = [
        'application/pdf',
        'application/vnd.ms-excel',                                           // .xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',  // .xlsx
        'image/jpeg',
        'image/png',
    ];

    /**
     * Ekstensi yang DIIZINKAN (whitelist ketat).
     */
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'xls', 'xlsx', 'jpg', 'jpeg', 'png',
    ];

    /**
     * Pola konten berbahaya yang wajib ditolak (walau ekstensinya aman).
     * Ini menangkap teknik rename PHP ke .jpg / .pdf.
     */
    private const DANGEROUS_CONTENT_PATTERNS = [
        '<?php', '<?=', '<? ',
        '<%', '%>',                    // ASP tag
        'eval(', 'exec(', 'system(',
        'passthru(', 'shell_exec(',
        'popen(', 'proc_open(',
        'base64_decode(', 'assert(',
        'preg_replace(', 'call_user_func(',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Iterasi semua file yang diupload dalam request ini
        foreach ($request->allFiles() as $files) {
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                if (!($file instanceof \Illuminate\Http\UploadedFile)) {
                    continue;
                }

                $this->validateUploadedFile($file, $request);
            }
        }

        return $next($request);
    }

    /**
     * Validasi file yang diupload secara ketat.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function validateUploadedFile(\Illuminate\Http\UploadedFile $file, Request $request): void
    {
        $originalName = $file->getClientOriginalName();
        $ext          = strtolower($file->getClientOriginalExtension());
        $mimeType     = $file->getMimeType();
        $ip           = $request->ip();

        // ── CEK 1: Ekstensi harus ada dan tidak boleh kosong ──────────────────
        if (empty($ext)) {
            Log::warning('[UPLOAD BLOCKED] File tanpa ekstensi ditolak', [
                'ip'   => $ip,
                'name' => $originalName,
                'mime' => $mimeType,
            ]);
            abort(422, 'File tanpa ekstensi tidak diizinkan.');
        }

        // ── CEK 2: Tolak ekstensi yang masuk daftar hitam ─────────────────────
        if (in_array($ext, self::BLOCKED_EXTENSIONS)) {
            Log::warning("[UPLOAD BLOCKED] Ekstensi berbahaya ditolak: .{$ext}", [
                'ip'   => $ip,
                'name' => $originalName,
                'mime' => $mimeType,
            ]);
            abort(422, "File dengan ekstensi '.{$ext}' tidak diizinkan demi keamanan sistem.");
        }

        // ── CEK 3: Ekstensi harus ada di whitelist ────────────────────────────
        if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
            Log::warning("[UPLOAD BLOCKED] Ekstensi tidak ada di whitelist: .{$ext}", [
                'ip'   => $ip,
                'name' => $originalName,
                'mime' => $mimeType,
            ]);
            abort(422, 'Hanya file PDF, Excel (.xls/.xlsx), JPG, JPEG, dan PNG yang diizinkan.');
        }

        // ── CEK 4: MIME type harus ada di whitelist ───────────────────────────
        if (!in_array($mimeType, self::ALLOWED_MIMES)) {
            Log::warning("[UPLOAD BLOCKED] MIME type tidak diizinkan: {$mimeType}", [
                'ip'   => $ip,
                'name' => $originalName,
                'ext'  => $ext,
            ]);
            abort(422, "Jenis file '{$mimeType}' tidak diizinkan oleh sistem.");
        }

        // ── CEK 5: Deteksi double extension (misal: shell.php.jpg) ────────────
        $nameParts = explode('.', $originalName);
        if (count($nameParts) > 2) {
            // Periksa apakah ada ekstensi PHP/script tersembunyi di bagian tengah
            foreach (array_slice($nameParts, 1, -1) as $hiddenExt) {
                if (in_array(strtolower($hiddenExt), self::BLOCKED_EXTENSIONS)) {
                    Log::warning("[UPLOAD BLOCKED] Double extension berbahaya terdeteksi: {$originalName}", [
                        'ip'         => $ip,
                        'hidden_ext' => $hiddenExt,
                    ]);
                    abort(422, "File '{$originalName}' mengandung ekstensi tersembunyi yang tidak diizinkan.");
                }
            }
        }

        // ── CEK 6: Scan konten file untuk pola skrip berbahaya ────────────────
        // Menangkap file yang di-rename (misal shell.php → surat.pdf)
        $realPath = $file->getRealPath();
        if ($realPath && is_readable($realPath)) {
            $handle  = fopen($realPath, 'r');
            $content = fread($handle, 8192); // Baca 8KB pertama (hemat memori)
            fclose($handle);

            foreach (self::DANGEROUS_CONTENT_PATTERNS as $pattern) {
                if (stripos($content, $pattern) !== false) {
                    Log::critical('[UPLOAD BLOCKED] Konten berbahaya terdeteksi dalam file', [
                        'ip'      => $ip,
                        'name'    => $originalName,
                        'pattern' => $pattern,
                    ]);
                    abort(422, 'File terdeteksi mengandung kode berbahaya dan ditolak oleh sistem keamanan.');
                }
            }
        }
    }
}
