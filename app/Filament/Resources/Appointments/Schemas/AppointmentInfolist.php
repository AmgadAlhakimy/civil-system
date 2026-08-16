<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppointmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | بيانات الموعد
                |--------------------------------------------------------------------------
                */
                Section::make('بيانات الموعد')
                    ->description('المعلومات الأساسية للموعد')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('service_type')
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

                                            default => 'غير محدد',
                                        }
                                    ),

                                TextEntry::make('status')
                                    ->label('حالة الموعد')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'pending' => 'قيد الانتظار',
                                            'confirmed' => 'مؤكد',
                                            'attended' => 'تم الحضور',
                                            'cancelled' => 'ملغي',
                                            'no_show' => 'لم يحضر',
                                            default => 'غير محدد',
                                        }
                                    ),

                                TextEntry::make('appointment_date')
                                    ->label('تاريخ الموعد')
                                    ->date('Y-m-d'),

                                TextEntry::make('appointment_time')
                                    ->label('وقت الموعد')
                                    ->time('H:i'),

                            ]),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | بيانات المواطن
                |--------------------------------------------------------------------------
                */
                Section::make('بيانات المواطن')
                    ->description('بيانات المواطن صاحب الموعد')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('citizen.full_name')
                                    ->label('اسم المواطن')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('citizen.national_id')
                                    ->label('الرقم الوطني')
                                    ->placeholder('غير محدد')
                                    ->copyable()
                                    ->copyMessage('تم نسخ الرقم الوطني'),

                                TextEntry::make('citizen.birth_date')
                                    ->label('تاريخ الميلاد')
                                    ->date('Y-m-d')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('citizen.gender')
                                    ->label('الجنس')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'male' => 'ذكر',
                                            'female' => 'أنثى',
                                            default => 'غير محدد',
                                        }
                                    )
                                    ->placeholder('غير محدد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | بيانات الفرع
                |--------------------------------------------------------------------------
                */
                Section::make('بيانات الفرع')
                    ->description('الفرع المحدد لتنفيذ الخدمة')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('branch.name')
                                    ->label('الفرع')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('user.name')
                                    ->label('الموظف المسؤول')
                                    ->placeholder('لم يتم تحديد موظف'),

                            ]),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | معلومات التأكيد
                |--------------------------------------------------------------------------
                */
                Section::make('بيانات تأكيد الموعد')
                    ->description('المعلومات المتعلقة بتأكيد الموعد')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('confirmedBy.name')
                                    ->label('تم التأكيد بواسطة')
                                    ->placeholder('لم يتم تأكيد الموعد بعد'),

                                TextEntry::make('confirmed_at')
                                    ->label('تاريخ ووقت التأكيد')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('لم يتم التأكيد بعد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | معلومات إضافية
                |--------------------------------------------------------------------------
                */
                Section::make('معلومات إضافية')
                    ->description('الملاحظات والبيانات الإضافية المرتبطة بالموعد')
                    ->icon('heroicon-o-information-circle')
                    ->schema([

                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('لا توجد ملاحظات')
                            ->columnSpanFull(),

                        TextEntry::make('qr_code')
                            ->label('رمز QR')
                            ->placeholder('لا يوجد رمز QR')
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | معلومات النظام
                |--------------------------------------------------------------------------
                */
                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث سجل الموعد')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء الموعد')
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
