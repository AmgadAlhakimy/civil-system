<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIdentityCards extends ListRecords
{
    protected static string $resource = IdentityCardResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('identity_cards.view'),
            403
        );

        parent::mount();
    }

    public function getTitle(): string
    {
        return 'البطاقات الشخصية';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إصدار بطاقة شخصية')
                ->icon('heroicon-o-document-plus')
                ->size('lg')
                ->visible(
                    fn (): bool => auth()->user()?->can('identity_cards.create') ?? false
                ),
        ];
    }
}
