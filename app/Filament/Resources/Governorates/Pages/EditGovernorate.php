<?php

namespace App\Filament\Resources\Governorates\Pages;

use App\Filament\Resources\Governorates\GovernorateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditGovernorate extends EditRecord
{
    protected static string $resource = GovernorateResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('governorates.update'),
            403
        );

        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المحافظة محذوفة')
                ->body(
                    'هذه المحافظة موجودة في سلة المحذوفات. '
                    . 'يجب استعادتها أولًا قبل تعديلها.'
                )
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                GovernorateResource::getUrl('index')
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', [
            'record' => $this->record,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض المحافظة')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->visible(
                    fn (): bool => auth()->user()?->can('governorates.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف المحافظة')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف المحافظة')
                ->modalDescription(
                    'هل أنت متأكد من حذف هذه المحافظة؟ '
                    . 'يمكن استعادتها من جدول المحافظات.'
                )
                ->modalSubmitActionLabel('نعم، حذف')
                ->visible(
                    fn (): bool => auth()->user()?->can('governorates.delete') ?? false
                )
                ->before(function (DeleteAction $action): void {
                    abort_unless(
                        auth()->user()?->can('governorates.delete') ?? false,
                        403
                    );

                    if (
                        $this->record->districts()->exists()
                        || $this->record->branches()->exists()
                    ) {
                        Notification::make()
                            ->title('لا يمكن حذف المحافظة')
                            ->body(
                                'توجد مديريات أو فروع مرتبطة بهذه المحافظة. '
                                . 'عالج الارتباطات أولًا ثم حاول الحذف مرة أخرى.'
                            )
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
