<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
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
                ->label('تعديل المستخدم')
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            DeleteAction::make()
                ->label('حذف المستخدم')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف المستخدم')
                ->modalDescription('هل أنت متأكد من حذف هذا المستخدم؟ يمكن استعادته من سلة المحذوفات.')
                ->modalSubmitActionLabel('نعم، حذف')
                ->before(function (DeleteAction $action): void {
                    if (! $this->record->canBeDeleted()) {
                        Notification::make()
                            ->title('لا يمكن حذف المستخدم')
                            ->body('هذا المستخدم هو مدير لفرع. يجب تعيين مدير آخر للفرع أولًا ثم محاولة حذف المستخدم مرة أخرى.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
