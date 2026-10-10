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

                                Section::make()
                                    ->schema([
                                        ImageEntry::make('photo')
                                            ->label('الصورة الشخصية')
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
                                            ->imageSize(170)
                                            ->defaultImageUrl(url('/images/default-avatar.png'))
                                            ->extraImgAttributes([
                                                'class' => 'object-cover shadow-lg ring-4 ring-white dark:ring-gray-800',
                                            ])
                                            ->alignCenter(),
                                    ])
                                    ->columnSpan(1),

                                Grid::make(2)
                                    ->schema([

                                        TextEntry::make('full_name')
                                            ->label('الاسم الكامل')
                                            ->state(
                                                fn ($record) => trim(
                                                    "{$record->first_name} {$record->father_name} {$record->middle_name} {$record->last_name}"
                                                )
                                            )
                                            ->weight('bold')
                                            ->size('xl')
                                            ->icon('heroicon-o-user')
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
                                            ),

                                        TextEntry::make('birth_date')
                                            ->label('تاريخ الميلاد')
                                            ->date('Y-m-d')
                                            ->icon('heroicon-o-calendar-days'),

                                        TextEntry::make('phone')
                                            ->label('رقم الهاتف')
                                            ->copyable()
                                            ->copyMessage('تم نسخ رقم الهاتف')
                                            ->icon('heroicon-o-device-phone-mobile')
                                            ->placeholder('غير مسجل'),

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
                                    ->description('المعلومات الشخصية الأساسية للمواطن')
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

                                                TextEntry::make('occupation')
                                                    ->label('المهنة')
                                                    ->placeholder('غير مسجل')
                                                    ->icon('heroicon-o-briefcase'),

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
                                                    ),

                                            ]),
                                    ])
                                    ->columnSpanFull(),

                            ]),

                        Tabs\Tab::make('الاتصال والعنوان')
                            ->icon('heroicon-o-phone')
                            ->schema([

                                Section::make('معلومات الاتصال والعنوان')
                                    ->description('بيانات التواصل وعنوان السكن المسجل للمواطن')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
                                        Grid::make(3)
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

                                                TextEntry::make('address')
                                                    ->label('العنوان التفصيلي')
                                                    ->placeholder('العنوان غير مسجل')
                                                    ->icon('heroicon-o-map-pin'),

                                            ]),
                                    ])
                                    ->columnSpanFull(),

                            ]),

                        Tabs\Tab::make('حالة المواطن')
                            ->icon('heroicon-o-shield-check')
                            ->schema([

                                Section::make('حالة السجل')
                                    ->description('حالة حساب المواطن والصورة الشخصية المسجلة')
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([

                                        Grid::make(2)
                                            ->schema([

                                                IconEntry::make('is_active')
                                                    ->label('حالة الحساب')
                                                    ->boolean()
                                                    ->trueIcon('heroicon-o-check-circle')
                                                    ->falseIcon('heroicon-o-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger'),

                                                TextEntry::make('photo_status')
                                                    ->label('الصورة الشخصية')
                                                    ->state(
                                                        fn ($record) => $record->photo
                                                            ? 'مسجلة'
                                                            : 'غير مسجلة'
                                                    )
                                                    ->color('gray')
                                                    ->icon(
                                                        fn ($state): string => $state === 'مسجلة'
                                                            ? 'heroicon-o-check-circle'
                                                            : 'heroicon-o-minus-circle'
                                                    )
                                                    ->iconColor(
                                                        fn ($state): string => $state === 'مسجلة'
                                                            ? 'success'
                                                            : 'gray'
                                                    ),

                                            ]),

                                    ])
                                    ->columnSpanFull(),

                            ]),

                        Tabs\Tab::make('التحقق')
                            ->icon('heroicon-o-shield-check')
                            ->schema([

                                Section::make('اعتماد بيانات المواطن')
                                    ->description('حالة مراجعة البيانات والقرار الإداري')
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([

                                        Grid::make(2)
                                            ->schema([

                                                TextEntry::make('verification_status')
                                                    ->label('حالة الاعتماد')
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
                                                    ),

                                                TextEntry::make('rejection_reason')
                                                    ->label('سبب الرفض')
                                                    ->placeholder('لا يوجد سبب رفض')
                                                    ->visible(
                                                        fn ($record): bool => $record->verification_status === 'rejected'
                                                            && filled($record->rejection_reason)
                                                    ),

                                            ]),

                                        Grid::make(2)
                                            ->schema([

                                                TextEntry::make('verifier.name')
                                                    ->label('الموظف الذي اتخذ القرار')
                                                    ->placeholder('لم يُتخذ قرار بعد')
                                                    ->icon('heroicon-o-user-circle'),

                                                TextEntry::make('verified_at')
                                                    ->label('تاريخ آخر قرار')
                                                    ->dateTime('Y-m-d H:i')
                                                    ->placeholder('لم يُتخذ قرار بعد')
                                                    ->icon('heroicon-o-calendar-days'),

                                            ]),

                                    ])
                                    ->columnSpanFull(),

                            ]),

                        Tabs\Tab::make('معلومات النظام')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([

                                Section::make('سجل النظام')
                                    ->description('المعلومات الإدارية والتقنية الخاصة بسجل المواطن')
                                    ->icon('heroicon-o-server')
                                    ->schema([

                                        Grid::make(2)
                                            ->schema([

                                                TextEntry::make('created_at')
                                                    ->label('تاريخ التسجيل')
                                                    ->dateTime('Y-m-d H:i')
                                                    ->icon('heroicon-o-calendar-days'),

                                                TextEntry::make('updated_at')
                                                    ->label('آخر تحديث')
                                                    ->dateTime('Y-m-d H:i')
                                                    ->icon('heroicon-o-arrow-path'),

                                            ]),

                                    ])
                                    ->columnSpanFull(),

                            ]),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
