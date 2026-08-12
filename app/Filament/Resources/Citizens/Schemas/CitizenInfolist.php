<?php

namespace App\Filament\Resources\Citizens\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class CitizenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 4,
                        ])
                            ->schema([
                                ImageEntry::make('photo')
                                    ->label('الصورة الشخصية')
                                    ->circular()
                                    ->size(150)
                                    ->defaultImageUrl(url('/images/default-avatar.png'))
                                    ->columnSpan(1),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('full_name')
                                            ->label('الاسم الكامل')
                                            ->state(fn ($record) => trim(
                                                "{$record->first_name} {$record->father_name} {$record->middle_name} {$record->last_name}"
                                            ))
                                            ->weight('bold')
                                            ->size('xl')
                                            ->columnSpanFull(),

                                        TextEntry::make('national_id')
                                            ->label('الرقم الوطني')
                                            ->copyable()
                                            ->copyMessage('تم نسخ الرقم الوطني')
                                            ->icon('heroicon-o-identification')
                                            ->weight('bold'),

                                        TextEntry::make('gender')
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

                                        TextEntry::make('is_active')
                                            ->label('الحالة')
                                            ->formatStateUsing(
                                                fn ($state): string => $state ? 'نشط' : 'غير نشط'
                                            )
                                            ->badge()
                                            ->color(
                                                fn ($state): string => $state ? 'success' : 'danger'
                                            )
                                            ->icon(
                                                fn ($state): string => $state
                                                    ? 'heroicon-o-check-circle'
                                                    : 'heroicon-o-x-circle'
                                            ),
                                    ])
                                    ->columnSpan(3),
                            ]),
                    ])
                    ->columnSpanFull(),

                Tabs::make('بيانات المواطن')
                    ->tabs([
                        Tabs\Tab::make('البيانات الشخصية')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('البيانات الأساسية')
                                    ->description('المعلومات الشخصية للمواطن')
                                    ->icon('heroicon-o-identification')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('first_name')
                                                    ->label('الاسم الأول')
                                                    ->placeholder('غير مسجل'),

                                                TextEntry::make('father_name')
                                                    ->label('اسم الأب')
                                                    ->placeholder('غير مسجل'),

                                                TextEntry::make('middle_name')
                                                    ->label('اسم الجد')
                                                    ->placeholder('غير مسجل'),

                                                TextEntry::make('last_name')
                                                    ->label('اسم العائلة')
                                                    ->placeholder('غير مسجل'),

                                                TextEntry::make('mother_name')
                                                    ->label('اسم الأم')
                                                    ->placeholder('غير مسجل'),

                                                TextEntry::make('birth_date')
                                                    ->label('تاريخ الميلاد')
                                                    ->date('Y-m-d')
                                                    ->icon('heroicon-o-calendar-days'),

                                                TextEntry::make('birth_place')
                                                    ->label('مكان الميلاد')
                                                    ->placeholder('غير مسجل')
                                                    ->icon('heroicon-o-map-pin'),

                                                TextEntry::make('marital_status')
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
                                                    ->badge(),

                                                TextEntry::make('occupation')
                                                    ->label('المهنة')
                                                    ->placeholder('غير مسجل')
                                                    ->icon('heroicon-o-briefcase'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('الاتصال والعنوان')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('معلومات الاتصال')
                                    ->description('بيانات التواصل المسجلة للمواطن')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('phone')
                                                    ->label('رقم الهاتف')
                                                    ->copyable()
                                                    ->copyMessage('تم نسخ رقم الهاتف')
                                                    ->icon('heroicon-o-device-phone-mobile')
                                                    ->placeholder('غير مسجل'),

                                                TextEntry::make('email')
                                                    ->label('البريد الإلكتروني')
                                                    ->copyable()
                                                    ->copyMessage('تم نسخ البريد الإلكتروني')
                                                    ->icon('heroicon-o-envelope')
                                                    ->placeholder('غير مسجل'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('العنوان')
                                    ->description('عنوان السكن المسجل للمواطن')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
                                        TextEntry::make('address')
                                            ->label('العنوان التفصيلي')
                                            ->placeholder('العنوان غير مسجل')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('البيانات الحيوية')
                            ->icon('heroicon-o-finger-print')
                            ->schema([
                                Section::make('البيانات الحيوية')
                                    ->description('البيانات الحيوية المرتبطة بسجل المواطن')
                                    ->icon('heroicon-o-finger-print')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                ImageEntry::make('face_data')
                                                    ->label('بصمة الوجه')
                                                    ->defaultImageUrl(url('/images/default-avatar.png'))
                                                    ->height(250),

                                                Section::make('حالة البيانات الحيوية')
                                                    ->schema([
                                                        IconEntry::make('is_active')
                                                            ->label('حالة الحساب')
                                                            ->boolean()
                                                            ->trueIcon('heroicon-o-check-circle')
                                                            ->falseIcon('heroicon-o-x-circle')
                                                            ->trueColor('success')
                                                            ->falseColor('danger'),

                                                        TextEntry::make('photo')
                                                            ->label('الصورة الشخصية')
                                                            ->state(fn ($record) => $record->photo ? 'مسجلة' : 'غير مسجلة')
                                                            ->badge()
                                                            ->color(
                                                                fn ($state): string => $state === 'مسجلة'
                                                                    ? 'success'
                                                                    : 'gray'
                                                            ),

                                                        TextEntry::make('face_data')
                                                            ->label('بيانات الوجه')
                                                            ->state(fn ($record) => $record->face_data ? 'مسجلة' : 'غير مسجلة')
                                                            ->badge()
                                                            ->color(
                                                                fn ($state): string => $state === 'مسجلة'
                                                                    ? 'success'
                                                                    : 'gray'
                                                            ),
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('التحقق')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('بيانات التحقق')
                                    ->description('معلومات التحقق من بيانات المواطن')
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('verified_by')
                                                    ->label('حالة التحقق')
                                                    ->state(
                                                        fn ($record): string => $record->verified_by
                                                            ? 'تم التحقق'
                                                            : 'لم يتم التحقق'
                                                    )
                                                    ->badge()
                                                    ->color(
                                                        fn ($record): string => $record->verified_by
                                                            ? 'success'
                                                            : 'warning'
                                                    )
                                                    ->icon(
                                                        fn ($record): string => $record->verified_by
                                                            ? 'heroicon-o-check-circle'
                                                            : 'heroicon-o-exclamation-circle'
                                                    ),

                                                TextEntry::make('verifier.name')
                                                    ->label('تم التحقق بواسطة')
                                                    ->placeholder('لم يتم التحقق')
                                                    ->icon('heroicon-o-user-circle'),

                                                TextEntry::make('verified_at')
                                                    ->label('تاريخ التحقق')
                                                    ->dateTime('Y-m-d H:i')
                                                    ->placeholder('لم يتم التحقق')
                                                    ->icon('heroicon-o-calendar-days'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('معلومات النظام')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('سجل النظام')
                                    ->description('المعلومات الإدارية والتقنية للسجل')
                                    ->icon('heroicon-o-server')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('created_at')
                                                    ->label('تاريخ التسجيل')
                                                    ->dateTime('Y-m-d H:i')
                                                    ->icon('heroicon-o-calendar-days'),

                                                TextEntry::make('updated_at')
                                                    ->label('آخر تحديث')
                                                    ->dateTime('Y-m-d H:i')
                                                    ->icon('heroicon-o-arrow-path'),

                                                TextEntry::make('id')
                                                    ->label('معرف السجل')
                                                    ->copyable()
                                                    ->copyMessage('تم نسخ معرف السجل')
                                                    ->icon('heroicon-o-hashtag'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
