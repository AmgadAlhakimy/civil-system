<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Resources\Pages\EditRecord;

class EditAuditLog extends EditRecord
{
    protected static string $resource = AuditLogResource::class;

    public function mount(int|string $record): void
    {
        abort(403);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
