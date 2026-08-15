<?php

namespace App\Filament\Resources\BirthCertificates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BirthCertificateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات شهادة الميلاد')
                    ->description('المعلومات الأساسية لشهادة الميلاد')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('certificate_number')
                                    ->label('رقم شهادة الميلاد')
                                    ->copyable()
                                    ->copyMessage('تم نسخ رقم شهادة الميلاد'),

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

                                TextEntry::make('issue_date')
                                    ->label('تاريخ الإصدار')
                                    ->date('Y-m-d'),

                                TextEntry::make('print_count')
                                    ->label('عدد مرات الطباعة')
                                    ->numeric(),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات الطفل')
                    ->description('بيانات الطفل صاحب شهادة الميلاد')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('child.full_name')
                                    ->label('اسم الطفل'),

                                TextEntry::make('child.national_id')
                                    ->label('الرقم الوطني')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('child.birth_date')
                                    ->label('تاريخ الميلاد')
                                    ->date('Y-m-d'),

                                TextEntry::make('child.birth_place')
                                    ->label('مكان الميلاد')
                                    ->placeholder('غير محدد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات الوالدين')
                    ->description('بيانات والد ووالدة الطفل')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('father.full_name')
                                    ->label('الأب'),

                                TextEntry::make('father.national_id')
                                    ->label('الرقم الوطني للأب')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('mother.full_name')
                                    ->label('الأم'),

                                TextEntry::make('mother.national_id')
                                    ->label('الرقم الوطني للأم')
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
                                    ->label('تم الإصدار بواسطة')
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
