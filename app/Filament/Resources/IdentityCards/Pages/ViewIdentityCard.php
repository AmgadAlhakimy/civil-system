<?php

namespace App\Filament\Resources\IdentityCards\Pages;

use App\Filament\Resources\IdentityCards\IdentityCardResource;
use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewIdentityCard extends ViewRecord
{
    protected static string $resource = IdentityCardResource::class;

    public function getTitle(): string
    {
        return 'بيانات البطاقة الشخصية: ' . $this->record->id_number;
    }

    protected function arabicText(mixed $text): string
    {
        if ($text === null || $text === '') {
            return '-';
        }

        $text = (string) $text;

        if (! preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return $text;
        }

        $arabic = new Arabic();

        return $arabic->utf8Glyphs($text);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('اعتماد البطاقة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد البطاقة الشخصية')
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
                        ->body('تم إصدار البطاقة وأصبحت سارية لمدة 5 سنوات.')
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
                ->modalHeading('طباعة البطاقة الشخصية')
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

                    $this->record->load('citizen');

                    $citizen = $this->record->citizen;

                    $fullName = collect([
                        $citizen?->first_name,
                        $citizen?->father_name,
                        $citizen?->middle_name,
                        $citizen?->last_name,
                    ])
                        ->filter()
                        ->join(' ');

                    $status = match ($this->record->status) {
                        'pending' => 'قيد الانتظار',
                        'rejected' => 'مرفوضة',
                        'active' => 'سارية',
                        'expired' => 'منتهية',
                        'cancelled' => 'ملغاة',
                        'lost' => 'مفقودة',
                        'damaged' => 'تالفة',
                        default => 'غير محدد',
                    };

                    $pdf = Pdf::loadView(
                        'identity-cards.pdf',
                        [
                            'identityCard' => $this->record,
                            'citizen' => $citizen,
                            'fullName' => $this->arabicText($fullName),
                            'status' => $this->arabicText($status),
                            'authority' => $this->arabicText(
                                'مصلحة الأحوال المدنية والسجل المدني - الجمهورية اليمنية'
                            ),
                            'documentTitle' => $this->arabicText(
                                'مستخرج بيانات بطاقة شخصية'
                            ),
                            'countryName' => $this->arabicText(
                                'الجمهورية اليمنية'
                            ),
                            'photoLabel' => $this->arabicText(
                                'صورة صاحب البطاقة'
                            ),
                            'photoUnavailable' => $this->arabicText(
                                'صورة غير متوفرة'
                            ),
                            'printCountLabel' => $this->arabicText(
                                'عدد مرات الطباعة'
                            ),
                            'verificationLabel' => $this->arabicText(
                                'رمز التحقق الإلكتروني'
                            ),
                            'footerText' => $this->arabicText(
                                'هذه الوثيقة مستخرجة إلكترونياً من نظام السجل المدني بالجمهورية اليمنية.'
                            ),
                            'warningText' => $this->arabicText(
                                'أي كشط أو تعديل في هذه الوثيقة يلغي صحتها.'
                            ),
                            'idNumberLabel' => $this->arabicText(
                                'رقم البطاقة'
                            ),
                            'nationalIdLabel' => $this->arabicText(
                                'الرقم الوطني'
                            ),
                            'fullNameLabel' => $this->arabicText(
                                'الاسم الكامل'
                            ),
                            'statusLabel' => $this->arabicText(
                                'حالة البطاقة'
                            ),
                            'issueDateLabel' => $this->arabicText(
                                'تاريخ الإصدار'
                            ),
                            'expiryDateLabel' => $this->arabicText(
                                'تاريخ الانتهاء'
                            ),
                            'authorityLabel' => $this->arabicText(
                                'جهة الإصدار'
                            ),
                            'qrLabel' => $this->arabicText(
                                'رمز التحقق الإلكتروني'
                            ),
                        ]
                    );

                    $this->record->increment('print_count');

                    Notification::make()
                        ->title('تمت طباعة البطاقة بنجاح')
                        ->body(
                            'تم تسجيل عملية الطباعة رقم ' .
                            $this->record->fresh()->print_count
                        )
                        ->success()
                        ->send();

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'identity-card-' . $this->record->id_number . '.pdf'
                    );
                }),

            Action::make('reject')
                ->label('رفض البطاقة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('رفض البطاقة الشخصية')
                ->modalDescription(
                    'هل أنت متأكد من رفض طلب البطاقة الشخصية؟'
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
                        ->body('تم تغيير حالة البطاقة إلى مرفوضة.')
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
