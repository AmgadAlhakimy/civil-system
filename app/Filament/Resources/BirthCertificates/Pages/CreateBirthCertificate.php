<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBirthCertificate extends CreateRecord
{
    protected static string $resource = BirthCertificateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['issued_by'] = auth()->id();
        $data['status'] = 'pending';
        $data['print_count'] = 0;

        return $data;
    }
}
