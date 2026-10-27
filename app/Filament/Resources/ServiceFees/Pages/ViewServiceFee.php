<?php

namespace App\Filament\Resources\ServiceFees\Pages;

use App\Filament\Resources\ServiceFees\ServiceFeeResource;
use App\Filament\Resources\ServiceFees\Schemas\ServiceFeeInfolist;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewServiceFee extends ViewRecord
{
    protected static string $resource = ServiceFeeResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('service_fees.view') ?? false;
    }

    public function infolist(Schema $schema): Schema
    {
        return ServiceFeeInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل الرسوم')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->visible(
                    fn (): bool => auth()->user()?->can(
                        'service_fees.update'
                    ) ?? false
                ),

            DeleteAction::make()
                ->label('حذف الرسوم')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(
                    fn (): bool => auth()->user()?->can(
                        'service_fees.delete'
                    ) ?? false
                )
                ->requiresConfirmation()
                ->modalHeading('حذف الرسوم')
                ->modalDescription(
                    'هل أنت متأكد من حذف هذه الرسوم؟'
                )
                ->modalSubmitActionLabel('نعم، حذف'),
        ];
    }
}
