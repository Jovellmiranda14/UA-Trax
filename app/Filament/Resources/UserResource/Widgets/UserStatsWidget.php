<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()),
            Stat::make('Total Super Admins', User::whereIn('role', [User::equipment_superadmin, User::facility_super_admin])->count()),
            Stat::make('Total Equipment Super Admins', User::where('role', User::equipment_superadmin)->count()),
            Stat::make('Total Facility Super Admins', User::where('role', User::facility_super_admin)->count()),
        ];
    }
}
