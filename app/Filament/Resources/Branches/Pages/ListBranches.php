<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('branches.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إضافة فرع')
                ->icon('heroicon-o-building-office-2')
                ->size('lg')
                ->visible(
                    fn (): bool => auth()->user()?->can('branches.create') ?? false
                ),
        ];
    }
}
