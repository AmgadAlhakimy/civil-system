<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewBranch extends ViewRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل الفرع')
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    public function mount(int|string $record): void
    {
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
}
