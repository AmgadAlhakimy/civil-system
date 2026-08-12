<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPassport extends EditRecord
{
    protected static string $resource = PassportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض الجواز'),

            DeleteAction::make()
                ->label('حذف الجواز'),

            ForceDeleteAction::make()
                ->label('حذف نهائي'),

            RestoreAction::make()
                ->label('استعادة الجواز'),
        ];
    }
}
