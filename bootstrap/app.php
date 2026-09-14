<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\BlockDangerousFileUpload;
use App\Http\Middleware\SecurityWafMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Proteksi WAF: SQL Injection & User-Agent filtering (Global Middleware)
        $middleware->prepend(SecurityWafMiddleware::class);

        // Proteksi Security Headers: Anti-Clickjacking (X-Frame-Options, CSP frame-ancestors, nosniff, dll)
        $middleware->prepend(SecurityHeadersMiddleware::class);

        // Blokir upload file berbahaya (PHP, script, exe, dll)
        // Berlaku untuk endpoint Livewire file upload yang digunakan Filament
        $middleware->appendToGroup('web', BlockDangerousFileUpload::class);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
