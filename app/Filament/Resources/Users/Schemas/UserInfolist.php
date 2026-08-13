<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // البيانات الأساسية
                Section::make('البيانات الأساسية')
                    ->description('المعلومات الأساسية للمستخدم')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('name')
                                    ->label('اسم المستخدم'),

                                TextEntry::make('full_name')
                                    ->label('الاسم الكامل'),

                                TextEntry::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->copyable()
                                    ->copyMessage('تم نسخ البريد الإلكتروني'),

                                TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->copyable()
                                    ->copyMessage('تم نسخ رقم الهاتف')
                                    ->placeholder('غير محدد'),

                                ImageEntry::make('profile_photo')
                                    ->label('الصورة الشخصية')
                                    ->circular()
                                    ->placeholder('لا توجد صورة'),

                            ]),
                    ])
                    ->columnSpanFull(),

                // الفرع والحالة
                Section::make('الفرع والحالة')
                    ->description('معلومات الفرع وحالة حساب المستخدم')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('branch.name')
                                    ->label('الفرع')
                                    ->badge()
                                    ->placeholder('غير محدد'),

                                TextEntry::make('status')
                                    ->label('الحالة')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (string $state): string => match ($state) {
                                            'active' => 'نشط',
                                            'inactive' => 'غير نشط',
                                            'blocked' => 'محظور',
                                            default => $state,
                                        }
                                    ),

                            ]),
                    ])
                    ->columnSpanFull(),

                // الدور والصلاحيات
                Section::make('الدور والصلاحيات')
                    ->description('الدور والصلاحيات الممنوحة للمستخدم')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('roles.name')
                                    ->label('الدور')
                                    ->badge()
                                    ->placeholder('بدون دور'),

                            ]),
                    ])
                    ->columnSpanFull(),

                // معلومات تسجيل الدخول
                Section::make('معلومات تسجيل الدخول')
                    ->description('آخر معلومات متعلقة بتسجيل دخول المستخدم')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('last_login_at')
                                    ->label('آخر دخول')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('لم يسجل دخول بعد'),

                                TextEntry::make('last_login_ip')
                                    ->label('عنوان IP لآخر دخول')
                                    ->copyable()
                                    ->copyMessage('تم نسخ عنوان IP')
                                    ->placeholder('غير متوفر'),

                            ]),
                    ])
                    ->columnSpanFull(),

                // معلومات النظام
                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث وحذف حساب المستخدم')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء الحساب')
                                    ->dateTime('Y-m-d H:i'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime('Y-m-d H:i'),

                                TextEntry::make('deleted_at')
                                    ->label('تاريخ الحذف')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('غير محذوف'),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
