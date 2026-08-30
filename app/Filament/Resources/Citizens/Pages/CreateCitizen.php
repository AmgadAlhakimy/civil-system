<?php

namespace App\Filament\Resources\Citizens\Pages;

use App\Filament\Resources\Citizens\CitizenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCitizen extends CreateRecord
{
    protected static string $resource = CitizenResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = false;

        return $data;
    }
}
