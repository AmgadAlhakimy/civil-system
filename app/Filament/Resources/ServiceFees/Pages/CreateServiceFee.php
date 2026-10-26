<?php

namespace App\Filament\Resources\ServiceFees\Pages;

use App\Filament\Resources\ServiceFees\ServiceFeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceFee extends CreateRecord
{
    protected static string $resource = ServiceFeeResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('service_fees.create'),
            403
        );

        parent::mount();
    }
}
