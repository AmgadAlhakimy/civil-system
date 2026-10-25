<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewBranch extends ViewRecord
{
    protected static string $resource = BranchResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('branches.view'),
            403
        );

        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('الفرع محذوف')
                ->body('هذا الفرع موجود في سلة المحذوفات ولا يمكن عرض تفاصيله.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                BranchResource::getUrl('index')
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل الفرع')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->visible(
                    fn (): bool => auth()->user()?->can('branches.update') ?? false
                ),

            DeleteAction::make()
                ->label('حذف الفرع')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف الفرع')
                ->modalDescription('هل أنت متأكد من حذف هذا الفرع؟ يمكن استعادته من سلة المحذوفات.')
                ->modalSubmitActionLabel('نعم، حذف')
                ->visible(
                    fn (): bool => auth()->user()?->can('branches.delete') ?? false
                )
                ->before(function (DeleteAction $action): void {
                    abort_unless(
                        auth()->user()?->can('branches.delete'),
                        403
                    );

                    if ($this->record->users()->exists()) {
                        Notification::make()
                            ->title('لا يمكن حذف الفرع')
                            ->body('لا يمكن حذف هذا الفرع لأنه يحتوي على مستخدمين مرتبطين به. يجب نقل المستخدمين إلى فرع آخر أولًا ثم محاولة الحذف مرة أخرى.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
