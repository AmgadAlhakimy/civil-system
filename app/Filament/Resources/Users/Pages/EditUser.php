<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض المستخدم'),

            DeleteAction::make()
                ->label('حذف المستخدم'),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المستخدم محذوف')
                ->body('هذا المستخدم موجود في سلة المحذوفات. يجب استعادة المستخدم أولًا قبل تعديله.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                UserResource::getUrl('index')
            );
        }
    }
}
