<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Auth\Access\AuthorizationException;

class ListPassports extends ListRecords
{
    protected static string $resource = PassportResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (! auth()->user()?->can("passports.view")) {
            throw new AuthorizationException(
                "ليس لديك صلاحية عرض قائمة الجوازات."
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("تقديم طلب جواز")
                ->icon("heroicon-o-book-open")
                ->size("lg")
                ->visible(
                    fn (): bool => auth()->user()?->can("passports.create") ?? false
                ),
        ];
    }
}
