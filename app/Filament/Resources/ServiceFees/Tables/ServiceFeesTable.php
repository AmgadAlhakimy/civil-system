<?php

namespace App\Filament\Resources\ServiceFees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ServiceFeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ═══════════════════════════════════════════════
                // اسم الخدمة (العمود الرئيسي)
                // ═══════════════════════════════════════════════
                TextColumn::make('service_name')
                    ->label('اسم الخدمة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                // ═══════════════════════════════════════════════
                // المفتاح (يظهر بخط صغير تحت الاسم)
                // ═══════════════════════════════════════════════
                TextColumn::make('service_key')
                    ->label('المفتاح')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ المفتاح')
                    ->fontFamily('mono')
                    ->size('xs')
                    ->color('gray')
                    ->toggleable(),

                // ═══════════════════════════════════════════════
                // الفئة (Badge ملون)
                // ═══════════════════════════════════════════════
                TextColumn::make('category')
                    ->label('الفئة')
                    ->badge()
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
                    })
                    ->sortable(),

                // ═══════════════════════════════════════════════
                // السعر (بخط كبير وبارز)
                // ═══════════════════════════════════════════════
                TextColumn::make('amount')
                    ->label('الرسوم')
                    ->numeric(decimalPlaces: 0)
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->icon('heroicon-o-banknotes')
                    ->formatStateUsing(function ($state, $record) {
                        return number_format((float) $state, 0) . ' ' . $record->currency;
                    }),

                // ═══════════════════════════════════════════════
                // العملة
                // ═══════════════════════════════════════════════
                TextColumn::make('currency')
                    ->label('العملة')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                // ═══════════════════════════════════════════════
                // الحالة (مفعّلة / معطّلة)
                // ═══════════════════════════════════════════════
                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                // ═══════════════════════════════════════════════
                // آخر تحديث
                // ═══════════════════════════════════════════════
                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y/m/d H:i')
                    ->since()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->updated_at?->format('Y/m/d H:i')),

                // ═══════════════════════════════════════════════
                // التواريخ (مخفية افتراضياً)
                // ═══════════════════════════════════════════════
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('تاريخ الحذف')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('category', 'asc')
            ->striped()
            ->filters([

                // فلتر حسب الفئة
                SelectFilter::make('category')
                    ->label('الفئة')
                    ->options([
                        'id_card' => 'بطاقة شخصية',
                        'passport' => 'جواز سفر',
                        'family_card' => 'بطاقة عائلية',
                        'certificate' => 'شهادات',
                    ])
                    ->native(false)
                    ->multiple(),

                // فلتر مفعّلة/معطّلة
                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('مفعّلة فقط')
                    ->falseLabel('معطّلة فقط')
                    ->native(false),

                // فلتر المحذوفة
                TrashedFilter::make()
                    ->label('المحذوفة'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف'),
                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي'),
                    RestoreBulkAction::make()
                        ->label('استعادة'),
                ]),
            ])
            ->emptyStateHeading('لا توجد رسوم بعد')
            ->emptyStateDescription('ابدأ بإضافة خدمة جديدة من زر "إنشاء".')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}