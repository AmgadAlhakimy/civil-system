<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('roles.view'),
            403
        );

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إنشاء دور جديد')
                ->icon('heroicon-o-plus-circle')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('roles.create') ?? false
                ),
        ];
    }
}
