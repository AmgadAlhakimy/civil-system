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
        $data['verification_status'] = 'pending';
        $data['rejection_reason'] = null;
        $data['verified_by'] = null;
        $data['verified_at'] = null;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return CitizenResource::getUrl('index');
    }
}
