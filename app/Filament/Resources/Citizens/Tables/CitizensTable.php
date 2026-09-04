<?php

namespace App\Filament\Resources\Citizens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CitizensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('national_id')
                    ->label('الرقم الوطني')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرقم الوطني')
                    ->weight('bold')
                    ->icon('heroicon-o-identification'),

                ImageColumn::make('photo')
                    ->label('الصورة')
                    ->getStateUsing(
                        fn ($record) => $record->photo
                            ? route(
                                'citizens.photo',
                                [
                                    'path' => basename($record->photo),
                                ]
                            )
                            : null
                    )
                    ->circular()
                    ->size(45)
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('full_name')
                    ->label('الاسم الكامل')
                    ->getStateUsing(
                        fn ($record): string => trim(
                            implode(' ', array_filter([
                                $record->first_name,
                                $record->father_name,
                                $record->middle_name,
                                $record->last_name,
                            ]))
                        )
                    )
                    ->searchable([
                        'first_name',
                        'father_name',
                        'middle_name',
                        'last_name',
                    ])
                    ->sortable(
                        query: function ($query, string $direction): void {
                            $query
                                ->orderBy('first_name', $direction)
                                ->orderBy('father_name', $direction)
                                ->orderBy('middle_name', $direction)
                                ->orderBy('last_name', $direction);
                        }
                    ),

                TextColumn::make('gender')
                    ->label('الجنس')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'male' => 'ذكر',
                            'female' => 'أنثى',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'male' => 'info',
                            'female' => 'danger',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الهاتف')
                    ->icon('heroicon-o-device-phone-mobile'),

                TextColumn::make('marital_status')
                    ->label('الحالة الاجتماعية')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'single' => 'أعزب',
                            'married' => 'متزوج',
                            'divorced' => 'مطلق',
                            'widowed' => 'أرمل',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('birth_date')
                    ->label('تاريخ الميلاد')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('birth_place')
                    ->label('مكان الميلاد')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('occupation')
                    ->label('المهنة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('verifier.name')
                    ->label('تم التحقق بواسطة')
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
                SelectFilter::make('gender')
                    ->label('الجنس')
                    ->options([
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                    ]),

                SelectFilter::make('marital_status')
                    ->label('الحالة الاجتماعية')
                    ->options([
                        'single' => 'أعزب',
                        'married' => 'متزوج',
                        'divorced' => 'مطلق',
                        'widowed' => 'أرمل',
                    ]),

                SelectFilter::make('is_active')
                    ->label('حالة المواطن')
                    ->options([
                        1 => 'نشط',
                        0 => 'غير نشط',
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
                        ->label('حذف المحدد'),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي'),
                ])
                    ->label('إجراءات جماعية'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
