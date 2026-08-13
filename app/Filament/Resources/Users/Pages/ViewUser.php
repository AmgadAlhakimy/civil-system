<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المستخدم محذوف')
                ->body('هذا المستخدم موجود في سلة المحذوفات. يجب استعادة المستخدم أولًا.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                UserResource::getUrl('index')
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
