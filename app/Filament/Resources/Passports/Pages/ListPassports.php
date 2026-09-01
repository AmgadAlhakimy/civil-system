<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPassports extends ListRecords
{
    protected static string $resource = PassportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('تقديم طلب جواز'),
        ];
    }
}
