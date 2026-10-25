<?php

namespace App\Filament\Resources\Districts\Pages;

use App\Filament\Resources\Districts\DistrictResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditDistrict extends EditRecord
{
    protected static string $resource = DistrictResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('districts.update'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(
                    fn (): bool => auth()->user()?->can('districts.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->visible(
                    fn (): bool => auth()->user()?->can('districts.force_delete') ?? false
                ),

            RestoreAction::make()
                ->visible(
                    fn (): bool => auth()->user()?->can('districts.restore') ?? false
                ),
        ];
    }
}
