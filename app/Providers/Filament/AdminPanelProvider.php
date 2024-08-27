<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Auth\Login;
use Filament\Notifications\Notification;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->topNavigation()
            ->id('admin')
            ->path('user')
            ->brandLogo(asset('images/UATRAX-logo-dark-transparent.png'))
            ->brandLogoHeight('3rem')
            ->login(Login::class) 
            ->colors([
                'primary' => '#4D68C9',
            //     'warning' => Color::Red,
            //     'info' => Color::Blue,
            //     'success' => Color::Green,
            ])
            ->favicon(asset('images/UATRAX-logo-dark-transparent.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            // Wag cocooment
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages') 
            // Wag cocooment

            
            // ->pages([
            //     Pages\Dashboard::class,
            //     //  Pages\FacilityDashboard::class,
            //     // Pages\EquipmentDashboard::class,
            // ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     Widgets\AccountWidget::class,
            //     Widgets\FilamentInfoWidget::class,
            // ])
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
