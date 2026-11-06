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
                TextColumn::make('causer_name')
                    ->label('المستخدم')
                    ->formatStateUsing(function (?string $state, Activity $record): string {
                        if ($state) {
                            return $record->causer_id && ! $record->causer
                                ? $state . ' (محذوف)'
                                : $state;
                        }

                        return $record->causer_id
                            ? ($record->causer?->name ?? 'غير معروف')
                            : 'النظام';
                    })
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user_role')
                    ->label('دور المستخدم')
                    ->default('غير محدد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event')
                    ->label('العملية')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'created' => 'إضافة',
                            'updated' => 'تعديل',
                            'deleted' => 'حذف',
                            'restored' => 'استعادة',
                            'login' => 'تسجيل الدخول',
                            'logout' => 'تسجيل الخروج',
                            'password_changed' => 'تغيير كلمة المرور',
                            'failed_login' => 'محاولة تسجيل دخول فاشلة',
                            default => $state ?? 'غير محدد',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'created', 'login' => 'success',
                            'updated', 'password_changed' => 'warning',
                            'deleted', 'failed_login' => 'danger',
                            'restored', 'logout' => 'info',
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
                            'App\\Models\\Appointment' => 'موعد',
                            'App\\Models\\Branch' => 'فرع',
                            'App\\Models\\District' => 'مديرية',
                            'App\\Models\\Governorate' => 'محافظة',
                            'App\\Models\\User' => 'مستخدم',
                            'App\\Models\\Report' => 'تقرير',
                            'App\\Models\\ServiceFee' => 'رسوم خدمة',
                            default => $state
                                ? class_basename($state)
                                : 'غير محدد',
                        }
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('السجل المتأثر')
                    ->formatStateUsing(
                        function ($state, Activity $record): string {
                            $properties = $record->properties ?? collect();

                            if ($state) {
                                return match ($record->subject_type) {
                                    'App\\Models\\Citizen' => trim(
                                        ($state->first_name ?? '') . ' ' .
                                        ($state->middle_name ?? '') . ' ' .
                                        ($state->last_name ?? '')
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

                                    'App\\Models\\User' => $state->full_name
                                        ?? $state->name
                                        ?? 'مستخدم',

                                    default => class_basename(
                                        $record->subject_type ?? ''
                                    ) ?: 'غير محدد',
                                };
                            }

                            $attributes = $properties->get('attributes', []);

                            if ($attributes instanceof \Illuminate\Support\Collection) {
                                $attributes = $attributes->all();
                            }

                            if (! is_array($attributes)) {
                                $attributes = [];
                            }

                            $name = trim(implode(' ', array_filter([
                                $attributes['first_name'] ?? null,
                                $attributes['middle_name'] ?? null,
                                $attributes['last_name'] ?? null,
                            ])));

                            if ($name !== '') {
                                return $name;
                            }

                            foreach ([
                                         'passport_number',
                                         'card_number',
                                         'id_number',
                                         'identity_number',
                                         'number',
                                         'full_name',
                                         'name',
                                     ] as $key) {
                                if (! empty($attributes[$key])) {
                                    return (string) $attributes[$key];
                                }
                            }

                            return $record->subject_type
                                ? class_basename($record->subject_type)
                                : 'غير محدد';
                        }
                    )
                    ->searchable(),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->formatStateUsing(
                        fn (?string $state): string => $state ?? 'غير محدد'
                    )
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (?string $state): ?string => $state),

                TextColumn::make('device_type')
                    ->label('نوع الجهاز')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'desktop' => 'حاسوب',
                            'mobile' => 'هاتف',
                            'tablet' => 'جهاز لوحي',
                            default => 'غير معروف',
                        }
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('browser')
                    ->label('المتصفح')
                    ->default('غير معروف')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->visible(
                        fn (Activity $record): bool =>
                            auth()->user()?->can('audit_logs.view') ?? false
                    ),
            ])
            ->recordActionsColumnLabel('العمليات')
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
