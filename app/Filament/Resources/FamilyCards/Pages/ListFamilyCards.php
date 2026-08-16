<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFamilyCards extends ListRecords
{
    protected static string $resource = FamilyCardResource::class;

    public function getTitle(): string
    {
        return 'البطاقات العائلية';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إصدار بطاقة عائلية')
                ->icon('heroicon-o-document-plus'),
        ];
    }
}
