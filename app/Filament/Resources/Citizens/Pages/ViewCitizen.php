<?php

namespace App\Filament\Resources\Citizens\Pages;

use App\Filament\Resources\Citizens\CitizenResource;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCitizen extends ViewRecord
{
    protected static string $resource = CitizenResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المواطن محذوف')
                ->body('هذا المواطن موجود في سلة المحذوفات. يجب استعادة المواطن أولًا.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                CitizenResource::getUrl('index')
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل')
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
