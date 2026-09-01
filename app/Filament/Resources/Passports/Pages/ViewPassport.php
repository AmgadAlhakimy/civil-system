<?php

namespace App\Filament\Resources\Passports\Pages;

use App\Filament\Resources\Passports\PassportResource;
use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPassport extends ViewRecord
{
    protected static string $resource = PassportResource::class;

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
                ->label('اعتماد الجواز')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد الجواز')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد هذا الجواز؟ سيتم تحديد تاريخ الإصدار تلقائيًا، وتكون مدة الصلاحية 5 سنوات.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    if ($this->record->status !== 'pending') {
                        Notification::make()
                            ->title('لا يمكن اعتماد الجواز')
                            ->body('حالة الجواز تغيرت ولم يعد الطلب قيد الانتظار.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $issueDate = now();

                    $this->record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => $issueDate,
                        'issue_date' => $issueDate->toDateString(),
                        'expiry_date' => $issueDate
                            ->copy()
                            ->addYears(5)
                            ->toDateString(),
                    ]);

                    Notification::make()
                        ->title('تم اعتماد الجواز بنجاح')
                        ->body('تم تحديد تاريخ الإصدار وتاريخ الانتهاء لمدة 5 سنوات.')
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

            Action::make('activate')
                ->label('تفعيل الجواز')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('تفعيل الجواز')
                ->modalDescription(
                    'هل أنت متأكد من تفعيل هذا الجواز؟ بعد التفعيل سيصبح الجواز فعالًا وصالحًا للاستخدام.'
                )
                ->modalSubmitActionLabel('نعم، تفعيل')
                ->visible(fn (): bool => $this->record->status === 'approved')
                ->action(function (): void {
                    if ($this->record->status !== 'approved') {
                        Notification::make()
                            ->title('لا يمكن تفعيل الجواز')
                            ->body('يجب اعتماد الجواز أولًا.')
                            ->warning()
                            ->send();

                        return;
                    }

                    if (
                        ! $this->record->issue_date
                        || ! $this->record->expiry_date
                        || ! $this->record->approved_by
                        || ! $this->record->approved_at
                    ) {
                        Notification::make()
                            ->title('لا يمكن تفعيل الجواز')
                            ->body('بيانات الاعتماد والإصدار غير مكتملة.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->record->update([
                        'status' => 'active',
                    ]);

                    Notification::make()
                        ->title('تم تفعيل الجواز بنجاح')
                        ->body('أصبح الجواز الآن فعالًا وصالحًا للاستخدام.')
                        ->success()
                        ->send();

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            Action::make('print')
                ->label('طباعة الجواز')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('طباعة الجواز')
                ->modalDescription(
                    'هل أنت متأكد من طباعة هذا الجواز؟ سيتم تسجيل عملية الطباعة في النظام.'
                )
                ->modalSubmitActionLabel('نعم، طباعة')
                ->visible(fn (): bool => $this->record->status === 'active')
                ->action(function () {
                    if ($this->record->status !== 'active') {
                        Notification::make()
                            ->title('لا يمكن طباعة الجواز')
                            ->body('يجب أن يكون الجواز فعالًا قبل طباعته.')
                            ->warning()
                            ->send();

                        return;
                    }

                    if (
                        ! $this->record->issue_date
                        || ! $this->record->expiry_date
                    ) {
                        Notification::make()
                            ->title('لا يمكن طباعة الجواز')
                            ->body('تاريخ إصدار أو انتهاء الجواز غير موجود.')
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

                    $type = match ($this->record->type) {
                        'ordinary' => 'عادي',
                        'diplomatic' => 'دبلوماسي',
                        'official' => 'رسمي',
                        default => 'غير محدد',
                    };

                    $status = match ($this->record->status) {
                        'pending' => 'قيد الانتظار',
                        'approved' => 'معتمد',
                        'rejected' => 'مرفوض',
                        'active' => 'فعال',
                        'expired' => 'منتهي',
                        'cancelled' => 'ملغي',
                        'lost' => 'مفقود',
                        'damaged' => 'تالف',
                        default => 'غير محدد',
                    };

                    $pdf = Pdf::loadView(
                        'passports.pdf',
                        [
                            'passport' => $this->record,
                            'citizen' => $citizen,
                            'fullName' => $this->arabicText($fullName),
                            'type' => $this->arabicText($type),
                            'status' => $this->arabicText($status),
                            'authority' => $this->arabicText(
                                'مصلحة الهجرة والجوازات - الجمهورية اليمنية'
                            ),
                            'documentTitle' => $this->arabicText(
                                'مستخرج بيانات جواز سفر'
                            ),
                            'countryName' => $this->arabicText(
                                'الجمهورية اليمنية'
                            ),
                            'photoLabel' => $this->arabicText(
                                'صورة صاحب الجواز'
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
                                'هذه الوثيقة مستخرجة إلكترونياً من نظام السجل المدني والجوازات بالجمهورية اليمنية.'
                            ),
                            'warningText' => $this->arabicText(
                                'أي كشط أو تعديل في هذه الوثيقة يلغي صحتها.'
                            ),
                            'passportNumberLabel' => $this->arabicText(
                                'رقم الجواز'
                            ),
                            'nationalIdLabel' => $this->arabicText(
                                'الرقم الوطني'
                            ),
                            'fullNameLabel' => $this->arabicText(
                                'الاسم الكامل'
                            ),
                            'typeLabel' => $this->arabicText(
                                'نوع الجواز'
                            ),
                            'statusLabel' => $this->arabicText(
                                'حالة الجواز'
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
                        ->title('تمت طباعة الجواز بنجاح')
                        ->body(
                            'تم تسجيل عملية الطباعة رقم ' .
                            $this->record->fresh()->print_count
                        )
                        ->success()
                        ->send();

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'passport-' . $this->record->passport_number . '.pdf'
                    );
                }),

            Action::make('reject')
                ->label('رفض الجواز')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('رفض الجواز')
                ->modalDescription(
                    'هل أنت متأكد من رفض هذا الجواز؟'
                )
                ->modalSubmitActionLabel('نعم، رفض')
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (): void {
                    if ($this->record->status !== 'pending') {
                        Notification::make()
                            ->title('لا يمكن رفض الجواز')
                            ->body('حالة الجواز تغيرت ولم يعد الطلب قيد الانتظار.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->update([
                        'status' => 'rejected',
                    ]);

                    Notification::make()
                        ->title('تم رفض الجواز')
                        ->body('تم تغيير حالة الجواز إلى مرفوض.')
                        ->danger()
                        ->send();

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            EditAction::make()
                ->label('تعديل الجواز')
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
