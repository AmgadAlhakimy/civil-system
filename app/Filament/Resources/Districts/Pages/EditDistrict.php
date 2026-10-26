<?php

namespace App\Filament\Resources\Districts\Pages;

use App\Filament\Resources\Districts\DistrictResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDistrict extends EditRecord
{
    protected static string $resource = DistrictResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('districts.update') ?? false,
            403
        );

        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المديرية محذوفة')
                ->body('يجب استعادة المديرية أولًا قبل تعديلها.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(DistrictResource::getUrl('index'));

            return;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('عرض المديرية')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->visible(
                    fn (): bool => auth()->user()?->can('districts.view') ?? false
                ),

            DeleteAction::make()
                ->label('حذف المديرية')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف المديرية')
                ->modalDescription(
                    'هل أنت متأكد من حذف هذه المديرية؟ يمكن استعادتها من قائمة المديريات المحذوفة.'
                )
                ->modalSubmitActionLabel('نعم، حذف')
                ->visible(
                    fn (): bool => auth()->user()?->can('districts.delete') ?? false
                )
                ->before(function (DeleteAction $action): void {
                    abort_unless(
                        auth()->user()?->can('districts.delete') ?? false,
                        403
                    );

                    if (
                        method_exists($this->record, 'branches')
                        && $this->record->branches()->exists()
                    ) {
                        Notification::make()
                            ->title('لا يمكن حذف المديرية')
                            ->body(
                                'توجد فروع مرتبطة بهذه المديرية. عالج الارتباطات أولًا ثم حاول الحذف مرة أخرى.'
                            )
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return DistrictResource::getUrl('view', [
            'record' => $this->record,
        ]);
    }
}
