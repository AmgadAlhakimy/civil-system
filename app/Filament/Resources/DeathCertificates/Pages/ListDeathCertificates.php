<?php

namespace App\Filament\Resources\DeathCertificates\Pages;

use App\Filament\Resources\DeathCertificates\DeathCertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeathCertificates extends ListRecords
{
    protected static string $resource = DeathCertificateResource::class;

    public function getTitle(): string
    {
        return 'شهادات الوفاة';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إصدار شهادة وفاة')
                ->icon('heroicon-o-document-plus'),
        ];
    }
}
