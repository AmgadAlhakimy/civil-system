<?php

namespace App\Filament\Resources\Governorates\Pages;

use App\Filament\Resources\Governorates\Schemas\GovernorateInfolist;
use App\Filament\Resources\Governorates\GovernorateResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;

class ViewGovernorate extends ViewRecord
{
    protected static string $resource = GovernorateResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('governorates.view'),
            403
        );

        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المحافظة محذوفة')
                ->body('هذه المحافظة موجودة في سلة المحذوفات ولا يمكن عرض تفاصيلها.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                GovernorateResource::getUrl('index')
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('تعديل المحافظة')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->visible(
                    fn (): bool => auth()->user()?->can('governorates.update') ?? false
                ),

            DeleteAction::make()
                ->label('حذف المحافظة')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف المحافظة')
                ->modalDescription(
                    'هل أنت متأكد من حذف هذه المحافظة؟ يمكن استعادتها من سلة المحذوفات.'
                )
                ->modalSubmitActionLabel('نعم، حذف')
                ->visible(
                    fn (): bool => auth()->user()?->can('governorates.delete') ?? false
                )
                ->before(function (DeleteAction $action): void {
                    abort_unless(
                        auth()->user()?->can('governorates.delete'),
                        403
                    );

                    if (
                        $this->record->districts()->exists()
                        || $this->record->branches()->exists()
                    ) {
                        Notification::make()
                            ->title('لا يمكن حذف المحافظة')
                            ->body(
                                'توجد مديريات أو فروع مرتبطة بهذه المحافظة. يجب معالجة الارتباطات أولًا قبل حذفها.'
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
        return $this->getResource()::getUrl('index');
    }

    public function infolist(Schema $schema): Schema
    {
        return GovernorateInfolist::configure($schema);
    }
}
