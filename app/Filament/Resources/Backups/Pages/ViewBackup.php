<?php

namespace App\Filament\Resources\Backups\Pages;

use App\Filament\Resources\Backups\BackupResource;
use Filament\Resources\Pages\ViewRecord;

class ViewBackup extends ViewRecord
{
    protected static string $resource = BackupResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('backups.view'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
