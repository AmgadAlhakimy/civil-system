<?php

namespace App\Filament\Resources\Branches\Tables;

use App\Models\Governorate;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('رمز الفرع')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رمز الفرع')
                    ->fontFamily('mono')
                    ->alignCenter(),

                TextColumn::make('name')
                    ->label('اسم الفرع')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محددة'),

                TextColumn::make('address')
                    ->label('العنوان')
                    ->searchable()
                    ->placeholder('غير محدد')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الهاتف')
                    ->placeholder('غير مسجل'),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ البريد الإلكتروني')
                    ->placeholder('غير مسجل')
                    ->toggleable(),

                TextColumn::make('manager.full_name')
                    ->label('مدير الفرع')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->since()
                    ->tooltip(
                        fn ($record): ?string => $record->updated_at
                            ?->format('Y/m/d H:i')
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->options(
                        fn (): array => Governorate::active()
                            ->ordered()
                            ->pluck('name_ar', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('is_active')
                    ->label('حالة الفرع')
                    ->options([
                        '1' => 'نشط',
                        '0' => 'غير نشط',
                    ])
                    ->native(false),

                TrashedFilter::make()
                    ->label('حالة الحذف'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->visible(
                        fn (): bool => auth()->user()?->can('branches.view') ?? false
                    ),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(
                        fn (): bool => auth()->user()?->can('branches.update') ?? false
                    ),
            ])
            ->recordActionsColumnLabel('العمليات')
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('حذف المحدد')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->visible(
                            fn (HasTable $livewire): bool =>
                                (auth()->user()?->can('branches.delete') ?? false)
                                && in_array(
                                    $livewire->getTableFilterState('trashed')['value'] ?? null,
                                    [null, ''],
                                    true
                                )
                        )
                        ->requiresConfirmation()
                        ->modalHeading('حذف الفروع المحددة')
                        ->modalDescription(
                            'سيتم حذف الفروع المحددة التي لا ترتبط بمستخدمين. '
                            . 'أما الفروع المرتبطة بمستخدمين فسيتم تجاوزها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            abort_unless(
                                auth()->user()?->can('branches.delete') ?? false,
                                403
                            );

                            $deleted = 0;
                            $skipped = 0;

                            DB::transaction(function () use (
                                $records,
                                &$deleted,
                                &$skipped
                            ): void {
                                foreach ($records as $record) {
                                    if ($record->users()->exists()) {
                                        $skipped++;
                                        continue;
                                    }

                                    if ($record->delete()) {
                                        $deleted++;
                                    }
                                }
                            });

                            $notification = Notification::make()
                                ->title(
                                    $skipped > 0
                                        ? 'اكتملت عملية الحذف'
                                        : 'تم الحذف بنجاح'
                                )
                                ->body(
                                    "تم حذف {$deleted} فرع، "
                                    . "وتجاوز {$skipped} فرع لارتباطه بمستخدمين."
                                );

                            if ($skipped > 0) {
                                $notification->warning();
                            } else {
                                $notification->success();
                            }

                            $notification->send();
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->visible(
                            fn (): bool => auth()->user()?->can('branches.restore') ?? false
                        ),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(
                            fn (): bool => auth()->user()?->can('branches.force_delete') ?? false
                        )
                        ->requiresConfirmation()
                        ->modalHeading('الحذف النهائي للفروع')
                        ->modalDescription(
                            'سيتم حذف الفروع المحددة نهائيًا، '
                            . 'ولا يمكن استعادتها بعد ذلك. '
                            . 'الفروع المرتبطة بمستخدمين سيتم تجاوزها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            abort_unless(
                                auth()->user()?->can('branches.force_delete') ?? false,
                                403
                            );

                            $deleted = 0;
                            $skipped = 0;

                            DB::transaction(function () use (
                                $records,
                                &$deleted,
                                &$skipped
                            ): void {
                                foreach ($records as $record) {
                                    if ($record->users()->exists()) {
                                        $skipped++;
                                        continue;
                                    }

                                    if ($record->forceDelete()) {
                                        $deleted++;
                                    }
                                }
                            });

                            $notification = Notification::make()
                                ->title(
                                    $skipped > 0
                                        ? 'اكتملت عملية الحذف النهائي'
                                        : 'تم الحذف النهائي بنجاح'
                                )
                                ->body(
                                    "تم حذف {$deleted} فرع نهائيًا، "
                                    . "وتجاوز {$skipped} فرع لارتباطه بمستخدمين."
                                );

                            if ($skipped > 0) {
                                $notification->warning();
                            } else {
                                $notification->success();
                            }

                            $notification->send();
                        }),
                ])->label('إجراءات جماعية'),
            ])
            ->emptyStateHeading('لا توجد فروع')
            ->emptyStateDescription(
                'يمكنك إضافة فرع جديد باستخدام زر إنشاء.'
            )
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}
