<?php

namespace App\Filament\Resources\Governorates\Tables;

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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GovernoratesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label('اسم المحافظة بالعربية')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->iconColor('primary'),

                TextColumn::make('name_en')
                    ->label('اسم المحافظة بالإنجليزية')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ اسم المحافظة بالإنجليزية'),

                TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرمز')
                    ->fontFamily('mono'),

                TextColumn::make('districts_count')
                    ->label('عدد المديريات')
                    ->counts('districts')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('branches_count')
                    ->label('عدد الفروع')
                    ->counts('branches')
                    ->alignCenter()
                    ->sortable(),

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
                    ->dateTime('Y/m/d H:i')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('تاريخ الحذف')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name_ar', 'asc')
            ->striped()
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('المفعّلة فقط')
                    ->falseLabel('المعطّلة فقط')
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
                        fn (): bool => auth()->user()?->can('governorates.view') ?? false
                    ),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(
                        fn (): bool => auth()->user()?->can('governorates.update') ?? false
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
                                (auth()->user()?->can('governorates.delete') ?? false)
                                && in_array(
                                    $livewire->getTableFilterState('trashed')['value'] ?? null,
                                    [null, ''],
                                    true
                                )
                        )
                        ->requiresConfirmation()
                        ->modalHeading('حذف المحافظات المحددة')
                        ->modalDescription(
                            'سيتم حذف المحافظات التي لا ترتبط بمديريات أو فروع. '
                            . 'أما المحافظات المرتبطة بها فسيتم تجاوزها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            abort_unless(
                                auth()->user()?->can('governorates.delete') ?? false,
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
                                    $governorate = Governorate::withTrashed()
                                        ->find($record->getKey());

                                    if (! $governorate || $governorate->trashed()) {
                                        $skipped++;
                                        continue;
                                    }

                                    if (
                                        $governorate->districts()->exists()
                                        || $governorate->branches()->exists()
                                    ) {
                                        $skipped++;
                                        continue;
                                    }

                                    if ($governorate->delete()) {
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
                                    "تم حذف {$deleted} محافظة، "
                                    . "وتجاوز {$skipped} محافظة لوجود ارتباطات أو لتعذر حذفها."
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
                            fn (): bool => auth()->user()?->can('governorates.restore') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('governorates.restore') ?? false,
                                403
                            );
                        }),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(
                            fn (): bool => auth()->user()?->can('governorates.force_delete') ?? false
                        )
                        ->requiresConfirmation()
                        ->modalHeading('الحذف النهائي للمحافظات')
                        ->modalDescription(
                            'سيتم حذف المحافظات المحددة نهائيًا. '
                            . 'المحافظات المرتبطة بمديريات أو فروع سيتم تجاوزها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            abort_unless(
                                auth()->user()?->can('governorates.force_delete') ?? false,
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
                                    $governorate = Governorate::withTrashed()
                                        ->find($record->getKey());

                                    if (! $governorate) {
                                        $skipped++;
                                        continue;
                                    }

                                    if (
                                        $governorate->districts()->withTrashed()->exists()
                                        || $governorate->branches()->withTrashed()->exists()
                                    ) {
                                        $skipped++;
                                        continue;
                                    }

                                    if ($governorate->forceDelete()) {
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
                                    "تم حذف {$deleted} محافظة نهائيًا، "
                                    . "وتجاوز {$skipped} محافظة لوجود ارتباطات أو لتعذر حذفها."
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
            ->emptyStateHeading('لا توجد محافظات')
            ->emptyStateDescription(
                'يمكنك إضافة محافظة جديدة باستخدام زر الإضافة.'
            )
            ->emptyStateIcon('heroicon-o-building-library');
    }
}
