<?php

namespace App\Filament\Resources\Citizens\Pages;

use App\Filament\Resources\Citizens\CitizenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCitizen extends EditRecord
{
    protected static string $resource = CitizenResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        abort_unless(
            Auth::user()?->can('citizens.update'),
            403
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض المواطن')
                ->icon('heroicon-o-eye')
                ->size('lg')
                ->visible(
                    fn (): bool => Auth::user()?->can('citizens.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف المواطن')
                ->icon('heroicon-o-trash')
                ->size('lg')
                ->visible(
                    fn (): bool => Auth::user()?->can('citizens.delete') ?? false
                ),

            ForceDeleteAction::make()
                ->label('حذف المواطن نهائياً')
                ->visible(
                    fn (): bool => Auth::user()?->can('citizens.force_delete') ?? false
                ),

            RestoreAction::make()
                ->label('استعادة المواطن')
                ->visible(
                    fn (): bool => Auth::user()?->can('citizens.restore') ?? false
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return CitizenResource::getUrl('view', [
            'record' => $this->getRecord(),
        ]);
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
