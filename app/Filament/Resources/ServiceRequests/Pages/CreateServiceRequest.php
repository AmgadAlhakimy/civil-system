<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('service_requests.create'),
            403
        );

        parent::mount();
    }
}
