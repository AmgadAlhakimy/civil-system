<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض الفرع'),

            DeleteAction::make()
                ->label('حذف الفرع'),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('الفرع محذوف')
                ->body('هذا الفرع موجود في سلة المحذوفات. يجب استعادة الفرع أولًا قبل تعديله.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                BranchResource::getUrl('index')
            );
        }
    }
}
