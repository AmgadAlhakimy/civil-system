<?php

namespace App\Filament\Resources\FamilyCards\Pages;

use App\Filament\Resources\FamilyCards\FamilyCardResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewFamilyCard extends ViewRecord
{
    protected static string $resource = FamilyCardResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('family_cards.view'),
            403
        );

        parent::mount($record);
    }

    public function getTitle(): string
    {
        return 'بيانات البطاقة العائلية: ' . $this->record->card_number;
    }

    protected function performCardOperation(
        string $expectedStatus,
        string $operationLabel
    ): void {
        if (! auth()->user()?->can('family_cards.update')) {
            abort(403);
        }

        $this->record->refresh();

        if ($this->record->status !== $expectedStatus) {
            Notification::make()
                ->title('لا يمكن تنفيذ العملية')
                ->body('حالة البطاقة تغيرت ولم تعد تسمح بتنفيذ هذه العملية.')
                ->warning()
                ->send();

            return;
        }

        $issueDate = now();

        $expiryDate = $issueDate
            ->copy()
            ->addYears($this->record->validityYears());

        $this->record->update([
            'status' => 'active',
            'issue_date' => $issueDate->toDateString(),
            'expiry_date' => $expiryDate->toDateString(),
        ]);

        $this->record->refresh();

        $this->refreshFormData([
            'status',
            'issue_date',
            'expiry_date',
        ]);

        Notification::make()
            ->title('تمت العملية بنجاح')
            ->body(
                'تم تنفيذ عملية ' . $operationLabel .
                ' وأصبحت البطاقة سارية لمدة 10 سنوات.'
            )
            ->success()
            ->send();
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
                    'هل أنت متأكد من اعتماد هذه البطاقة؟ سيتم تحديد تاريخ الإصدار تلقائيًا، وتكون مدة الصلاحية 10 سنوات، وستصبح البطاقة سارية.'
                )
                ->modalSubmitActionLabel('نعم، اعتماد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'pending'
                        && (auth()->user()?->can('family_cards.approve') ?? false)
                )
                ->action(function (): void {
                    if (! auth()->user()?->can('family_cards.approve')) {
                        abort(403);
                    }

                    $this->record->refresh();

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
                            ->addYears($this->record->validityYears())
                            ->toDateString(),
                        'rejection_reason' => null,
                    ]);

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                        'expiry_date',
                        'rejection_reason',
                    ]);

                    Notification::make()
                        ->title('تم اعتماد البطاقة بنجاح')
                        ->body('تم إصدار البطاقة العائلية وأصبحت سارية لمدة 10 سنوات.')
                        ->success()
                        ->send();
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
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'active'
                        && (auth()->user()?->can('family_cards.print') ?? false)
                )
                ->action(function () {
                    if (! auth()->user()?->can('family_cards.print')) {
                        abort(403);
                    }

                    $this->record->refresh();

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

                    if ($this->record->expiry_date->isBefore(today())) {
                        $this->record->update([
                            'status' => 'expired',
                        ]);

                        $this->record->refresh();

                        $this->refreshFormData([
                            'status',
                        ]);

                        Notification::make()
                            ->title('لا يمكن طباعة البطاقة')
                            ->body('انتهت صلاحية البطاقة، يجب تجديدها أولًا.')
                            ->warning()
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

            Action::make('renew')
                ->label('تجديد البطاقة')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('تجديد البطاقة العائلية')
                ->modalDescription(
                    'هل أنت متأكد من تجديد البطاقة؟ سيتم تحديث تاريخ الإصدار والانتهاء، وستصبح البطاقة سارية لمدة 10 سنوات.'
                )
                ->modalSubmitActionLabel('نعم، تجديد البطاقة')
                ->modalCancelActionLabel('إلغاء')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'expired'
                        && (auth()->user()?->can('family_cards.update') ?? false)
                )
                ->action(
                    fn () => $this->performCardOperation(
                        'expired',
                        'تجديد البطاقة'
                    )
                ),

            Action::make('replaceLost')
                ->label('بدل فاقد')
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('إصدار بدل فاقد')
                ->modalDescription(
                    'هل أنت متأكد من إصدار بدل فاقد لهذه البطاقة؟ ستبقى البطاقة بنفس الرقم، وسيتم تحديث تاريخ الإصدار والانتهاء لمدة 10 سنوات.'
                )
                ->modalSubmitActionLabel('نعم، إصدار بدل فاقد')
                ->modalCancelActionLabel('إلغاء')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'lost'
                        && (auth()->user()?->can('family_cards.update') ?? false)
                )
                ->action(
                    fn () => $this->performCardOperation(
                        'lost',
                        'إصدار بدل فاقد'
                    )
                ),

            Action::make('replaceDamaged')
                ->label('بدل تالف')
                ->icon('heroicon-o-document-check')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('إصدار بدل تالف')
                ->modalDescription(
                    'هل أنت متأكد من إصدار بدل تالف لهذه البطاقة؟ ستبقى البطاقة بنفس الرقم، وسيتم تحديث تاريخ الإصدار والانتهاء لمدة 10 سنوات.'
                )
                ->modalSubmitActionLabel('نعم، إصدار بدل تالف')
                ->modalCancelActionLabel('إلغاء')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'damaged'
                        && (auth()->user()?->can('family_cards.update') ?? false)
                )
                ->action(
                    fn () => $this->performCardOperation(
                        'damaged',
                        'إصدار بدل تالف'
                    )
                ),

            Action::make('reject')
                ->label('رفض البطاقة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'pending'
                        && (auth()->user()?->can('family_cards.reject') ?? false)
                )
                ->schema([
                    Textarea::make('rejection_reason')
                        ->label('سبب رفض البطاقة')
                        ->placeholder('اكتب سبب رفض البطاقة بالتفصيل')
                        ->required()
                        ->minLength(5)
                        ->maxLength(100)
                        ->rows(4)
                        ->validationMessages([
                            'required' => 'يجب كتابة سبب رفض البطاقة.',
                            'min' => 'يجب ألا يقل سبب الرفض عن 5 أحرف.',
                            'max' => 'يجب ألا يتجاوز سبب الرفض 100 حرف.',
                        ]),
                ])
                ->requiresConfirmation()
                ->modalHeading('رفض البطاقة العائلية')
                ->modalDescription(
                    'اكتب سبب الرفض ثم أكد العملية. سيُحفظ السبب ضمن بيانات البطاقة.'
                )
                ->modalSubmitActionLabel('تأكيد الرفض')
                ->modalCancelActionLabel('إلغاء')
                ->action(function (array $data): void {
                    if (! auth()->user()?->can('family_cards.reject')) {
                        abort(403);
                    }

                    $this->record->refresh();

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
                        'rejection_reason' => trim($data['rejection_reason']),
                        'approved_by' => null,
                        'approved_at' => null,
                        'issue_date' => null,
                        'expiry_date' => null,
                    ]);

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'rejection_reason',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                        'expiry_date',
                    ]);

                    Notification::make()
                        ->title('تم رفض البطاقة')
                        ->body('تم حفظ سبب رفض البطاقة العائلية بنجاح.')
                        ->danger()
                        ->send();
                }),

            Action::make('resubmit')
                ->label('إعادة التقديم')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'rejected'
                        && (auth()->user()?->can('family_cards.resubmit') ?? false)
                )
                ->requiresConfirmation()
                ->modalHeading('إعادة تقديم طلب البطاقة')
                ->modalDescription(
                    'هل تريد إعادة تقديم الطلب؟ ستتغير الحالة إلى قيد الانتظار، وسيُمسح سبب الرفض السابق.'
                )
                ->modalSubmitActionLabel('نعم، إعادة التقديم')
                ->modalCancelActionLabel('إلغاء')
                ->action(function (): void {
                    if (! auth()->user()?->can('family_cards.resubmit')) {
                        abort(403);
                    }

                    $this->record->refresh();

                    if ($this->record->status !== 'rejected') {
                        Notification::make()
                            ->title('لا يمكن إعادة التقديم')
                            ->body('البطاقة ليست في حالة الرفض.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->update([
                        'status' => 'pending',
                        'rejection_reason' => null,
                        'approved_by' => null,
                        'approved_at' => null,
                        'issue_date' => null,
                        'expiry_date' => null,
                    ]);

                    $this->record->refresh();

                    $this->refreshFormData([
                        'status',
                        'rejection_reason',
                        'approved_by',
                        'approved_at',
                        'issue_date',
                        'expiry_date',
                    ]);

                    Notification::make()
                        ->title('تمت إعادة تقديم الطلب')
                        ->body('أصبحت البطاقة العائلية قيد الانتظار وجاهزة للمراجعة.')
                        ->success()
                        ->send();
                }),

            EditAction::make()
                ->label('تعديل البطاقة')
                ->icon('heroicon-o-pencil-square')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('family_cards.update') ?? false
                ),

            DeleteAction::make()
                ->label('حذف البطاقة')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(
                    fn (): bool =>
                        auth()->user()?->can('family_cards.delete') ?? false
                )
                ->requiresConfirmation()
                ->modalHeading('حذف البطاقة العائلية')
                ->modalDescription(
                    'هل أنت متأكد من حذف هذه البطاقة العائلية؟ سيتم نقلها إلى سلة المحذوفات ويمكن استعادتها لاحقًا.'
                )
                ->modalSubmitActionLabel('نعم، حذف')
                ->modalCancelActionLabel('إلغاء'),
        ];
    }
}
