<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Filament\Filters\TwoOptionTrashedFilter;
use App\Models\RequestStatusHistory;
use App\Models\ServiceRequest;
use App\Services\Requests\ApproveRequestService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الطلب')
                    ->fontFamily('mono')
                    ->weight('bold'),

                TextColumn::make('service_name')
                    ->label('الخدمة')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('category')
                    ->label('الفئة')
                    ->color(fn (string $state): string => match ($state) {
                        'id_card' => 'primary',
                        'passport' => 'success',
                        'family_card' => 'warning',
                        'certificate' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'id_card' => 'بطاقة شخصية',
                        'passport' => 'جواز سفر',
                        'family_card' => 'بطاقة عائلية',
                        'certificate' => 'شهادات',
                        default => $state,
                    }),

                TextColumn::make('user.name')
                    ->label('صاحب الطلب')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->icon('heroicon-o-banknotes')
                    ->formatStateUsing(
                        fn ($state, $record) =>
                            number_format((float) $state, 0) . ' ' . $record->currency
                    ),

                TextColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'مدفوع',
                        'pending' => 'بانتظار الدفع',
                        'failed' => 'فشل',
                        default => $state,
                    }),

                TextColumn::make('status')
                    ->label('حالة الطلب')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'printing' => 'warning',
                        'ready' => 'success',
                        'delivered' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                        'printing' => 'قيد الطباعة',
                        'ready' => 'جاهز للاستلام',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                        default => $state,
                    })
                    ->sortable(),

                // ✅ عمود جديد — عدد مرات إعادة الإرسال
                TextColumn::make('resubmission_count')
                    ->label('إعادة الإرسال')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state === 1 => 'info',
                        $state === 2 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state . ' / 3')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->toggleable()
                    ->placeholder('غير محدد'),

                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->since(),

                TextColumn::make('reviewedBy.name')
                    ->label('المراجع')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reviewed_at')
                    ->label('تاريخ المراجعة')
                    ->dateTime('Y/m/d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                SelectFilter::make('status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending'    => 'قيد الانتظار',
                        'processing' => 'قيد المراجعة',
                        'approved'   => 'مقبول',
                        'rejected'   => 'مرفوض',
                        'printing'   => 'قيد الطباعة',
                        'ready'      => 'جاهز للاستلام',
                        'delivered'  => 'تم التسليم',
                        'cancelled'  => 'ملغي',
                    ])
                    ->multiple()
                    ->native(false),

                SelectFilter::make('category')
                    ->label('الفئة')
                    ->options([
                        'id_card' => 'بطاقة شخصية',
                        'passport' => 'جواز سفر',
                        'family_card' => 'بطاقة عائلية',
                        'certificate' => 'شهادات',
                    ])
                    ->multiple()
                    ->native(false),

                SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'paid' => 'مدفوع',
                        'pending' => 'بانتظار الدفع',
                        'failed' => 'فشل',
                    ])
                    ->native(false),

                TwoOptionTrashedFilter::make()
                    ->label('سلة المحذوفات'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->visible(fn (): bool =>
                        auth()->user()?->can('service_requests.view') ?? false
                    ),

                EditAction::make()
                    ->label('تعديل')
                    ->visible(fn (): bool =>
                        auth()->user()?->can('service_requests.update') ?? false
                    ),

                // ─── بدء المراجعة (pending → processing) ───
                Action::make('startProcessing')
                    ->label('بدء المراجعة')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('بدء مراجعة الطلب')
                    ->modalDescription('سيتم نقل الطلب إلى حالة "قيد المراجعة".')
                    ->visible(fn (ServiceRequest $record): bool =>
                        $record->status === 'pending'
                        && (auth()->user()?->can('service_requests.approve') ?? false)
                    )
                    ->action(function (ServiceRequest $record): void {
                        $result = app(ApproveRequestService::class)->updateStatus(
                            $record,
                            auth()->user(),
                            'processing',
                            'بدء المراجعة',
                        );

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title('تم نقل الطلب إلى: قيد المراجعة')
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('فشل')
                                ->body($result['error'] ?? 'خطأ')
                                ->send();
                        }
                    }),

                // ─── اعتماد (pending/processing → approved) ───
                Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('اعتماد الطلب')
                    ->modalDescription('سيتم تخزين البيانات في جدول المواطن وجدول الخدمة.')
                    ->modalSubmitActionLabel('نعم، اعتماد')
                    ->form([
                        Textarea::make('notes')
                            ->label('ملاحظات (اختياري)')
                            ->rows(2),
                    ])
                    ->visible(fn (ServiceRequest $record): bool =>
                        (auth()->user()?->can('service_requests.approve') ?? false)
                        && in_array($record->status, ['pending', 'processing'], true)
                    )
                    ->action(function (ServiceRequest $record, array $data): void {
                        abort_unless(
                            auth()->user()?->can('service_requests.approve'),
                            403
                        );

                        $result = app(ApproveRequestService::class)->approve(
                            $record,
                            auth()->user(),
                            $data['notes'] ?? null,
                        );

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title('تم اعتماد الطلب')
                                ->body('تم تخزين البيانات بنجاح للطلب رقم ' . $record->request_number)
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('فشل الاعتماد')
                                ->body($result['error'] ?? 'خطأ غير معروف')
                                ->send();
                        }
                    }),

                // ─── ✅ رفض (pending/processing → rejected) — مع Toggle ───
                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('رفض الطلب')
                    ->modalDescription('أدخل سبب الرفض. إذا كان الرفض بسبب وثائق/بيانات، فعّل خيار السماح بالتعديل.')
                    ->modalSubmitActionLabel('نعم، رفض')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->maxLength(1000)
                            ->rows(3),
                        Toggle::make('is_resubmittable')
                            ->label('السماح للمواطن بالتعديل وإعادة الإرسال')
                            ->helperText('فعّل إذا كان الرفض بسبب وثائق ناقصة أو بيانات خاطئة')
                            ->default(true),
                    ])
                    ->visible(fn (ServiceRequest $record): bool =>
                        (auth()->user()?->can('service_requests.reject') ?? false)
                        && in_array($record->status, ['pending', 'processing'], true)
                    )
                    ->action(function (ServiceRequest $record, array $data): void {
                        abort_unless(
                            auth()->user()?->can('service_requests.reject'),
                            403
                        );

                        $oldStatus = $record->status;

                        // ✅ حفظ الحالة في history
                        RequestStatusHistory::create([
                            'service_request_id' => $record->id,
                            'from_status' => $oldStatus,
                            'to_status' => 'rejected',
                            'notes' => $data['rejection_reason'],
                            'changed_by' => auth()->id(),
                        ]);

                        // ✅ تحديث الطلب مع is_resubmittable
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'is_resubmittable' => (bool) ($data['is_resubmittable'] ?? false),
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('تم رفض الطلب')
                            ->body(
                                'الطلب رقم ' . $record->request_number
                                . (($data['is_resubmittable'] ?? false)
                                    ? ' — يمكن للمواطن التعديل وإعادة الإرسال'
                                    : ' — الرفض نهائي')
                            )
                            ->send();
                    }),

                // ─── إرسال للطباعة (approved → printing) ───
                Action::make('sendToPrint')
                    ->label('إرسال للطباعة')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('إرسال الطلب للطباعة')
                    ->modalDescription('سيتم نقل الطلب إلى حالة "قيد الطباعة".')
                    ->visible(fn (ServiceRequest $record): bool =>
                        $record->status === 'approved'
                        && (auth()->user()?->can('service_requests.approve') ?? false)
                    )
                    ->action(function (ServiceRequest $record): void {
                        $result = app(ApproveRequestService::class)->updateStatus(
                            $record,
                            auth()->user(),
                            'printing',
                            'تم إرسال الطلب للطباعة',
                        );

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title('تم نقل الطلب إلى: قيد الطباعة')
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('فشل')
                                ->body($result['error'] ?? 'خطأ')
                                ->send();
                        }
                    }),

                // ─── جاهز للاستلام (printing → ready) ───
                Action::make('markReady')
                    ->label('جاهز للاستلام')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد جاهزية الطلب')
                    ->modalDescription('سيتم نقل الطلب إلى حالة "جاهز للاستلام".')
                    ->visible(fn (ServiceRequest $record): bool =>
                        $record->status === 'printing'
                        && (auth()->user()?->can('service_requests.approve') ?? false)
                    )
                    ->action(function (ServiceRequest $record): void {
                        $result = app(ApproveRequestService::class)->updateStatus(
                            $record,
                            auth()->user(),
                            'ready',
                            'الطلب جاهز للاستلام',
                        );

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title('تم نقل الطلب إلى: جاهز للاستلام')
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('فشل')
                                ->body($result['error'] ?? 'خطأ')
                                ->send();
                        }
                    }),

                // ─── تم التسليم (ready → delivered) ───
                Action::make('markDelivered')
                    ->label('تم التسليم')
                    ->icon('heroicon-o-hand-thumb-up')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد تسليم الطلب')
                    ->modalDescription('سيتم نقل الطلب إلى حالة "تم التسليم".')
                    ->form([
                        Textarea::make('notes')
                            ->label('ملاحظات (اختياري)')
                            ->rows(2),
                    ])
                    ->visible(fn (ServiceRequest $record): bool =>
                        $record->status === 'ready'
                        && (auth()->user()?->can('service_requests.approve') ?? false)
                    )
                    ->action(function (ServiceRequest $record, array $data): void {
                        $result = app(ApproveRequestService::class)->updateStatus(
                            $record,
                            auth()->user(),
                            'delivered',
                            $data['notes'] ?? 'تم تسليم الطلب للمواطن',
                        );

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title('تم تسليم الطلب بنجاح')
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('فشل')
                                ->body($result['error'] ?? 'خطأ')
                                ->send();
                        }
                    }),
            ])
            ->recordActionsColumnLabel('العمليات')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف')
                        ->visible(
                            fn ($livewire): bool =>
                                (auth()->user()?->can('identity_cards.delete') ?? false)
                                && ! (bool) data_get($livewire->tableFilters, 'trashed.value', 0)
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('service_requests.delete'),
                                403
                            );
                        }),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(fn (): bool =>
                            auth()->user()?->can('service_requests.force_delete') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('service_requests.force_delete'),
                                403
                            );
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة')
                        ->visible(fn (): bool =>
                            auth()->user()?->can('service_requests.restore') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('service_requests.restore'),
                                403
                            );
                        }),
                ]),
            ])
            ->emptyStateHeading('لا توجد طلبات بعد')
            ->emptyStateDescription('ستظهر طلبات المواطنين هنا عندما يقومون بها من التطبيق.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}