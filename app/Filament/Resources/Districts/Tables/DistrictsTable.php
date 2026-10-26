<?php

namespace App\Filament\Resources\Districts\Tables;

use App\Models\District;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DistrictsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label('اسم المديرية بالعربية')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name_en')
                    ->label('اسم المديرية بالإنجليزية')
                    ->searchable()
                    ->sortable()
                    ->copyMessage('تم نسخ اسم المديرية بالإنجليزية'),

                TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->searchable()
                    ->sortable()
                    ->color('info'),

                TextColumn::make('code')
                    ->label('رمز المديرية')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رمز المديرية')
                    ->fontFamily('mono'),

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
            ->defaultSort('governorate.name_ar', 'asc')
            ->striped()
            ->filters([
                SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->options(
                        fn (): array => Governorate::query()
                            ->orderBy('name_ar')
                            ->pluck('name_ar', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

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
                        fn (): bool => auth()->user()?->can('districts.view') ?? false
                    ),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(
                        fn (): bool => auth()->user()?->can('districts.update') ?? false
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
                                (auth()->user()?->can('districts.delete') ?? false)
                                && in_array(
                                    $livewire->getTableFilterState('trashed')['value'] ?? null,
                                    [null, ''],
                                    true
                                )
                        )
                        ->requiresConfirmation()
                        ->modalHeading('حذف المديريات المحددة')
                        ->modalDescription(
                            'سيتم حذف المديريات المحددة. المديريات المرتبطة بفروع سيتم تجاوزها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            abort_unless(
                                auth()->user()?->can('districts.delete') ?? false,
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
                                    $district = District::withTrashed()
                                        ->find($record->getKey());

                                    if (! $district || $district->trashed()) {
                                        $skipped++;
                                        continue;
                                    }

                                    if (
                                        method_exists($district, 'branches')
                                        && $district->branches()->exists()
                                    ) {
                                        $skipped++;
                                        continue;
                                    }

                                    if ($district->delete()) {
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
                                    "تم حذف {$deleted} مديرية، "
                                    . "وتجاوز {$skipped} مديرية لوجود ارتباطات أو لتعذر حذفها."
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
                            fn (): bool => auth()->user()?->can('districts.restore') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('districts.restore') ?? false,
                                403
                            );
                        }),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(
                            fn (): bool => auth()->user()?->can('districts.force_delete') ?? false
                        )
                        ->requiresConfirmation()
                        ->modalHeading('الحذف النهائي للمديريات')
                        ->modalDescription(
                            'سيتم حذف المديريات المحددة نهائيًا. لا يمكن التراجع عن هذا الإجراء.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            abort_unless(
                                auth()->user()?->can('districts.force_delete') ?? false,
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
                                    $district = District::withTrashed()
                                        ->find($record->getKey());

                                    if (! $district) {
                                        $skipped++;
                                        continue;
                                    }

                                    if (
                                        method_exists($district, 'branches')
                                        && $district->branches()->withTrashed()->exists()
                                    ) {
                                        $skipped++;
                                        continue;
                                    }

                                    if ($district->forceDelete()) {
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
                                    "تم حذف {$deleted} مديرية نهائيًا، "
                                    . "وتجاوز {$skipped} مديرية لوجود ارتباطات أو لتعذر حذفها."
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
            ->emptyStateHeading('لا توجد مديريات')
            ->emptyStateDescription(
                'يمكنك إضافة مديرية جديدة باستخدام زر الإضافة.'
            )
            ->emptyStateIcon('heroicon-o-map');
    }
}
