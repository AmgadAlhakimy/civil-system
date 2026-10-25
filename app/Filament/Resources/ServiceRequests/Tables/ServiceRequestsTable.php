<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
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
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                        default => $state,
                    })
                    ->sortable(),

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
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
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

                TrashedFilter::make()->label('المحذوفة'),
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

                Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('الموافقة على الطلب')
                    ->modalDescription('سيتم اعتماد الطلب وستُنشأ البطاقة/الوثيقة تلقائياً.')
                    ->modalSubmitActionLabel('نعم، موافقة')
                    ->visible(fn (ServiceRequest $record): bool =>
                        (auth()->user()?->can('service_requests.approve') ?? false)
                        && in_array($record->status, ['pending', 'processing'], true)
                    )
                    ->action(function (ServiceRequest $record): void {
                        abort_unless(
                            auth()->user()?->can('service_requests.approve'),
                            403
                        );

                        abort_unless(
                            in_array($record->status, ['pending', 'processing'], true),
                            403
                        );

                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        if ($record->category === 'id_card') {
                            $record->createIdentityCard();
                        }

                        Notification::make()
                            ->title('تم اعتماد الطلب')
                            ->body('تم اعتماد الطلب رقم ' . $record->request_number)
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->modalHeading('رفض الطلب')
                    ->modalSubmitActionLabel('نعم، رفض')
                    ->visible(fn (ServiceRequest $record): bool =>
                        (auth()->user()?->can('service_requests.reject') ?? false)
                        && in_array($record->status, ['pending', 'processing'], true)
                    )
                    ->action(function (ServiceRequest $record, array $data): void {
                        abort_unless(
                            auth()->user()?->can('service_requests.reject'),
                            403
                        );

                        abort_unless(
                            in_array($record->status, ['pending', 'processing'], true),
                            403
                        );

                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('تم رفض الطلب')
                            ->body('الطلب رقم ' . $record->request_number)
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف')
                        ->visible(fn (): bool =>
                            auth()->user()?->can('service_requests.delete') ?? false
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
