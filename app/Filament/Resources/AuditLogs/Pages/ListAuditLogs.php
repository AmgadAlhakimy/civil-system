<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('audit_logs.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
