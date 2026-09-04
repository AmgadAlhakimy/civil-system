<?php

namespace App\Filament\Resources\BirthCertificates\Tables;

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

class BirthCertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('certificate_number')
                    ->label('رقم شهادة الميلاد')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم شهادة الميلاد')
                    ->weight('bold')
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('child')
                    ->label('الطفل')
                    ->getStateUsing(
                        fn ($record): string => collect([
                            $record->child?->first_name,
                            $record->child?->father_name,
                            $record->child?->middle_name,
                            $record->child?->last_name,
                        ])
                            ->filter()
                            ->join(' ')
                    )
                    ->searchable(
                        query: function ($query, string $search): void {
                            $query->whereHas('child', function ($query) use ($search) {
                                $query
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('father_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('national_id', 'like', "%{$search}%");
                            });
                        }
                    )
                    ->sortable(
                        query: function ($query, string $direction): void {
                            $query->orderBy(
                                $query->getModel()->getTable() . '.child_id',
                                $direction
                            );
                        }
                    ),

                TextColumn::make('father')
                    ->label('الأب')
                    ->getStateUsing(
                        fn ($record): string => collect([
                            $record->father?->first_name,
                            $record->father?->father_name,
                            $record->father?->middle_name,
                            $record->father?->last_name,
                        ])
                            ->filter()
                            ->join(' ')
                    )
                    ->searchable(
                        query: function ($query, string $search): void {
                            $query->whereHas('father', function ($query) use ($search) {
                                $query
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('father_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('national_id', 'like', "%{$search}%");
                            });
                        }
                    )
                    ->sortable(
                        query: function ($query, string $direction): void {
                            $query->orderBy(
                                $query->getModel()->getTable() . '.father_id',
                                $direction
                            );
                        }
                    ),

                TextColumn::make('mother')
                    ->label('الأم')
                    ->getStateUsing(
                        fn ($record): string => collect([
                            $record->mother?->first_name,
                            $record->mother?->father_name,
                            $record->mother?->middle_name,
                            $record->mother?->last_name,
                        ])
                            ->filter()
                            ->join(' ')
                    )
                    ->searchable(
                        query: function ($query, string $search): void {
                            $query->whereHas('mother', function ($query) use ($search) {
                                $query
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('father_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('national_id', 'like', "%{$search}%");
                            });
                        }
                    )
                    ->sortable(
                        query: function ($query, string $direction): void {
                            $query->orderBy(
                                $query->getModel()->getTable() . '.mother_id',
                                $direction
                            );
                        }
                    ),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'قيد الانتظار',
                            'active' => 'سارية',
                            'cancelled' => 'ملغاة',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('issue_date')
                    ->label('تاريخ الإصدار')
                    ->date('Y-m-d')
                    ->placeholder('لم يتم الإصدار')
                    ->sortable(),

                TextColumn::make('issuedBy.name')
                    ->label('تم إنشاء الطلب بواسطة')
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
                    ->label('حالة الشهادة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'active' => 'سارية',
                        'cancelled' => 'ملغاة',
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
                        ->modalHeading('حذف شهادات الميلاد المحددة')
                        ->modalDescription(
                            'هل أنت متأكد من حذف شهادات الميلاد المحددة؟ يمكن استعادتها من سلة المحذوفات.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف'),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة الشهادات')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة شهادات الميلاد المحددة؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'تحذير: سيتم حذف شهادات الميلاد نهائيًا ولا يمكن استعادتها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي'),

                ])
                    ->label('إجراءات جماعية'),

            ])

            ->defaultSort('created_at', 'desc');
    }
}
