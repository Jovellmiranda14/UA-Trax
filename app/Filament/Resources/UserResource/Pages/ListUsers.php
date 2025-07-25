<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use App\Models\User;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [

            Actions\CreateAction::make()
                ->label('New user admin'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return[
            UserStatsWidget::class
        ];
    }
}
