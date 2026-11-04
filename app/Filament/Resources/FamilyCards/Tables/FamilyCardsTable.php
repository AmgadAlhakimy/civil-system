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
                    ->fontFamily('mono')
                    ->size('md'),

                TextColumn::make('head.first_name')
                    ->label('رب الأسرة')
                    ->formatStateUsing(
                        fn ($record): string => trim(
                            $record->head?->first_name . ' ' .
                            $record->head?->father_name . ' ' .
                            $record->head?->middle_name . ' ' .
                            $record->head?->last_name
                        ) ?: 'غير محدد'
                    )
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('head', function ($query) use ($search): void {
                            $query->where('national_id', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('father_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function ($query, string $direction): void {
                        $query->orderBy('head_id', $direction);
                    }),

                TextColumn::make('members_count')
                    ->label('أفراد الأسرة')
                    ->counts('members')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'قيد الانتظار',
                            'approved' => 'معتمدة',
                            'rejected' => 'مرفوضة',
                            'active' => 'سارية',
                            'expired' => 'منتهية',
                            'cancelled' => 'ملغاة',
                            'lost' => 'مفقودة',
                            'damaged' => 'تالفة',
                            default => 'غير محددة',
                        }
                    )
                    ->searchable(query: function ($query, string $search): void {
                        $statuses = [
                            'pending' => ['pending', 'قيد الانتظار'],
                            'approved' => ['approved', 'معتمدة'],
                            'rejected' => ['rejected', 'مرفوضة'],
                            'active' => ['active', 'سارية'],
                            'expired' => ['expired', 'منتهية'],
                            'cancelled' => ['cancelled', 'ملغاة'],
                            'lost' => ['lost', 'مفقودة'],
                            'damaged' => ['damaged', 'تالفة'],
                        ];

                        $matchedStatuses = collect($statuses)
                            ->filter(
                                fn (array $labels): bool => collect($labels)
                                    ->contains(
                                        fn (string $label): bool => str_contains(
                                            mb_strtolower($label),
                                            mb_strtolower($search)
                                        )
                                    )
                            )
                            ->keys()
                            ->all();

                        if ($matchedStatuses) {
                            $query->whereIn('status', $matchedStatuses);
                        }
                    })
                    ->sortable(),

                TextColumn::make('issue_date')
                    ->label('تاريخ الإصدار')
                    ->date('Y-m-d')
                    ->placeholder('غير محدد')
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->placeholder('غير محدد')
                    ->sortable(),

                TextColumn::make('issuedBy.full_name')
                    ->label('تم إنشاء الطلب بواسطة')
                    ->sortable()
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approvedBy.full_name')
                    ->label('تم الاعتماد بواسطة')
                    ->sortable()
                    ->placeholder('لم يتم الاعتماد')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approved_at')
                    ->label('تاريخ الاعتماد')
                    ->dateTime()
                    ->placeholder('غير محدد')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('print_count')
                    ->label('عدد مرات الطباعة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('تاريخ الحذف')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'approved' => 'معتمدة',
                        'rejected' => 'مرفوضة',
                        'active' => 'سارية',
                        'expired' => 'منتهية',
                        'cancelled' => 'ملغاة',
                        'lost' => 'مفقودة',
                        'damaged' => 'تالفة',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->visible(
                        fn (): bool => auth()->user()?->can('family_cards.view') ?? false
                    ),

                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->visible(
                        fn (): bool => auth()->user()?->can('family_cards.update') ?? false
                    ),
            ])
            ->recordActionsColumnLabel('العمليات')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('حذف البطاقات العائلية المحددة')
                        ->modalDescription('هل أنت متأكد من حذف البطاقات العائلية المحددة؟')
                        ->modalSubmitActionLabel('نعم، احذف')
                        ->visible(
                            fn (): bool => auth()->user()?->can('family_cards.delete') ?? false
                        ),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة البطاقات العائلية المحددة')
                        ->modalDescription('هل أنت متأكد من استعادة البطاقات العائلية المحددة؟')
                        ->modalSubmitActionLabel('نعم، استعد')
                        ->visible(
                            fn (): bool => auth()->user()?->can('family_cards.restore') ?? false
                        ),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف البطاقات العائلية نهائيًا')
                        ->modalDescription('هل أنت متأكد من حذف البطاقات العائلية المحددة نهائيًا؟ لا يمكن التراجع عن هذا الإجراء.')
                        ->modalSubmitActionLabel('نعم، احذف نهائيًا')
                        ->visible(
                            fn (): bool => auth()->user()?->can('family_cards.force_delete') ?? false
                        ),
                ])
                    ->label('إجراءات جماعية'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
