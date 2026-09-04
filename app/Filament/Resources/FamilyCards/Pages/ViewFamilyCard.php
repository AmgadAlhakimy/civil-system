<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewFamilyCard extends ViewRecord
{
    protected static string $resource = FamilyCardResource::class;

    public function getTitle(): string
    {
        return 'بيانات البطاقة العائلية: ' . $this->record->card_number;
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('approve')
                ->label('اعتماد البطاقة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد البطاقة العائلية')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد هذه البطاقة؟ سيتم تحديد تاريخ الإصدار تلقائيًا، وتكون مدة الصلاحية 5 سنوات، وستصبح البطاقة سارية.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    if ($this->record->status !== 'pending') {
                        Notification::make()
                            ->title('لا يمكن اعتماد البطاقة')
                            ->body('حالة البطاقة تغيرت ولم يعد الطلب قيد الانتظار.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $issueDate = now();

                    $this->record->update([
                        'status' => 'active',
                        'approved_by' => auth()->id(),
                        'approved_at' => $issueDate,
                        'issue_date' => $issueDate->toDateString(),
                        'expiry_date' => $issueDate
                            ->copy()
                            ->addYears(5)
                            ->toDateString(),
                    ]);

                    Notification::make()
                        ->title('تم اعتماد البطاقة بنجاح')
                        ->body('تم إصدار البطاقة العائلية وأصبحت سارية لمدة 5 سنوات.')
                        ->success()
                        ->send();

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                        'expiry_date',
                    ]);
                }),

            Action::make('print')
                ->label('طباعة البطاقة')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('طباعة البطاقة العائلية')
                ->modalDescription(
                    'هل أنت متأكد من طباعة هذه البطاقة؟ سيتم تسجيل عملية الطباعة في النظام.'
                )
                ->modalSubmitActionLabel('نعم، طباعة')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'active')
                ->action(function () {
                    if ($this->record->status !== 'active') {
                        Notification::make()
                            ->title('لا يمكن طباعة البطاقة')
                            ->body('يجب أن تكون البطاقة سارية قبل طباعتها.')
                            ->warning()
                            ->send();

                        return;
                    }

                    if (
                        ! $this->record->issue_date ||
                        ! $this->record->expiry_date
                    ) {
                        Notification::make()
                            ->title('لا يمكن طباعة البطاقة')
                            ->body('تاريخ إصدار أو انتهاء البطاقة غير موجود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->record->load([
                        'head',
                        'members.citizen',
                    ]);

                    $pdf = Pdf::loadView(
                        'family-cards.pdf',
                        [
                            'familyCard' => $this->record,
                        ]
                    );

                    $this->record->increment('print_count');

                    $printCount = $this->record->fresh()->print_count;

                    Notification::make()
                        ->title('تمت طباعة البطاقة بنجاح')
                        ->body(
                            'تم تسجيل عملية الطباعة رقم ' . $printCount
                        )
                        ->success()
                        ->send();

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'family-card-' . $this->record->card_number . '.pdf'
                    );
                }),

            Action::make('reject')
                ->label('رفض البطاقة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('رفض البطاقة العائلية')
                ->modalDescription(
                    'هل أنت متأكد من رفض طلب البطاقة العائلية؟'
                )
                ->modalSubmitActionLabel('نعم، رفض')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    if ($this->record->status !== 'pending') {
                        Notification::make()
                            ->title('لا يمكن رفض البطاقة')
                            ->body('حالة البطاقة تغيرت ولم يعد الطلب قيد الانتظار.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->update([
                        'status' => 'rejected',
                        'approved_by' => null,
                        'approved_at' => null,
                        'issue_date' => null,
                        'expiry_date' => null,
                    ]);

                    Notification::make()
                        ->title('تم رفض البطاقة')
                        ->body('تم تغيير حالة البطاقة العائلية إلى مرفوضة.')
                        ->danger()
                        ->send();

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                        'expiry_date',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل البطاقة')
                ->icon('heroicon-o-pencil-square'),

        ];
    }
}
