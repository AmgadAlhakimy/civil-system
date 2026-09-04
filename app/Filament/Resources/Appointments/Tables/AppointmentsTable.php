<?php

namespace App\Filament\Resources\Appointments\Tables;

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

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('citizen')
                    ->label('المواطن')
                    ->getStateUsing(
                        fn ($record): string => trim(
                            implode(' ', array_filter([
                                $record->citizen?->first_name,
                                $record->citizen?->father_name,
                                $record->citizen?->middle_name,
                                $record->citizen?->last_name,
                            ]))
                        ) ?: 'غير محدد'
                    )
                    ->searchable(
                        query: function ($query, string $search): void {
                            $query->whereHas('citizen', function ($query) use ($search) {
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
                                    'appointments.citizen_id',
                                    '=',
                                    'citizens.id'
                                )
                                ->orderBy('citizens.first_name', $direction)
                                ->orderBy('citizens.father_name', $direction)
                                ->orderBy('citizens.middle_name', $direction)
                                ->orderBy('citizens.last_name', $direction)
                                ->select('appointments.*');
                        }
                    ),

                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable()
                    ->placeholder('غير محدد'),

                TextColumn::make('service_type')
                    ->label('نوع الخدمة')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'passport_new' => 'إصدار جواز سفر',
                            'passport_renew' => 'تجديد جواز سفر',
                            'passport_lost' => 'بدل فاقد لجواز السفر',
                            'passport_damaged' => 'بدل تالف لجواز السفر',

                            'national_id_new' => 'إصدار بطاقة شخصية',
                            'national_id_renew' => 'تجديد بطاقة شخصية',
                            'national_id_lost' => 'بدل فاقد للبطاقة الشخصية',
                            'national_id_damaged' => 'بدل تالف للبطاقة الشخصية',

                            'family_card_new' => 'إصدار بطاقة عائلية',
                            'family_card_renew' => 'تجديد بطاقة عائلية',

                            'birth_certificate' => 'إصدار شهادة ميلاد',
                            'death_certificate' => 'إصدار شهادة وفاة',

                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('appointment_date')
                    ->label('تاريخ الموعد')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('appointment_time')
                    ->label('وقت الموعد')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('حالة الموعد')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'pending' => 'قيد الانتظار',
                            'confirmed' => 'مؤكد',
                            'attended' => 'تم الحضور',
                            'cancelled' => 'ملغى',
                            'no_show' => 'لم يحضر',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('تم الحجز بواسطة')
                    ->sortable()
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('confirmedBy.name')
                    ->label('تم التأكيد بواسطة')
                    ->sortable()
                    ->placeholder('لم يتم التأكيد')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('confirmed_at')
                    ->label('تاريخ التأكيد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('لم يتم التأكيد')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(40)
                    ->placeholder('لا توجد ملاحظات')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
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
                    ->label('حالة الموعد')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مؤكد',
                        'attended' => 'تم الحضور',
                        'cancelled' => 'ملغى',
                        'no_show' => 'لم يحضر',
                    ]),

                SelectFilter::make('service_type')
                    ->label('نوع الخدمة')
                    ->options([
                        'passport_new' => 'إصدار جواز سفر',
                        'passport_renew' => 'تجديد جواز سفر',
                        'passport_lost' => 'بدل فاقد لجواز السفر',
                        'passport_damaged' => 'بدل تالف لجواز السفر',

                        'national_id_new' => 'إصدار بطاقة شخصية',
                        'national_id_renew' => 'تجديد بطاقة شخصية',
                        'national_id_lost' => 'بدل فاقد للبطاقة الشخصية',
                        'national_id_damaged' => 'بدل تالف للبطاقة الشخصية',

                        'family_card_new' => 'إصدار بطاقة عائلية',
                        'family_card_renew' => 'تجديد بطاقة عائلية',

                        'birth_certificate' => 'إصدار شهادة ميلاد',
                        'death_certificate' => 'إصدار شهادة وفاة',
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
                        ->modalHeading('حذف المواعيد المحددة')
                        ->modalDescription(
                            'هل أنت متأكد من حذف المواعيد المحددة؟ يمكن استعادتها من سلة المحذوفات.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف'),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة المواعيد')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة المواعيد المحددة؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'تحذير: سيتم حذف المواعيد نهائيًا ولا يمكن استعادتها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي'),
                ])
                    ->label('إجراءات جماعية'),
            ])
            ->defaultSort('appointment_date', 'desc');
    }
}
