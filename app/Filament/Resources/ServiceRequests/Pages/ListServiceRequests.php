<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceRequests extends ListRecords
{
    protected static string $resource = ServiceRequestResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('service_requests.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_requests.create') ?? false
                ),
        ];
    }
}
