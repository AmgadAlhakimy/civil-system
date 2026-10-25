<?php

namespace App\Filament\Resources\ServiceFees\Tables;

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

class ServiceFeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_name')
                    ->label('اسم الخدمة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('service_key')
                    ->label('المفتاح')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ المفتاح')
                    ->fontFamily('mono')
                    ->size('xs')
                    ->color('gray')
                    ->toggleable(),

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
                    })
                    ->sortable(),

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

                TextColumn::make('currency')
                    ->label('العملة')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y/m/d H:i')
                    ->since()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->updated_at?->format('Y/m/d H:i')),

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

                TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('مفعّلة فقط')
                    ->falseLabel('معطّلة فقط')
                    ->native(false),

                TrashedFilter::make()
                    ->label('المحذوفة'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('تعديل')
                    ->visible(fn (): bool =>
                        auth()->user()?->can('service_fees.update') ?? false
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف')
                        ->visible(fn (): bool =>
                            auth()->user()?->can('service_fees.delete') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('service_fees.delete'),
                                403
                            );
                        }),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(fn (): bool =>
                            auth()->user()?->can('service_fees.force_delete') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('service_fees.force_delete'),
                                403
                            );
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة')
                        ->visible(fn (): bool =>
                            auth()->user()?->can('service_fees.restore') ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('service_fees.restore'),
                                403
                            );
                        }),
                ]),
            ])
            ->emptyStateHeading('لا توجد رسوم بعد')
            ->emptyStateDescription('ابدأ بإضافة خدمة جديدة من زر "إنشاء".')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
