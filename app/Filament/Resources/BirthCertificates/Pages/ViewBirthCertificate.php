<?php

namespace App\Filament\Resources\BirthCertificates\Pages;

use App\Filament\Resources\BirthCertificates\BirthCertificateResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewBirthCertificate extends ViewRecord
{
    protected static string $resource = BirthCertificateResource::class;

    public function getTitle(): string
    {
        return 'بيانات شهادة الميلاد: ' . $this->record->certificate_number;
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('approve')
                ->label('اعتماد الشهادة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد شهادة الميلاد')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد هذه الشهادة؟ سيتم تحديد تاريخ الإصدار تلقائيًا، وستصبح الشهادة سارية.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    if ($this->record->status !== 'pending') {
                        Notification::make()
                            ->title('لا يمكن اعتماد الشهادة')
                            ->body('حالة الشهادة تغيرت ولم يعد الطلب قيد الانتظار.')
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
                    ]);

                    Notification::make()
                        ->title('تم اعتماد الشهادة بنجاح')
                        ->body('تم إصدار شهادة الميلاد وأصبحت سارية.')
                        ->success()
                        ->send();

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                    ]);
                }),

            Action::make('print')
                ->label('طباعة الشهادة')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('طباعة شهادة الميلاد')
                ->modalDescription(
                    'هل أنت متأكد من طباعة هذه الشهادة؟ سيتم تسجيل عملية الطباعة في النظام.'
                )
                ->modalSubmitActionLabel('نعم، طباعة')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'active')
                ->action(function () {
                    if ($this->record->status !== 'active') {
                        Notification::make()
                            ->title('لا يمكن طباعة الشهادة')
                            ->body('يجب أن تكون الشهادة سارية قبل طباعتها.')
                            ->warning()
                            ->send();

                        return;
                    }

                    if (! $this->record->issue_date) {
                        Notification::make()
                            ->title('لا يمكن طباعة الشهادة')
                            ->body('تاريخ إصدار الشهادة غير موجود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->record->load([
                        'child',
                        'father',
                        'mother',
                        'issuedBy',
                        'approvedBy',
                    ]);

                    $pdf = Pdf::loadView(
                        'birth-certificates.pdf',
                        [
                            'birthCertificate' => $this->record,
                        ]
                    );

                    $this->record->increment('print_count');

                    $printCount = $this->record->fresh()->print_count;

                    Notification::make()
                        ->title('تمت طباعة الشهادة بنجاح')
                        ->body(
                            'تم تسجيل عملية الطباعة رقم ' . $printCount
                        )
                        ->success()
                        ->send();

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'birth-certificate-' . $this->record->certificate_number . '.pdf'
                    );
                }),

            Action::make('cancel')
                ->label('إلغاء الشهادة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('إلغاء شهادة الميلاد')
                ->modalDescription(
                    'هل أنت متأكد من إلغاء طلب شهادة الميلاد؟'
                )
                ->modalSubmitActionLabel('نعم، إلغاء')
                ->modalCancelActionLabel('إلغاء')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    if ($this->record->status !== 'pending') {
                        Notification::make()
                            ->title('لا يمكن إلغاء الشهادة')
                            ->body('حالة الشهادة تغيرت ولم يعد الطلب قيد الانتظار.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->update([
                        'status' => 'cancelled',
                        'approved_by' => null,
                        'approved_at' => null,
                        'issue_date' => null,
                    ]);

                    Notification::make()
                        ->title('تم إلغاء الشهادة')
                        ->body('تم تغيير حالة شهادة الميلاد إلى ملغاة.')
                        ->danger()
                        ->send();

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل الشهادة')
                ->icon('heroicon-o-pencil-square'),

        ];
    }
}
