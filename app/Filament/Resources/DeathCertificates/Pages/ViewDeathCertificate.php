<?php

namespace App\Filament\Resources\DeathCertificates\Pages;

use App\Filament\Resources\DeathCertificates\DeathCertificateResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewDeathCertificate extends ViewRecord
{
    protected static string $resource = DeathCertificateResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('death_certificates.view'),
            403
        );

        parent::mount($record);
    }

    public function getTitle(): string
    {
        return 'بيانات شهادة الوفاة';
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('approve')
                ->label('اعتماد الشهادة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('اعتماد شهادة الوفاة')
                ->modalDescription(
                    'هل أنت متأكد من اعتماد هذه الشهادة؟ سيتم تحديد تاريخ الإصدار تلقائيًا، وستصبح الشهادة معتمدة.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'pending'
                        && (auth()->user()?->can('death_certificates.approve') ?? false)
                )
                ->action(function (): void {
                    abort_unless(
                        auth()->user()?->can('death_certificates.approve'),
                        403
                    );

                    $this->record->refresh();

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
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => $issueDate,
                        'issue_date' => $issueDate->toDateString(),
                        'rejection_reason' => null,
                    ]);

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                        'rejection_reason',
                    ]);

                    Notification::make()
                        ->title('تم اعتماد الشهادة بنجاح')
                        ->body('تم اعتماد شهادة الوفاة وتحديد تاريخ إصدارها.')
                        ->success()
                        ->send();
                }),

            Action::make('print')
                ->label('طباعة الشهادة')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('طباعة شهادة الوفاة')
                ->modalDescription(
                    'هل أنت متأكد من طباعة هذه الشهادة؟ سيتم تسجيل عملية الطباعة في النظام.'
                )
                ->modalSubmitActionLabel('نعم، طباعة')
                ->modalCancelActionLabel('إلغاء')
                ->visible(
                    fn (): bool =>
                        in_array($this->record->status, ['approved', 'issued'], true)
                        && (auth()->user()?->can('death_certificates.print') ?? false)
                )
                ->action(function () {
                    abort_unless(
                        auth()->user()?->can('death_certificates.print'),
                        403
                    );

                    $this->record->refresh();

                    if (! in_array($this->record->status, ['approved', 'issued'], true)) {
                        Notification::make()
                            ->title('لا يمكن طباعة الشهادة')
                            ->body('يجب أن تكون الشهادة معتمدة أو سارية قبل طباعتها.')
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
                        'deceased',
                        'issuedBy',
                        'approvedBy',
                    ]);

                    $pdf = Pdf::loadView(
                        'death-certificates.pdf',
                        [
                            'certificate' => $this->record,
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
                        'death-certificate-' . $this->record->id . '.pdf'
                    );
                }),

            Action::make('reject')
                ->label('رفض الشهادة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->schema([
                    Textarea::make('rejection_reason')
                        ->label('سبب الرفض')
                        ->required()
                        ->minLength(3)
                        ->maxLength(1000)
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->modalHeading('رفض شهادة الوفاة')
                ->modalDescription(
                    'يرجى كتابة سبب رفض شهادة الوفاة قبل تأكيد العملية.'
                )
                ->modalSubmitActionLabel('نعم، رفض الشهادة')
                ->modalCancelActionLabel('إلغاء')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'pending'
                        && (auth()->user()?->can('death_certificates.reject') ?? false)
                )
                ->action(function (array $data): void {
                    abort_unless(
                        auth()->user()?->can('death_certificates.reject'),
                        403
                    );

                    $this->record->refresh();

                    if ($this->record->status !== 'pending') {
                        Notification::make()
                            ->title('لا يمكن رفض الشهادة')
                            ->body('حالة الشهادة تغيرت ولم يعد الطلب قيد الانتظار.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->update([
                        'status' => 'rejected',
                        'rejection_reason' => trim($data['rejection_reason']),
                        'approved_by' => null,
                        'approved_at' => null,
                        'issue_date' => null,
                    ]);

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'rejection_reason',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                    ]);

                    Notification::make()
                        ->title('تم رفض الشهادة')
                        ->body('تم رفض شهادة الوفاة وحفظ سبب الرفض.')
                        ->danger()
                        ->send();
                }),

            EditAction::make()
                ->label('تعديل الشهادة')
                ->icon('heroicon-o-pencil-square')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('death_certificates.update') ?? false
                ),

            DeleteAction::make()
                ->label('حذف الشهادة')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('death_certificates.delete') ?? false
                )
                ->requiresConfirmation()
                ->modalHeading('حذف شهادة الوفاة')
                ->modalDescription(
                    'هل أنت متأكد من حذف شهادة الوفاة؟ سيتم نقلها إلى سلة المحذوفات ويمكن استعادتها لاحقًا.'
                )
                ->modalSubmitActionLabel('نعم، حذف')
                ->modalCancelActionLabel('إلغاء'),

        ];
    }
}
