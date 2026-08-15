<?php

namespace App\Filament\Resources\FamilyCards\Tables;

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

class FamilyCardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('card_number')
                    ->label('رقم البطاقة')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم البطاقة')
                    ->weight('bold')
                    ->icon('heroicon-o-rectangle-stack'),

                TextColumn::make('head.full_name')
                    ->label('رب الأسرة')
                    ->getStateUsing(fn ($record) => trim(
                        "{$record->head?->first_name} {$record->head?->father_name} {$record->head?->middle_name} {$record->head?->last_name}"
                    ))
                    ->searchable(
                        query: function ($query, string $search): void {
                            $query->whereHas('head', function ($query) use ($search) {
                                $query
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('father_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                            });
                        }
                    )
                    ->sortable(),

                TextColumn::make('members_count')
                    ->label('أفراد الأسرة')
                    ->counts('members')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'قيد الانتظار',
                            'active' => 'سارية',
                            'expired' => 'منتهية',
                            'cancelled' => 'ملغاة',
                            'lost' => 'مفقودة',
                            'damaged' => 'تالفة',
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
                    ->placeholder('غير محدد')
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

                SelectFilter::make('status')
                    ->label('حالة البطاقة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'active' => 'سارية',
                        'expired' => 'منتهية',
                        'cancelled' => 'ملغاة',
                        'lost' => 'مفقودة',
                        'damaged' => 'تالفة',
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
                        ->modalHeading('حذف البطاقات المحددة')
                        ->modalDescription(
                            'هل أنت متأكد من حذف البطاقات المحددة؟ يمكن استعادتها من سلة المحذوفات.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف'),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة البطاقات')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة البطاقات المحددة؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'تحذير: سيتم حذف البطاقات نهائيًا ولا يمكن استعادتها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي'),

                ])
                    ->label('إجراءات جماعية'),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
