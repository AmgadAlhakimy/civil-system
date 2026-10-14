<?php

namespace App\Filament\Resources\Citizens\Pages;

use App\Filament\Resources\Citizens\CitizenResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCitizen extends CreateRecord
{
    protected static string $resource = CitizenResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = true;
        $data['verification_status'] = 'approved';
        $data['rejection_reason'] = null;
        $data['verified_by'] = Auth::id();
        $data['verified_at'] = now();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return CitizenResource::getUrl('index');
    }
}
