<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->description('المعلومات التعريفية وبيانات التواصل الخاصة بالفرع')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                            'xl' => 3,
                        ])
                            ->schema([
                                TextEntry::make('code')
                                    ->label('رمز الفرع')
                                    ->icon('heroicon-o-hashtag')
                                    ->copyable()
                                    ->placeholder('غير محدد'),

                                TextEntry::make('name')
                                    ->label('اسم الفرع')
                                    ->icon('heroicon-o-building-office')
                                    ->weight('bold')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('governorate.name_ar')
                                    ->label('المحافظة')
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('غير محددة'),

                                TextEntry::make('address')
                                    ->label('عنوان الفرع')
                                    ->icon('heroicon-o-map-pin')
                                    ->copyable()
                                    ->placeholder('غير محدد'),

                                TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->placeholder('غير محدد'),

                                TextEntry::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('إدارة الفرع')
                    ->description('بيانات المدير المسؤول وحالة تشغيل الفرع')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextEntry::make('manager.full_name')
                                    ->label('مدير الفرع')
                                    ->icon('heroicon-o-user')
                                    ->weight('medium')
                                    ->placeholder('لم يتم تعيين مدير'),

                                TextEntry::make('is_active')
                                    ->label('حالة الفرع')
                                    ->icon(
                                        fn ($state): string => (bool) $state
                                            ? 'heroicon-o-check-circle'
                                            : 'heroicon-o-x-circle'
                                    )
                                    ->iconColor(
                                        fn ($state): string => (bool) $state
                                            ? 'success'
                                            : 'danger'
                                    )
                                    ->formatStateUsing(
                                        fn ($state): string => (bool) $state
                                            ? 'نشط'
                                            : 'غير نشط'
                                    ),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات السجل')
                    ->description('التواريخ المرتبطة بإنشاء بيانات الفرع وتحديثها')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('تاريخ الإنشاء')
                                    ->icon('heroicon-o-calendar-days')
                                    ->dateTime('Y/m/d - H:i')
                                    ->placeholder('غير متوفر'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->icon('heroicon-o-arrow-path')
                                    ->dateTime('Y/m/d - H:i')
                                    ->placeholder('غير متوفر'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
