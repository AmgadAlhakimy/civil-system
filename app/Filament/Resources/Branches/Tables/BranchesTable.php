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
                    ->badge()
                    ->color('info')
                    ->fontFamily('mono'),

                TextColumn::make('name')
                    ->label('اسم الفرع')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('primary'),

                // ✅ المحافظة (جديد)
                TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محددة'),

                TextColumn::make('address')
                    ->label('العنوان')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الهاتف')
                    ->icon('heroicon-o-phone'),

                TextColumn::make('email')
                    ->label('البريد')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ البريد')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('manager.full_name')
                    ->label('المدير')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
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
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                // فلتر حسب المحافظة (جديد)
                SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->options(fn () => Governorate::active()->ordered()->pluck('name_ar', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('is_active')
                    ->label('حالة الفرع')
                    ->options([
                        1 => 'نشط',
                        0 => 'غير نشط',
                    ])
                    ->native(false),

                TrashedFilter::make()
                    ->label('المحذوفة'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('حذف المحدد')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->visible(
                            fn (HasTable $livewire): bool => in_array(
                                $livewire->getTableFilterState('trashed')['value'] ?? null,
                                [null, ''],
                                true
                            )
                        )
                        ->requiresConfirmation()
                        ->successNotification(null)
                        ->modalHeading('حذف الفروع المحددة')
                        ->modalDescription('سيتم حذف الفروع المسموح بحذفها، وتجاوز أي فرع مرتبط بمستخدمين.')
                        ->modalSubmitActionLabel('نعم، حذف')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $deleted = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if ($record->users()->exists()) {
                                    $skipped++;
                                    continue;
                                }
                                $record->delete();
                                $deleted++;
                            }

                            if ($skipped > 0) {
                                Notification::make()
                                    ->title('اكتملت العملية')
                                    ->body("تم حذف {$deleted} فرع، وتجاوز {$skipped} لارتباطها بمستخدمين.")
                                    ->warning()
                                    ->send();
                                return;
                            }

                            Notification::make()
                                ->title('تم الحذف بنجاح')
                                ->body("تم حذف {$deleted} فرع.")
                                ->success()
                                ->send();
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->action(function (Collection $records): void {
                            $deleted = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if ($record->users()->exists()) {
                                    $skipped++;
                                    continue;
                                }
                                $record->forceDelete();
                                $deleted++;
                            }

                            if ($skipped > 0) {
                                Notification::make()
                                    ->title('اكتمل الحذف النهائي')
                                    ->body("تم حذف {$deleted} نهائياً، وتجاوز {$skipped}.")
                                    ->warning()
                                    ->send();
                            }
                        }),
                ])->label('إجراءات جماعية'),
            ])
            ->emptyStateHeading('لا توجد فروع بعد')
            ->emptyStateDescription('ابدأ بإضافة فرع جديد من زر "إنشاء".')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}