<?php

namespace App\Providers;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use App\Filament\Pages\FacilityDashboard;
use App\Filament\Pages\EquipmentDashboard;
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
        Filament::registerPages([
            FacilityDashboard::class,
            EquipmentDashboard::class,
        ]);
    }
}
