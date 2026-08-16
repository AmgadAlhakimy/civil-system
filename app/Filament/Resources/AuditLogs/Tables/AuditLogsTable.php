<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Models\Activity;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('المستخدم')
                    ->default('النظام')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user_role')
                    ->label('دور المستخدم')
                    ->default('غير محدد')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event')
                    ->label('العملية')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'created' => 'إضافة',
                            'updated' => 'تعديل',
                            'deleted' => 'حذف',
                            'restored' => 'استعادة',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            'restored' => 'info',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('subject_type')
                    ->label('نوع السجل')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'App\\Models\\Citizen' => 'مواطن',
                            'App\\Models\\Passport' => 'جواز سفر',
                            'App\\Models\\FamilyCard' => 'بطاقة عائلية',
                            'App\\Models\\FamilyMember' => 'فرد أسرة',
                            'App\\Models\\IdentityCard' => 'بطاقة شخصية',
                            'App\\Models\\BirthCertificate' => 'شهادة ميلاد',
                            'App\\Models\\DeathCertificate' => 'شهادة وفاة',
                            default => $state
                                ? class_basename($state)
                                : 'غير محدد',
                        }
                    )
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('السجل المتأثر')
                    ->formatStateUsing(
                        function ($state, Activity $record): string {
                            if (!$state) {
                                return 'السجل غير موجود';
                            }

                            return match ($record->subject_type) {
                                'App\\Models\\Citizen' => trim(
                                    $state->first_name . ' ' .
                                    $state->middle_name . ' ' .
                                    $state->last_name
                                ),

                                'App\\Models\\Passport' => $state->passport_number
                                    ?? $state->number
                                    ?? 'جواز سفر',

                                'App\\Models\\FamilyCard' => $state->card_number
                                    ?? 'بطاقة عائلية',

                                'App\\Models\\IdentityCard' => $state->id_number
                                    ?? $state->card_number
                                    ?? $state->identity_number
                                    ?? 'بطاقة شخصية',

                                default => class_basename(
                                    $record->subject_type
                                ),
                            };
                        }
                    )
                    ->searchable(),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->formatStateUsing(
                        fn (?string $state, Activity $record): string => match ($record->event) {
                            'created' => 'تمت إضافة السجل',
                            'updated' => 'تم تعديل السجل',
                            'deleted' => 'تم حذف السجل',
                            'restored' => 'تمت استعادة السجل',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->searchable()
                    ->limit(50),

                TextColumn::make('ip_address')
                    ->label('عنوان IP')
                    ->default('غير متوفر')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('تاريخ العملية')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('عرض التفاصيل')
                    ->icon('heroicon-o-eye'),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
