<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use App\Filament\Resources\AdminUserResource\Widgets\AdminUserOverview;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use App\Models\User;
use Filament\Notifications\Notification;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('New admin user'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return[
            AdminUserOverview::class
        ];
    }
}
