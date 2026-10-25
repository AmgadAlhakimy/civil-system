<?php

namespace App\Filament\Resources\Backups\Pages;

use App\Filament\Resources\Backups\BackupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBackups extends ListRecords
{
    protected static string $resource = BackupResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('backups.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(
                    fn (): bool => auth()->user()?->can('backups.create') ?? false
                ),
        ];
    }
}
