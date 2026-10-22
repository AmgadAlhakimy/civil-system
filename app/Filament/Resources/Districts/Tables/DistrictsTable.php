<?php

namespace App\Filament\Resources\Districts\Tables;

use App\Models\Governorate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DistrictsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ═══════════════════════════════════════════════
                // الاسم بالعربية
                // ═══════════════════════════════════════════════
                TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary'),

                // ═══════════════════════════════════════════════
                // المحافظة (Badge)
                // ═══════════════════════════════════════════════
                TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                // ═══════════════════════════════════════════════
                // الاسم بالإنجليزية
                // ═══════════════════════════════════════════════
                TextColumn::make('name_en')
                    ->label('بالإنكليزية')
                    ->searchable()
                    ->toggleable()
                    ->color('gray'),

                // ═══════════════════════════════════════════════
                // الرمز
                // ═══════════════════════════════════════════════
                TextColumn::make('code')
                    ->label('الرمز')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرمز')
                    ->fontFamily('mono')
                    ->placeholder('—')
                    ->toggleable(),

                // ═══════════════════════════════════════════════
                // الحالة
                // ═══════════════════════════════════════════════
                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                // ═══════════════════════════════════════════════
                // الترتيب (مخفي افتراضياً)
                // ═══════════════════════════════════════════════
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ═══════════════════════════════════════════════
                // التواريخ
                // ═══════════════════════════════════════════════
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

                // فلتر حسب المحافظة
                SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->options(fn () => Governorate::active()->ordered()->pluck('name_ar', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->native(false),

                // فلتر الحالة
                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('المفعّلة فقط')
                    ->falseLabel('المعطّلة فقط')
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
            ->emptyStateHeading('لا توجد مديريات بعد')
            ->emptyStateDescription('ابدأ بإضافة مديرية جديدة من زر "إنشاء".')
            ->emptyStateIcon('heroicon-o-map');
    }
}