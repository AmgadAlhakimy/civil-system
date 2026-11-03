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
use Filament\Actions\ViewAction;
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
                    ->wrap(),

                TextColumn::make('category')
                    ->label('تصنيف الخدمة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'identity_card' => 'بطاقة شخصية',
                            'passport' => 'جواز سفر',
                            'family_card' => 'بطاقة عائلية',
                            'birth_certificate' => 'شهادة ميلاد',
                            'death_certificate' => 'شهادة وفاة',
                            'other' => 'خدمات أخرى',
                            default => 'غير محدد',
                        }
                    )
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('قيمة الرسوم')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->color('success')
                    ->formatStateUsing(
                        fn ($state): string => number_format(
                                (float) $state,
                                2
                            ) . ' ريال يمني'
                    ),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('updatedBy.full_name')
                    ->label('آخر تعديل بواسطة')
                    ->placeholder('غير محدد')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->placeholder('لا يوجد')
                    ->tooltip(
                        fn ($record): ?string => $record->updated_at
                            ?->format('Y/m/d H:i')
                    ),

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
                    ->label('تصنيف الخدمة')
                    ->options([
                        'identity_card' => 'بطاقة شخصية',
                        'passport' => 'جواز سفر',
                        'family_card' => 'بطاقة عائلية',
                        'birth_certificate' => 'شهادة ميلاد',
                        'death_certificate' => 'شهادة وفاة',
                        'other' => 'خدمات أخرى',
                    ])
                    ->native(false)
                    ->multiple(),

                TernaryFilter::make('is_active')
                    ->label('حالة الخدمة')
                    ->placeholder('جميع الخدمات')
                    ->trueLabel('الخدمات المفعّلة')
                    ->falseLabel('الخدمات المعطّلة')
                    ->native(false),

                TrashedFilter::make()
                    ->label('سلة المحذوفات'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->visible(
                        fn (): bool => auth()->user()?->can(
                            'service_fees.view'
                        ) ?? false
                    ),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(
                        fn (): bool => auth()->user()?->can(
                            'service_fees.update'
                        ) ?? false
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->visible(
                            fn (): bool => auth()->user()?->can(
                                'service_fees.delete'
                            ) ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can('service_fees.delete'),
                                403
                            );
                        }),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(
                            fn (): bool => auth()->user()?->can(
                                'service_fees.force_delete'
                            ) ?? false
                        )
                        ->before(function (): void {
                            abort_unless(
                                auth()->user()?->can(
                                    'service_fees.force_delete'
                                ),
                                403
                            );
                        }),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->visible(
                            fn (): bool => auth()->user()?->can(
                                'service_fees.restore'
                            ) ?? false
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
            ->emptyStateDescription(
                'ابدأ بإضافة خدمة جديدة من زر «إنشاء».'
            )
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
