<?php

namespace App\Filament\Resources\DeathCertificates\Tables;

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

class DeathCertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('certificate_number')
                    ->label('رقم شهادة الوفاة')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم شهادة الوفاة')
                    ->weight('bold')
                    ->icon('heroicon-o-document-text'),

                TextColumn::make('deceased')
                    ->label('المتوفى')
                    ->getStateUsing(
                        fn ($record): string => collect([
                            $record->deceased?->first_name,
                            $record->deceased?->father_name,
                            $record->deceased?->middle_name,
                            $record->deceased?->last_name,
                        ])
                            ->filter()
                            ->join(' ')
                    )
                    ->searchable(
                        query: function ($query, string $search): void {
                            $query->whereHas('deceased', function ($query) use ($search) {
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
                            $query
                                ->join(
                                    'citizens',
                                    'death_certificates.deceased_id',
                                    '=',
                                    'citizens.id'
                                )
                                ->orderBy('citizens.first_name', $direction)
                                ->orderBy('citizens.father_name', $direction)
                                ->orderBy('citizens.middle_name', $direction)
                                ->orderBy('citizens.last_name', $direction)
                                ->select('death_certificates.*');
                        }
                    ),

                TextColumn::make('deceased.national_id')
                    ->label('الرقم الوطني')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('death_date')
                    ->label('تاريخ الوفاة')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('place_of_death')
                    ->label('مكان الوفاة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cause_of_death')
                    ->label('سبب الوفاة')
                    ->searchable(),

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
                    ->sortable()
                    ->placeholder('لم يتم الإصدار'),

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
                        ->modalHeading('حذف شهادات الوفاة المحددة')
                        ->modalDescription(
                            'هل أنت متأكد من حذف شهادات الوفاة المحددة؟ يمكن استعادتها من سلة المحذوفات.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف'),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة الشهادات')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة شهادات الوفاة المحددة؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'تحذير: سيتم حذف شهادات الوفاة نهائيًا ولا يمكن استعادتها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي'),

                ])
                    ->label('إجراءات جماعية'),

            ])

            ->defaultSort('created_at', 'desc');
    }
}
