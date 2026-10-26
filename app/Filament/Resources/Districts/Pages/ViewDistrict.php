<?php

namespace App\Filament\Resources\Districts\Pages;

use App\Filament\Resources\Districts\DistrictResource;
use App\Filament\Resources\Districts\Schemas\DistrictInfolist;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewDistrict extends ViewRecord
{
    protected static string $resource = DistrictResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('districts.view') ?? false,
            403
        );

        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المديرية محذوفة')
                ->body('هذه المديرية موجودة في سلة المحذوفات ولا يمكن عرض تفاصيلها.')
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
            EditAction::make()
                ->label('تعديل المديرية')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->visible(
                    fn (): bool => auth()->user()?->can('districts.update') ?? false
                ),

            DeleteAction::make()
                ->label('حذف المديرية')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف المديرية')
                ->modalDescription(
                    'هل أنت متأكد من حذف هذه المديرية؟ يمكن استعادتها من سلة المحذوفات.'
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
                                'توجد فروع مرتبطة بهذه المديرية. يجب معالجة الارتباطات أولًا قبل حذفها.'
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
        return DistrictResource::getUrl('index');
    }

    public function infolist(Schema $schema): Schema
    {
        return DistrictInfolist::configure($schema);
    }
}
