<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Infolists\Components\IconEntry;
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

                // البيانات الأساسية
                Section::make('بيانات الفرع')
                    ->description('المعلومات الأساسية للفرع')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('code')
                                    ->label('رمز الفرع')
                                    ->badge(),

                                TextEntry::make('name')
                                    ->label('اسم الفرع'),

                                TextEntry::make('address')
                                    ->label('عنوان الفرع'),

                                TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->copyable()
                                    ->copyMessage('تم نسخ رقم الهاتف'),

                                TextEntry::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->copyable()
                                    ->copyMessage('تم نسخ البريد الإلكتروني')
                                    ->placeholder('غير محدد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                // الإدارة والحالة
                Section::make('الإدارة والحالة')
                    ->description('معلومات مدير الفرع وحالة الفرع')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('manager_name')
                                    ->label('مدير الفرع')
                                    ->placeholder('غير محدد'),

                                IconEntry::make('is_active')
                                    ->label('حالة الفرع')
                                    ->boolean(),

                            ]),
                    ])
                    ->columnSpanFull(),

                // معلومات النظام
                Section::make('معلومات النظام')
                    ->description('معلومات إنشاء وتحديث وحذف الفرع')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء الفرع')
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
