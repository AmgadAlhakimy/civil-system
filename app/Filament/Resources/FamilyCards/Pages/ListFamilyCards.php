<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFamilyCards extends ListRecords
{
    protected static string $resource = FamilyCardResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('family_cards.view'),
            403
        );

        parent::mount();
    }

    public function getTitle(): string
    {
        return 'البطاقات العائلية';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إصدار بطاقة عائلية')
                ->icon('heroicon-o-document-plus')
                ->visible(
                    fn (): bool => auth()->user()?->can('family_cards.create') ?? false
                ),
        ];
    }
}
