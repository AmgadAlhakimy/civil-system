<?php

namespace App\Filament\Resources\Passports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PassportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('passport_number')
                    ->label('رقم الجواز')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الجواز')
                    ->weight('bold')
                    ->icon('heroicon-o-identification'),

                TextColumn::make('citizen.full_name')
                    ->label('المواطن')
                    ->getStateUsing(fn ($record) => trim(
                        "{$record->citizen?->first_name} {$record->citizen?->father_name} {$record->citizen?->middle_name} {$record->citizen?->last_name}"
                    ))
                    ->searchable(
                        query: function ($query, string $search): void {
                            $query->whereHas('citizen', function ($query) use ($search) {
                                $query
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('father_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                            });
                        }
                    )
                    ->sortable(),

                TextColumn::make('type')
                    ->label('نوع الجواز')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'ordinary' => 'عادي',
                            'diplomatic' => 'دبلوماسي',
                            'official' => 'رسمي',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'قيد الانتظار',
                            'approved' => 'معتمد',
                            'rejected' => 'مرفوض',
                            'active' => 'فعال',
                            'expired' => 'منتهي',
                            'cancelled' => 'ملغي',
                            'lost' => 'مفقود',
                            'damaged' => 'تالف',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('issue_date')
                    ->label('تاريخ الإصدار')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('issuedBy.name')
                    ->label('تم الإصدار بواسطة')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approvedBy.name')
                    ->label('تم الاعتماد بواسطة')
                    ->sortable()
                    ->placeholder('لم يتم الاعتماد')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approved_at')
                    ->label('تاريخ الاعتماد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('print_count')
                    ->label('مرات الطباعة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('تاريخ الحذف')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('type')
                    ->label('نوع الجواز')
                    ->options([
                        'ordinary' => 'عادي',
                        'diplomatic' => 'دبلوماسي',
                        'official' => 'رسمي',
                    ]),

                SelectFilter::make('status')
                    ->label('حالة الجواز')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'approved' => 'معتمد',
                        'rejected' => 'مرفوض',
                        'active' => 'فعال',
                        'expired' => 'منتهي',
                        'cancelled' => 'ملغي',
                        'lost' => 'مفقود',
                        'damaged' => 'تالف',
                    ]),

                TrashedFilter::make()
                    ->label('سلة المحذوفات'),
            ])

            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('حذف الجوازات المحددة')
                        ->modalDescription(
                            'هل أنت متأكد من حذف الجوازات المحددة؟ يمكن استعادتها من سلة المحذوفات.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف'),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة الجوازات')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة الجوازات المحددة؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'تحذير: سيتم حذف الجوازات نهائيًا ولا يمكن استعادتها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي'),
                ])
                    ->label('إجراءات جماعية'),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
