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
                    ->formatStateUsing(
                        fn (?string $state): string => filled($state)
                            ? $state
                            : 'غير محدد'
                    )
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
                            'force_deleted' => 'حذف نهائي',
                            'login' => 'تسجيل الدخول',
                            'logout' => 'تسجيل الخروج',
                            'password_changed' => 'تغيير كلمة المرور',
                            'failed_login' => 'محاولة دخول فاشلة',
                            default => 'عملية أخرى',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {
                            'created', 'login' => 'success',
                            'updated', 'password_changed' => 'warning',
                            'deleted', 'force_deleted', 'failed_login' => 'danger',
                            'restored', 'logout' => 'info',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('device_type')
                    ->label('نوع الجهاز')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'desktop' => 'كمبيوتر مكتبي',
                            'mobile' => 'هاتف محمول',
                            'tablet' => 'جهاز لوحي',
                            default => 'غير معروف',
                        }
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('browser')
                    ->label('المتصفح')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'Chrome' => 'جوجل كروم',
                            'Firefox' => 'موزيلا فايرفوكس',
                            'Edge' => 'مايكروسوفت إيدج',
                            'Opera' => 'أوبرا',
                            'Samsung Internet' => 'سامسونج إنترنت',
                            'Safari' => 'سفاري',
                            default => 'غير معروف',
                        }
                    )
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
