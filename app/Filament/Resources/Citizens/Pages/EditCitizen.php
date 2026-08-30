<?php

namespace App\Filament\Resources\Citizens\Pages;

use App\Filament\Resources\Citizens\CitizenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCitizen extends EditRecord
{
    protected static string $resource = CitizenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض المواطن'),

            DeleteAction::make()
                ->label('حذف المواطن'),

            ForceDeleteAction::make()
                ->label('حذف المواطن نهائياً'),

            RestoreAction::make()
                ->label('استعادة المواطن'),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المواطن محذوف')
                ->body('هذا المواطن موجود في سلة المحذوفات. يجب استعادة المواطن أولًا قبل تعديله.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                CitizenResource::getUrl('index')
            );
        }
    }
}
