<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIdentityCard extends EditRecord
{
    protected static string $resource = IdentityCardResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset(
            $data['issued_by'],
            $data['issue_date'],
            $data['approved_by'],
            $data['approved_at'],
            $data['print_count'],
            $data['status']
        );

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            DeleteAction::make()
                ->label('حذف'),

            ForceDeleteAction::make()
                ->label('حذف نهائي'),

            RestoreAction::make()
                ->label('استعادة'),
        ];
    }
}
