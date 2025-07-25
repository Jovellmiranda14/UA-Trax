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
            Stat::make('Total Admin User', User::whereIn('role', [User::equipment_admin_omiss, User::equipment_admin_labcustodian, User::facility_admin])->count()),
            Stat::make('Total Equipment Admin OMISS', User::where('role', User::equipment_admin_omiss)->count()),
            Stat::make('Total Equipment Admin Lab Custodian', User::where('role', User::equipment_admin_labcustodian)->count()),
            Stat::make('Total Facility Admin', User::where('role', User::facility_admin)->count()),
        ];
    }
}
