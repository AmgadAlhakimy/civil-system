<?php

namespace App\Filament\Resources\DeathCertificates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeathCertificateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات شهادة الوفاة')
                    ->description('المعلومات الأساسية لشهادة الوفاة')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('certificate_number')
                                    ->label('رقم شهادة الوفاة')
                                    ->copyable()
                                    ->copyMessage('تم نسخ رقم شهادة الوفاة'),

                                TextEntry::make('status')
                                    ->label('حالة الشهادة')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'pending' => 'قيد الانتظار',
                                            'active' => 'سارية',
                                            'cancelled' => 'ملغاة',
                                            default => 'غير محدد',
                                        }
                                    ),

                                TextEntry::make('death_date')
                                    ->label('تاريخ الوفاة')
                                    ->date('Y-m-d'),

                                TextEntry::make('issue_date')
                                    ->label('تاريخ الإصدار')
                                    ->date('Y-m-d')
                                    ->placeholder('لم يتم الإصدار بعد'),

                                TextEntry::make('place_of_death')
                                    ->label('مكان الوفاة')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('cause_of_death')
                                    ->label('سبب الوفاة')
                                    ->placeholder('غير محدد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات المتوفى')
                    ->description('بيانات المواطن صاحب شهادة الوفاة')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('deceased')
                                    ->label('اسم المتوفى')
                                    ->formatStateUsing(
                                        fn ($record): string => collect([
                                            $record->deceased?->first_name,
                                            $record->deceased?->father_name,
                                            $record->deceased?->middle_name,
                                            $record->deceased?->last_name,
                                        ])
                                            ->filter()
                                            ->join(' ') ?: 'غير محدد'
                                    ),

                                TextEntry::make('deceased.national_id')
                                    ->label('الرقم الوطني')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('deceased.birth_date')
                                    ->label('تاريخ الميلاد')
                                    ->date('Y-m-d')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('deceased.birth_place')
                                    ->label('مكان الميلاد')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('deceased.gender')
                                    ->label('الجنس')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'male' => 'ذكر',
                                            'female' => 'أنثى',
                                            default => 'غير محدد',
                                        }
                                    ),

                                TextEntry::make('deceased.address')
                                    ->label('العنوان')
                                    ->placeholder('غير محدد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات الإصدار والاعتماد')
                    ->description('المعلومات النظامية المتعلقة بإصدار واعتماد الشهادة')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('issuedBy.name')
                                    ->label('تم إنشاء الطلب بواسطة')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('approvedBy.name')
                                    ->label('تم الاعتماد بواسطة')
                                    ->placeholder('لم يتم الاعتماد بعد'),

                                TextEntry::make('approved_at')
                                    ->label('تاريخ ووقت الاعتماد')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('لم يتم الاعتماد بعد'),

                                TextEntry::make('print_count')
                                    ->label('عدد مرات الطباعة')
                                    ->numeric(),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('الملاحظات والبيانات الإضافية')
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

                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث سجل الشهادة')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('created_at')
                                    ->label('تاريخ التسجيل')
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
