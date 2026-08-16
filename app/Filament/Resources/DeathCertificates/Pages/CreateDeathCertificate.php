<?php

namespace App\Filament\Resources\DeathCertificates\Pages;

use App\Filament\Resources\DeathCertificates\DeathCertificateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeathCertificate extends CreateRecord
{
    protected static string $resource = DeathCertificateResource::class;

    public function getTitle(): string
    {
        return 'إصدار شهادة وفاة';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();
        $data['status'] = 'pending';
        $data['print_count'] = 0;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إصدار شهادة الوفاة بنجاح';
    }
}
