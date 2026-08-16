<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIdentityCards extends ListRecords
{
    protected static string $resource = IdentityCardResource::class;

    public function getTitle(): string
    {
        return 'البطاقات الشخصية';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إصدار بطاقة شخصية')
                ->icon('heroicon-o-document-plus'),
        ];
    }
}
