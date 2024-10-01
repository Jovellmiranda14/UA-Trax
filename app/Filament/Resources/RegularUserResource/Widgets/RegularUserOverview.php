<?php

namespace App\Filament\Resources\RegularUserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;

class RegularUserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Regular Users', User::where('role', User::REGULAR_USER)->count()),
            Stat::make('Total RSO Users', User::where('role', User::REGULAR_USER)->where('position', User::RSO)->count()),
            Stat::make('Total Secretary Users', User::where('role', User::REGULAR_USER)->where('position', User::Secretary)->count()),
            Stat::make('Total Faculty Users', User::where('role', User::REGULAR_USER)->where('position', User::Faculty)->count()),
        ];
    }
}
