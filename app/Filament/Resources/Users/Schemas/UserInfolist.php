<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('البيانات الأساسية')
                    ->description('المعلومات الأساسية للمستخدم')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                ImageEntry::make('profile_photo')
                                    ->label('الصورة الشخصية')
                                    ->getStateUsing(
                                        fn ($record) => $record->profile_photo
                                            ? route(
                                                'users.profile-photo',
                                                [
                                                    'path' => basename($record->profile_photo),
                                                ]
                                            )
                                            : null
                                    )
                                    ->circular()
                                    ->imageSize(110)
                                    ->extraImgAttributes([
                                        'class' => 'object-cover shadow-md ring-2 ring-white',
                                    ])
                                    ->extraAttributes([
                                        'class' => 'flex items-center justify-start',
                                    ])
                                    ->placeholder('لا توجد صورة')
                                    ->columnSpan(1),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('full_name')
                                            ->label('الاسم الكامل')
                                            ->weight('bold')
                                            ->size('lg')
                                            ->icon('heroicon-o-user'),

                                        TextEntry::make('name')
                                            ->label('اسم المستخدم')
                                            ->weight('bold')
                                            ->icon('heroicon-o-at-symbol'),

                                        TextEntry::make('email')
                                            ->label('البريد الإلكتروني')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable()
                                            ->copyMessage('تم نسخ البريد الإلكتروني'),

                                        TextEntry::make('phone')
                                            ->label('رقم الهاتف')
                                            ->icon('heroicon-o-phone')
                                            ->copyable()
                                            ->copyMessage('تم نسخ رقم الهاتف')
                                            ->placeholder('غير محدد'),
                                    ])
                                    ->columnSpan(3),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات العمل والصلاحيات')
                    ->description('الفرع وحالة الحساب والدور الممنوح للمستخدم')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('branch.name')
                                    ->label('الفرع')
                                    ->icon('heroicon-o-building-office')
                                    ->badge()
                                    ->placeholder('غير محدد'),

                                TextEntry::make('status')
                                    ->label('حالة المستخدم')
                                    ->icon('heroicon-o-check-circle')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'active' => 'نشط',
                                            'inactive' => 'غير نشط',
                                            'blocked' => 'محظور',
                                            default => $state ?? 'غير محدد',
                                        }
                                    ),

                                TextEntry::make('roles.name')
                                    ->label('الدور')
                                    ->icon('heroicon-o-shield-check')
                                    ->badge()
                                    ->placeholder('بدون دور'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات تسجيل الدخول')
                    ->description('آخر معلومات متعلقة بتسجيل دخول المستخدم')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('last_login_at')
                                    ->label('آخر دخول')
                                    ->icon('heroicon-o-clock')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('لم يسجل دخول بعد'),

                                TextEntry::make('last_login_ip')
                                    ->label('عنوان IP لآخر دخول')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable()
                                    ->copyMessage('تم نسخ عنوان IP')
                                    ->placeholder('غير متوفر'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث وحذف حساب المستخدم')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء الحساب')
                                    ->icon('heroicon-o-calendar-days')
                                    ->dateTime('Y-m-d H:i'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->icon('heroicon-o-arrow-path')
                                    ->dateTime('Y-m-d H:i'),

                                TextEntry::make('deleted_at')
                                    ->label('تاريخ الحذف')
                                    ->icon('heroicon-o-trash')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('غير محذوف'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
