<?php

namespace App\Filament\Resources\Citizens\Pages;

use App\Filament\Resources\Citizens\CitizenResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewCitizen extends ViewRecord
{
    protected static string $resource = CitizenResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->trashed()) {
            Notification::make()
                ->title('المواطن محذوف')
                ->body('هذا المواطن موجود في سلة المحذوفات. يجب استعادة المواطن أولًا.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(
                CitizenResource::getUrl('index')
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('اعتماد البيانات')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد بيانات المواطن')
                ->modalDescription('هل أنت متأكد من اعتماد بيانات هذا المواطن؟')
                ->modalSubmitActionLabel('تأكيد الاعتماد')
                ->visible(
                    fn (): bool => !$this->record->trashed()
                        && $this->record->verification_status === 'pending'
                )
                ->action(function (): void {
                    $this->record->update([
                        'verification_status' => 'approved',
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                        'rejection_reason' => null,
                    ]);

                    Notification::make()
                        ->title('تم اعتماد بيانات المواطن')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'verification_status',
                        'verified_by',
                        'verified_at',
                        'rejection_reason',
                    ]);
                }),

            Action::make('reject')
                ->label('رفض البيانات')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->modalHeading('رفض بيانات المواطن')
                ->modalDescription('يرجى إدخال سبب الرفض بوضوح.')
                ->modalSubmitActionLabel('تأكيد الرفض')
                ->schema([
                    Textarea::make('rejection_reason')
                        ->label('سبب الرفض')
                        ->required()
                        ->minLength(5)
                        ->maxLength(2000)
                        ->rows(4)
                        ->placeholder('اكتب سبب رفض بيانات المواطن...')
                        ->validationMessages([
                            'required' => 'سبب الرفض مطلوب.',
                            'min' => 'يجب ألا يقل سبب الرفض عن 5 أحرف.',
                            'max' => 'يجب ألا يتجاوز سبب الرفض 2000 حرف.',
                        ]),
                ])
                ->visible(
                    fn (): bool => !$this->record->trashed()
                        && $this->record->verification_status === 'pending'
                )
                ->action(function (array $data): void {
                    $this->record->update([
                        'verification_status' => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                    ]);

                    Notification::make()
                        ->title('تم رفض بيانات المواطن')
                        ->body('تم حفظ سبب الرفض بنجاح.')
                        ->danger()
                        ->send();

                    $this->refreshFormData([
                        'verification_status',
                        'rejection_reason',
                        'verified_by',
                        'verified_at',
                    ]);
                }),

            Action::make('resubmit')
                ->label('إعادة تقديم للمراجعة')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('إعادة تقديم بيانات المواطن')
                ->modalDescription('تأكد من تصحيح البيانات المطلوبة قبل إعادتها للمراجعة.')
                ->modalSubmitActionLabel('إعادة التقديم')
                ->visible(
                    fn (): bool => !$this->record->trashed()
                        && $this->record->verification_status === 'rejected'
                )
                ->action(function (): void {
                    $this->record->update([
                        'verification_status' => 'pending',
                        'rejection_reason' => null,
                        'verified_by' => null,
                        'verified_at' => null,
                    ]);

                    Notification::make()
                        ->title('تمت إعادة تقديم البيانات')
                        ->body('أصبحت بيانات المواطن بانتظار الاعتماد.')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'verification_status',
                        'rejection_reason',
                        'verified_by',
                        'verified_at',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل المواطن')
                ->icon('heroicon-o-pencil-square'),

            DeleteAction::make()
                ->label('حذف المواطن')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('حذف المواطن')
                ->modalDescription('هل أنت متأكد من حذف هذا المواطن؟ سيتم نقله إلى سلة المحذوفات.')
                ->modalSubmitActionLabel('تأكيد الحذف')
                ->successNotificationTitle('تم حذف المواطن بنجاح'),
        ];
    }
}
