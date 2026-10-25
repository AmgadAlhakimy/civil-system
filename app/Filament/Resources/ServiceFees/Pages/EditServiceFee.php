<?php

namespace App\Filament\Resources\ServiceFees\Pages;

use App\Filament\Resources\ServiceFees\ServiceFeeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceFee extends EditRecord
{
    protected static string $resource = ServiceFeeResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('service_fees.update'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_fees.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_fees.force_delete') ?? false
                ),

            RestoreAction::make()
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_fees.restore') ?? false
                ),
        ];
    }
}
