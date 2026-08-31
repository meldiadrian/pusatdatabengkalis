<?php

namespace App\Providers\Filament;

use App\Filament\Resources\PemohonResource;
use App\Filament\Resources\PemilikDataResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UnitKerjaResource;
use App\Filament\Resources\AplikasiPerangkatDaerahResource;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Auth\Login;
use Filament\View\PanelsRenderHook;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use App\Filament\Resources\DaftarWebsiteDesaResource\Widgets\StatistikWebsiteDesa;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\UserResource\Widgets\StatistikUser;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets\StatistikAplikasiOpd;
use App\Filament\Resources\DaftarAplikasiPerangkatDaerahResource\Widgets\StatAplikasiOpd;
use App\Filament\Resources\DaftarWebsiteDesaResource\Widgets\StatTotalDesa;





class AdminPanelProvider extends PanelProvider
{

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('login-bg', asset('login.css')),
        ]);

        \Filament\Tables\Table::configureUsing(function (\Filament\Tables\Table $table): void {
            $table
                ->striped()
                ->defaultPaginationPageOption(10);
        });
    }
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('bulubabi')
            //->login()
            ->login(Login::class)
            ->brandName('SISTEM INFORMASI PERMINTAAN DATA')
            ->brandLogo(asset('images/lgo.png'))
            ->brandLogoHeight('2.25rem')
            ->font('Inter')
            ->maxContentWidth('full')
            ->spa()
            ->darkMode(false)

            // ->navigationItems([
            //     NavigationItem::make('WA Login')
            //         ->icon('heroicon-o-qr-code')
            //         ->group('Integrasi')
            //         ->sort(99) // biar di bawah
            //         ->url(url('/wa-login'), shouldOpenInNewTab: true)
            //         ->visible(fn() => auth()->check() && auth()->user()->role === 'admin'),

            // ])
            // ada method ini di beberapa versi
            ->renderHook(
                'panels::body.start',
                fn() => view('components.loader')
            )

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn() => '<link rel="stylesheet" href="' . asset('admin.css') . '">'
            )

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => '<link rel="stylesheet" href="' . asset('css/custom.css') . '"><link rel="stylesheet" href="' . asset('css/filament-compact.css') . '">'
            )
            // Google reCAPTCHA v2
            ->renderHook(
                'panels::head.end',
                fn(): string => '<script src="https://www.google.com/recaptcha/api.js" async defer></script>'
            )
            //->brandName('Hoaxs')
            // ->loginHeading('Masuk Aplikasi')
            // ->loginSubheading('Silakan masuk menggunakan akun Anda')
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'danger' => Color::Red,


            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->pages([
                //Pages\Dashboard::class,

                \App\Filament\Pages\Dashboard::class,

            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     Widgets\AccountWidget::class,
            //     StatAplikasiOpd::class,
            //     StatTotalDesa::class,

            //widget filament pada dashboard
            // Widgets\FilamentInfoWidget::class,
            // -----end---
            // ])
            ->navigationGroups([
                'Manajemen',
                'Data',
            ])
            //->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                // 'throttle:filament-login',
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
