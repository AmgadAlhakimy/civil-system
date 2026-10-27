<?php

namespace App\Filament\Resources\ServiceFees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceFeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('البيانات الأساسية للخدمة')
                    ->description('المعلومات الأساسية للخدمة والرسوم المالية المعتمدة')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('service_name')
                                    ->label('اسم الخدمة')
                                    ->weight('bold')
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('category')
                                    ->label('تصنيف الخدمة')
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'identity_card' => 'بطاقة شخصية',
                                            'passport' => 'جواز سفر',
                                            'family_card' => 'بطاقة عائلية',
                                            'birth_certificate' => 'شهادة ميلاد',
                                            'death_certificate' => 'شهادة وفاة',
                                            'other' => 'خدمات أخرى',
                                            default => $state ?? 'غير محدد',
                                        }
                                    )
                                    ->placeholder('غير محدد'),

                                TextEntry::make('amount')
                                    ->label('قيمة الرسوم')
                                    ->weight('bold')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success')
                                    ->formatStateUsing(
                                        fn ($state): string => number_format(
                                                (float) $state,
                                                2
                                            ) . ' ريال يمني'
                                    ),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('حالة الخدمة والملاحظات')
                    ->description('حالة تفعيل الرسوم والملاحظات الإضافية الخاصة بالخدمة')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('حالة الرسوم')
                                    ->icon(
                                        fn ($state): string => $state
                                            ? 'heroicon-o-check-circle'
                                            : 'heroicon-o-x-circle'
                                    )
                                    ->iconColor(
                                        fn ($state): string => $state
                                            ? 'success'
                                            : 'danger'
                                    )
                                    ->formatStateUsing(
                                        fn ($state): string => $state
                                            ? 'مفعّلة'
                                            : 'غير مفعّلة'
                                    ),

                                TextEntry::make('notes')
                                    ->label('الملاحظات')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->placeholder('لا توجد ملاحظات')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات النظام والتدقيق')
                    ->description('معلومات إنشاء سجل الرسوم وآخر تعديل عليه')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('updatedBy.full_name')
                                    ->label('آخر تعديل بواسطة')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء السجل')
                                    ->icon('heroicon-o-calendar-days')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('غير متوفر'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->icon('heroicon-o-arrow-path')
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('غير متوفر'),
                            ]),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
