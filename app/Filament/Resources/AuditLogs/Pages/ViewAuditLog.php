<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('audit_logs.view'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
