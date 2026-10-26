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
                TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary'),

                TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_en')
                    ->label('بالإنكليزية')
                    ->searchable()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('code')
                    ->label('الرمز')
                    ->color('gray')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرمز')
                    ->fontFamily('mono')
                    ->placeholder('—')
                    ->toggleable(),

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
            ->defaultSort('governorate.name_ar', 'asc')
            ->striped()
            ->filters([
                SelectFilter::make('governorate_id')
                    ->label('المحافظة')
                    ->options(
                        fn () => Governorate::active()
                            ->ordered()
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
                    ->label('المحذوفة'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('تعديل')
                    ->visible(
                        fn ($record): bool => auth()->user()?->can('districts.update') ?? false
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف')
                        ->visible(
                            fn (): bool => auth()->user()?->can('districts.delete') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('districts.delete'),
                                403
                            );
                        }),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(
                            fn (): bool => auth()->user()?->can('districts.force_delete') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('districts.force_delete'),
                                403
                            );
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة')
                        ->visible(
                            fn (): bool => auth()->user()?->can('districts.restore') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('districts.restore'),
                                403
                            );
                        }),
                ]),
            ])
            ->emptyStateHeading('لا توجد مديريات بعد')
            ->emptyStateDescription('ابدأ بإضافة مديرية جديدة من زر "إنشاء".')
            ->emptyStateIcon('heroicon-o-map');
    }
}
