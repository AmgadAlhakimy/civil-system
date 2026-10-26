<?php

namespace App\Filament\Resources\ServiceFees\Pages;

use App\Filament\Resources\ServiceFees\ServiceFeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceFees extends ListRecords
{
    protected static string $resource = ServiceFeeResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('service_fees.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_fees.create') ?? false
                ),
        ];
    }
}
