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
                TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-building-library')
                    ->iconColor('primary'),

                TextColumn::make('name_en')
                    ->label('بالإنكليزية')
                    ->searchable()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('code')
                    ->label('الرمز')
                    ->color('info')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرمز')
                    ->fontFamily('mono'),

                TextColumn::make('districts_count')
                    ->label('المديريات')
                    ->counts('districts')
                    ->color('warning')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('branches_count')
                    ->label('الفروع')
                    ->counts('branches')
                    ->color('success')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('المفعّلة فقط')
                    ->falseLabel('المعطّلة فقط')
                    ->native(false),

                TrashedFilter::make()
                    ->label('المحذوفة'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('تعديل')
                    ->visible(
                        fn (): bool => auth()->user()?->can('governorates.update') ?? false
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف')
                        ->visible(
                            fn (): bool => auth()->user()?->can('governorates.delete') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('governorates.delete'),
                                403
                            );
                        }),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(
                            fn (): bool => auth()->user()?->can('governorates.force_delete') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('governorates.force_delete'),
                                403
                            );
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة')
                        ->visible(
                            fn (): bool => auth()->user()?->can('governorates.restore') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('governorates.restore'),
                                403
                            );
                        }),
                ]),
            ])
            ->emptyStateHeading('لا توجد محافظات بعد')
            ->emptyStateDescription('ابدأ بإضافة محافظة جديدة من زر "إنشاء".')
            ->emptyStateIcon('heroicon-o-building-library');
    }
}
