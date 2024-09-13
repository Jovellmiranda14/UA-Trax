<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()),
            Stat::make('Total Super Admins', User::whereIn('role', [User::EquipmentSUPER_ADMIN, User::FacilitySUPER_ADMIN])->count()),
            Stat::make('Total Equipment Super Admins', User::where('role', User::EquipmentSUPER_ADMIN)->count()),
            Stat::make('Total Facility Super Admins', User::where('role', User::FacilitySUPER_ADMIN)->count()),
        ];
    }
}
