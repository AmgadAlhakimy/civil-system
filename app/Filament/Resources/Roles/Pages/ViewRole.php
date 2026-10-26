<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Roles\Schemas\RoleInfolist;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('roles.view'),
            403
        );

        parent::mount($record);
    }

    public function infolist(Schema $schema): Schema
    {
        return RoleInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل الدور')
                ->icon('heroicon-o-pencil-square')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('roles.update') ?? false
                ),
        ];
    }
}
