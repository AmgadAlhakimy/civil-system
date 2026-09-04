<?php

namespace App\Filament\Resources\FamilyCards\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FamilyCardInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات البطاقة العائلية')
                    ->description('المعلومات الأساسية للبطاقة العائلية')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('card_number')
                                    ->label('رقم البطاقة')
                                    ->weight('bold')
                                    ->copyable()
                                    ->copyMessage('تم نسخ رقم البطاقة'),

                                TextEntry::make('head')
                                    ->label('رب الأسرة')
                                    ->formatStateUsing(
                                        fn ($record): string => collect([
                                            $record->head?->first_name,
                                            $record->head?->father_name,
                                            $record->head?->middle_name,
                                            $record->head?->last_name,
                                        ])
                                            ->filter()
                                            ->join(' ')
                                    )
                                    ->placeholder('غير محدد'),

                                TextEntry::make('status')
                                    ->label('حالة البطاقة')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'pending' => 'قيد الانتظار',
                                            'rejected' => 'مرفوضة',
                                            'active' => 'سارية',
                                            'expired' => 'منتهية',
                                            'cancelled' => 'ملغاة',
                                            'lost' => 'مفقودة',
                                            'damaged' => 'تالفة',
                                            default => 'غير محدد',
                                        }
                                    ),

                                TextEntry::make('issue_date')
                                    ->label('تاريخ الإصدار')
                                    ->date('Y-m-d')
                                    ->placeholder('لم يتم الإصدار بعد'),

                                TextEntry::make('expiry_date')
                                    ->label('تاريخ الانتهاء')
                                    ->date('Y-m-d')
                                    ->placeholder('لم يتم تحديده بعد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('أفراد الأسرة')
                    ->description('الأفراد المرتبطون بالبطاقة العائلية')
                    ->icon('heroicon-o-users')
                    ->schema([

                        RepeatableEntry::make('members')
                            ->label('')
                            ->schema([

                                Grid::make(3)
                                    ->schema([

                                        TextEntry::make('citizen')
                                            ->label('المواطن')
                                            ->formatStateUsing(
                                                fn ($record): string => collect([
                                                    $record->citizen?->first_name,
                                                    $record->citizen?->father_name,
                                                    $record->citizen?->middle_name,
                                                    $record->citizen?->last_name,
                                                ])
                                                    ->filter()
                                                    ->join(' ')
                                            )
                                            ->placeholder('غير محدد'),

                                        TextEntry::make('relationship')
                                            ->label('صلة القرابة')
                                            ->formatStateUsing(
                                                fn (?string $state): string => match ($state) {
                                                    'spouse' => 'زوج / زوجة',
                                                    'child' => 'ابن / ابنة',
                                                    'father' => 'أب',
                                                    'mother' => 'أم',
                                                    'brother' => 'أخ',
                                                    'sister' => 'أخت',
                                                    'other' => 'أخرى',
                                                    default => 'غير محدد',
                                                }
                                            ),

                                        TextEntry::make('is_active')
                                            ->label('الحالة')
                                            ->badge()
                                            ->formatStateUsing(
                                                fn ($state): string =>
                                                $state ? 'فعال' : 'غير فعال'
                                            ),

                                    ]),

                                TextEntry::make('notes')
                                    ->label('ملاحظات')
                                    ->placeholder('لا توجد ملاحظات')
                                    ->columnSpanFull(),

                            ])
                            ->contained(true),

                    ])
                    ->columnSpanFull(),

                Section::make('بيانات الإصدار والاعتماد')
                    ->description('المعلومات النظامية المتعلقة بإصدار واعتماد البطاقة')
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
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('notes')
                                    ->label('ملاحظات')
                                    ->placeholder('لا توجد ملاحظات')
                                    ->columnSpanFull(),

                                TextEntry::make('qr_code')
                                    ->label('رمز QR')
                                    ->placeholder('لا يوجد رمز QR')
                                    ->columnSpanFull(),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث سجل البطاقة')
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
