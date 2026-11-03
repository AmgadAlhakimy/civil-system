<?php

namespace App\Filament\Resources\ServiceFees\Pages;

use App\Filament\Resources\ServiceFees\ServiceFeeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceFee extends EditRecord
{
    protected static string $resource = ServiceFeeResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('service_fees.update') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض الرسوم')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->visible(
                    fn (): bool => auth()->user()?->can(
                        'service_fees.view'
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ServiceFeeResource::getUrl('view', [
            'record' => $this->getRecord(),
        ]);
    }
}
