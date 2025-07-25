<?php

namespace App\Filament\Resources\RegularUserResource\Pages;

use App\Filament\Resources\RegularUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRegularUser extends CreateRecord
{
    protected static string $resource = RegularUserResource::class;
}
