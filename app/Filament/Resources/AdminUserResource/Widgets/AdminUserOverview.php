<?php

namespace App\Filament\Resources\AdminUserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;

class AdminUserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Admin User', User::whereIn('role', [User::EQUIPMENT_ADMIN_Omiss, User::EQUIPMENT_ADMIN_labcustodian, User::FACILITY_ADMIN])->count()),
            Stat::make('Total Equipment Admin OMISS', User::where('role', User::EQUIPMENT_ADMIN_Omiss)->count()),
            Stat::make('Total Equipment Admin Lab Custodian', User::where('role', User::EQUIPMENT_ADMIN_labcustodian)->count()),
        ];
    }
}
