<?php

namespace App\Filament\Resources\ServiceFees\Pages;

use App\Filament\Resources\ServiceFees\ServiceFeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceFees extends ListRecords
{
    protected static string $resource = ServiceFeeResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('service_fees.view') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إضافة رسوم')
                ->icon('heroicon-o-plus-circle')
                ->size('lg')
                ->visible(
                    fn (): bool => auth()->user()?->can(
                        'service_fees.create'
                    ) ?? false
                ),
        ];
    }
}
