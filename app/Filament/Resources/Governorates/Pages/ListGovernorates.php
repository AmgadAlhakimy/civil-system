<?php

namespace App\Filament\Resources\Governorates\Pages;

use App\Filament\Resources\Governorates\GovernorateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGovernorates extends ListRecords
{
    protected static string $resource = GovernorateResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('governorates.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => auth()->user()?->can('governorates.create') ?? false),
        ];
    }
}
