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
                    ->sortable()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'male' => 'ذكر',
                            'female' => 'أنثى',
                            default => $state ?? 'غير محدد',
                        }
                    ),

                TextColumn::make('blood_type')
                    ->label('فصيلة الدم')
                    ->placeholder('غير مسجلة')
                    ->sortable()
                    ->extraAttributes([
                        'dir' => 'ltr',
                        'class' => 'text-right',
                    ])
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الهاتف'),

                TextColumn::make('marital_status')
                    ->label('الحالة الاجتماعية')
                    ->sortable()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'single' => 'أعزب',
                            'married' => 'متزوج',
                            'divorced' => 'مطلق',
                            'widowed' => 'أرمل',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('verification_status')
                    ->label('حالة اعتماد المواطن')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'بانتظار الاعتماد',
                            'approved' => 'معتمدة',
                            'rejected' => 'مرفوضة',
                            default => 'غير محددة',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        }
                    )
                    ->icon(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'heroicon-o-clock',
                            'approved' => 'heroicon-o-check-circle',
                            'rejected' => 'heroicon-o-x-circle',
                            default => 'heroicon-o-question-mark-circle',
                        }
                    )
                    ->sortable(),

                TextColumn::make('rejection_reason')
                    ->label('سبب الرفض')
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('حالة المواطن')
                    ->sortable()
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
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('occupation')
                    ->label('المهنة')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
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

                SelectFilter::make('blood_type')
                    ->label('فصيلة الدم')
                    ->options([
                        'A+' => 'A+',
                        'A-' => 'A-',
                        'B+' => 'B+',
                        'B-' => 'B-',
                        'AB+' => 'AB+',
                        'AB-' => 'AB-',
                        'O+' => 'O+',
                        'O-' => 'O-',
                    ]),

                SelectFilter::make('marital_status')
                    ->label('الحالة الاجتماعية')
                    ->options([
                        'single' => 'أعزب',
                        'married' => 'متزوج',
                        'divorced' => 'مطلق',
                        'widowed' => 'أرمل',
                    ]),

                SelectFilter::make('verification_status')
                    ->label('حالة اعتماد المواطن')
                    ->options([
                        'pending' => 'بانتظار الاعتماد',
                        'approved' => 'معتمدة',
                        'rejected' => 'مرفوضة',
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
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->visible(
                        fn (): bool => auth()->user()?->can('citizens.view') ?? false
                    ),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(
                        fn (): bool => auth()->user()?->can('citizens.update') ?? false
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->visible(
                            fn (): bool => auth()->user()?->can('citizens.delete') ?? false
                        ),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->visible(
                            fn (): bool => auth()->user()?->can('citizens.restore') ?? false
                        ),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->visible(
                            fn (): bool => auth()->user()?->can('citizens.force_delete') ?? false
                        ),
                ])
                    ->label('إجراءات جماعية'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
