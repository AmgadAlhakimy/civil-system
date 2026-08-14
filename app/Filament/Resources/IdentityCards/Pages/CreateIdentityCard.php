<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIdentityCard extends CreateRecord
{
    protected static string $resource = IdentityCardResource::class;

    protected static ?string $title = 'إصدار بطاقة شخصية';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        $data['issued_by'] = auth()->id();
        $data['print_count'] = 0;

        return $data;
    }
}
