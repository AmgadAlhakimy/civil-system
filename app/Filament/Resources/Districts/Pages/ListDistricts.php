<?php

namespace App\Filament\Resources\Districts\Pages;

use App\Filament\Resources\Districts\DistrictResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDistricts extends ListRecords
{
    protected static string $resource = DistrictResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('districts.view') ?? false,
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إضافة مديرية')
                ->icon('heroicon-o-map-pin')
                ->size('lg')
                ->visible(
                    fn (): bool => auth()->user()?->can('districts.create') ?? false
                ),
        ];
    }
}
