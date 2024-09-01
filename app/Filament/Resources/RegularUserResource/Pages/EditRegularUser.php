<?php

namespace App\Filament\Resources\RegularUserResource\Pages;

use App\Filament\Resources\RegularUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegularUser extends EditRecord
{
    protected static string $resource = RegularUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
