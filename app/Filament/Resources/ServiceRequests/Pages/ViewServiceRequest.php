<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Jobs\ProcessStoredDocumentVerification;
use App\Models\ServiceRequest;
use App\Services\Requests\ApproveRequestService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            auth()->user()?->can('service_requests.view'),
            403
        );

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runAutomatedDocumentChecks')
                ->label('فحص المستندات آليًا')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('تشغيل الفحص الآلي للمرفقات')
                ->modalDescription('سيتم إرسال كل مرفق إلى مهمة مستقلة للفحص في الخلفية. الفحص آلي استرشادي ولا يصدر قرارًا نهائيًا.')
                ->visible(fn (ServiceRequest $record): bool =>
                    (auth()->user()?->can('service_requests.view') ?? false)
                    && is_array($record->documents)
                    && count($record->documents) > 0
                )
                ->action(function (ServiceRequest $record): void {
                    $documents = is_array($record->documents)
                        ? $record->documents
                        : [];

                    $queued = 0;
                    $invalid = [];

                    foreach ($documents as $reference => $attachment) {
                        if (
                            ! is_array($attachment)
                            || ! is_string($attachment['path'] ?? null)
                            || trim($attachment['path']) === ''
                        ) {
                            $invalid[] = (string) $reference;
                            continue;
                        }

                        ProcessStoredDocumentVerification::dispatch(
                            (string) $record->getKey(),
                            (string) $reference,
                            auth()->id()
                                ? (string) auth()->id()
                                : null,
                        );

                        $queued++;
                    }

                    if ($queued > 0) {
                        Notification::make()
                            ->success()
                            ->title('تم إرسال مهام الفحص')
                            ->body("تم إرسال {$queued} مرفق/مرفقات إلى قائمة الانتظار. ستظهر النتائج بعد انتهاء المعالجة.")
                            ->persistent()
                            ->send();
                    }

                    if ($invalid !== []) {
                        Notification::make()
                            ->warning()
                            ->title('بعض المرفقات لم تُرسل للفحص')
                            ->body(
                                'تعذر إرسال المرفقات التالية: '
                                . implode(', ', array_slice($invalid, 0, 5))
                            )
                            ->persistent()
                            ->send();
                    }
                }),

            Action::make('startReview')
                ->label('بدء مراجعة المستندات')
                ->icon('heroicon-o-document-magnifying-glass')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('بدء مراجعة الطلب')
                ->modalDescription('سيتم تسجيل بدء المراجعة باسم الموظف الحالي ونقل الطلب إلى حالة قيد المراجعة.')
                ->visible(fn (ServiceRequest $record): bool =>
                    $record->status === 'pending'
                    && (auth()->user()?->can('service_requests.approve') ?? false)
                )
                ->action(function (ServiceRequest $record): void {
                    $result = app(ApproveRequestService::class)->updateStatus(
                        $record,
                        auth()->user(),
                        'processing',
                        'بدأ الموظف مراجعة المستندات والبيانات.',
                    );

                    if ($result['success']) {
                        Notification::make()
                            ->success()
                            ->title('بدأت مراجعة الطلب')
                            ->body('تم تحديث الحالة إلى قيد المراجعة.')
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->danger()
                        ->title('تعذر بدء المراجعة')
                        ->body($result['error'] ?? 'حدث خطأ غير متوقع.')
                        ->send();
                }),

            Action::make('approveRequest')
                ->label('اعتماد الطلب')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->modalHeading('اعتماد الطلب بعد مراجعة المستندات')
                ->modalDescription('تأكد من مراجعة جميع المرفقات والبيانات قبل الاعتماد.')
                ->modalSubmitActionLabel('تأكيد الاعتماد')
                ->form([
                    Textarea::make('notes')
                        ->label('ملاحظات المراجع')
                        ->rows(3)
                        ->maxLength(1000)
                        ->placeholder('ملاحظات اختيارية حول نتيجة المراجعة...'),
                ])
                ->visible(fn (ServiceRequest $record): bool =>
                    in_array($record->status, ['pending', 'processing'], true)
                    && (auth()->user()?->can('service_requests.approve') ?? false)
                )
                ->action(function (ServiceRequest $record, array $data): void {
                    $result = app(ApproveRequestService::class)->approve(
                        $record,
                        auth()->user(),
                        $data['notes'] ?? null,
                    );

                    if ($result['success']) {
                        Notification::make()
                            ->success()
                            ->title('تم اعتماد الطلب')
                            ->body('تم تسجيل قرار الاعتماد وربطه بسجل الطلب.')
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->danger()
                        ->title('تعذر اعتماد الطلب')
                        ->body($result['error'] ?? 'حدث خطأ أثناء الاعتماد.')
                        ->send();
                }),

            Action::make('rejectRequest')
                ->label('إرجاع / رفض الطلب')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->modalHeading('إرجاع الطلب بسبب المستندات أو البيانات')
                ->modalDescription('اكتب سببًا واضحًا يساعد المواطن على معرفة المطلوب تصحيحه. فعّل إعادة الإرسال إذا كان بالإمكان استكمال النواقص.')
                ->modalSubmitActionLabel('تسجيل القرار')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('سبب الإرجاع أو الرفض')
                        ->required()
                        ->maxLength(1000)
                        ->rows(4)
                        ->placeholder('مثال: صورة المستند غير واضحة، يرجى إعادة رفع صورة واضحة للوجهين...'),
                    Toggle::make('is_resubmittable')
                        ->label('السماح للمواطن بتعديل الطلب وإعادة إرساله')
                        ->helperText('اترك الخيار مفعّلًا عند وجود مستند ناقص أو صورة غير واضحة أو بيانات قابلة للتصحيح.')
                        ->default(true),
                ])
                ->visible(fn (ServiceRequest $record): bool =>
                    in_array($record->status, ['pending', 'processing'], true)
                    && (auth()->user()?->can('service_requests.reject') ?? false)
                )
                ->action(function (ServiceRequest $record, array $data): void {
                    $result = app(ApproveRequestService::class)->reject(
                        $record,
                        auth()->user(),
                        $data['rejection_reason'],
                    );

                    if ($result['success']) {
                        $record->update([
                            'is_resubmittable' => (bool) ($data['is_resubmittable'] ?? false),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('تم تسجيل قرار المراجعة')
                            ->body(($data['is_resubmittable'] ?? false)
                                ? 'أُعيد الطلب للمواطن لاستكمال أو تصحيح المستندات.'
                                : 'تم رفض الطلب نهائيًا.')
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->danger()
                        ->title('تعذر تسجيل القرار')
                        ->body($result['error'] ?? 'حدث خطأ أثناء تحديث الطلب.')
                        ->send();
                }),

            Action::make('edit')
                ->label('تعديل بيانات الطلب')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->url(fn (ServiceRequest $record): string =>
                    ServiceRequestResource::getUrl('edit', ['record' => $record])
                )
                ->visible(fn (): bool =>
                    auth()->user()?->can('service_requests.update') ?? false
                ),
        ];
    }
}
