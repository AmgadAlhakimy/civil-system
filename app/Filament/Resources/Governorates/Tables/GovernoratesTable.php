<?php

namespace App\Filament\Resources\Governorates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class GovernoratesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ═══════════════════════════════════════════════
                // الاسم بالعربية (العمود الرئيسي)
                // ═══════════════════════════════════════════════
                TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-building-library')
                    ->iconColor('primary'),

                // ═══════════════════════════════════════════════
                // الاسم بالإنجليزية
                // ═══════════════════════════════════════════════
                TextColumn::make('name_en')
                    ->label('بالإنكليزية')
                    ->searchable()
                    ->toggleable()
                    ->color('gray'),

                // ═══════════════════════════════════════════════
                // الرمز (Badge)
                // ═══════════════════════════════════════════════
                TextColumn::make('code')
                    ->label('الرمز')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرمز')
                    ->fontFamily('mono'),

                // ═══════════════════════════════════════════════
                // عدد المديريات
                // ═══════════════════════════════════════════════
                TextColumn::make('districts_count')
                    ->label('المديريات')
                    ->counts('districts')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->sortable(),

                // ═══════════════════════════════════════════════
                // عدد الفروع
                // ═══════════════════════════════════════════════
                TextColumn::make('branches_count')
                    ->label('الفروع')
                    ->counts('branches')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable(),

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
                // الترتيب
                // ═══════════════════════════════════════════════
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ═══════════════════════════════════════════════
                // التواريخ (مخفية افتراضياً)
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
            ->defaultSort('sort_order', 'asc')
            ->striped()
            ->filters([

                // فلتر الحالة (مفعّلة / معطّلة)
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
            ->emptyStateHeading('لا توجد محافظات بعد')
            ->emptyStateDescription('ابدأ بإضافة محافظة جديدة من زر "إنشاء".')
            ->emptyStateIcon('heroicon-o-building-library');
    }
}