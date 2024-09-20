<?php

namespace App\Providers;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use App\Filament\Pages\FacilityDashboard;
use App\Filament\Pages\EquipmentDashboard;
// use App\Filament\Pages\ImageModalPage;
use App\Observers\TicketObserver;
use App\Models\Ticket;
use App\Models\TicketQueue;




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

        Ticket::observe(TicketObserver::class);
           

        Filament::navigation([
            TicketHistoryResource::class,
        ]);

      
            
    }
}