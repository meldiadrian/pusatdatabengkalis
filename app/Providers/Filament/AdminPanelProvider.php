<?php

namespace App\Providers\Filament;

use App\Filament\Resources\PemohonResource;
use App\Filament\Resources\PemilikDataResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UnitKerjaResource;
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




class AdminPanelProvider extends PanelProvider
{

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('login-bg', asset('login.css')),
        ]);
    }
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->login(Login::class)
            ->brandName('SISTEM INFORMASI PERMINTAAN DATA')
            ->brandLogo(asset('images/lgo.png'))
            ->brandLogoHeight('2.5rem')
            // ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->default()
            ->id('admin')
            ->path('admin')
            // ->styles([
            //     asset('css/sidebar.css'),
            // ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn() => '<link rel="stylesheet" href="' . asset('admin.css') . '">'
            )

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => '<link rel="stylesheet" href="' . asset('css/custom.css') . '">'
            )
            //->brandName('Hoaxs')
            // ->loginHeading('Masuk Aplikasi')
            // ->loginSubheading('Silakan masuk menggunakan akun Anda')
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'danger'  => Color::Red,


            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // StatistikWebsiteDesa::class,
                //widget filament pada dashboard
                // Widgets\FilamentInfoWidget::class,
                // -----end---
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
