<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIdentityCard extends CreateRecord
{
    protected static string $resource = IdentityCardResource::class;

    public function getTitle(): string
    {
        return 'إصدار بطاقة شخصية';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        $data['issued_by'] = auth()->id();
        $data['print_count'] = 0;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إصدار البطاقة الشخصية بنجاح';
    }
}
