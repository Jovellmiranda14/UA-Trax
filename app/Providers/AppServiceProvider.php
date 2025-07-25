<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use App\Filament\Pages\FacilityDashboard;
use App\Filament\Pages\EquipmentDashboard;
use App\Observers\TicketObserver;
use App\Models\Ticket;
use App\Models\TicketQueue;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Ticket::observe(TicketObserver::class);
        
        // Filament::registerPages([
        //     FacilityDashboard::class,
        //     EquipmentDashboard::class,
        //     //  ImageModalPage::class,
        // ]);
    }
}

class FilamentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Observe Ticket model
        Ticket::observe(TicketObserver::class);

        // Register your custom CSS for the Ticket resource
        FilamentAsset::registerStyles([
            asset('css/custom.css'), // Path to your custom CSS file
        ]);

        // Register navigation
        Filament::navigation([
            TicketHistoryResource::class,
        ]);
    }
}
