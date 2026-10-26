<?php

namespace App\Filament\Resources\Governorates\Pages;

use App\Filament\Resources\Governorates\GovernorateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditGovernorate extends EditRecord
{
    protected static string $resource = GovernorateResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('governorates.update'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->can('governorates.delete') ?? false),

            ForceDeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->can('governorates.force_delete') ?? false),

            RestoreAction::make()
                ->visible(fn (): bool => auth()->user()?->can('governorates.restore') ?? false),
        ];
    }
}
