<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBirthCertificate extends CreateRecord
{
    protected static string $resource = BirthCertificateResource::class;

    public function getTitle(): string
    {
        return 'إصدار شهادة ميلاد';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();
        $data['status'] = 'pending';
        $data['print_count'] = 0;
        $data['issue_date'] = null;
        $data['approved_by'] = null;
        $data['approved_at'] = null;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء طلب شهادة الميلاد بنجاح';
    }
}
