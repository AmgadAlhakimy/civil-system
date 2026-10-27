<?php

namespace App\Filament\Resources\ServiceFees\Pages;

use App\Filament\Resources\ServiceFees\ServiceFeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceFee extends CreateRecord
{
    protected static string $resource = ServiceFeeResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('service_fees.create') ?? false;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
