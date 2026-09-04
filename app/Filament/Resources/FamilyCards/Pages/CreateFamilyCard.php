<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFamilyCard extends CreateRecord
{
    protected static string $resource = FamilyCardResource::class;

    public function getTitle(): string
    {
        return 'إصدار بطاقة عائلية';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();
        $data['status'] = 'pending';
        $data['print_count'] = 0;
        $data['issue_date'] = null;
        $data['expiry_date'] = null;
        $data['approved_by'] = null;
        $data['approved_at'] = null;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء طلب إصدار البطاقة العائلية بنجاح';
    }
}
