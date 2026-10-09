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
                    ->description('المعلومات الأساسية للفرع وبيانات التواصل')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(3)
                            ->schema([

                                TextEntry::make('code')
                                    ->label('رمز الفرع')
                                    ->icon('heroicon-o-hashtag')
                                    ->copyable()
                                    ->copyMessage('تم نسخ رمز الفرع'),

                                TextEntry::make('name')
                                    ->label('اسم الفرع')
                                    ->icon('heroicon-o-building-office')
                                    ->weight('bold')
                                    ->size('lg'),

                                TextEntry::make('address')
                                    ->label('عنوان الفرع')
                                    ->icon('heroicon-o-map-pin')
                                    ->columnSpanFull(),

                                TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage('تم نسخ رقم الهاتف')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage('تم نسخ البريد الإلكتروني')
                                    ->placeholder('غير محدد'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('إدارة الفرع ومعلومات النظام')
                    ->description('معلومات مدير الفرع وحالة التشغيل وتفاصيل النظام')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('manager.full_name')
                                    ->label('مدير الفرع')
                                    ->icon('heroicon-o-user')
                                    ->weight('bold')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('is_active')
                                    ->label('حالة الفرع')
                                    ->icon(
                                        fn (bool $state): string => $state
                                            ? 'heroicon-o-check-circle'
                                            : 'heroicon-o-x-circle'
                                    )
                                    ->iconColor(
                                        fn (bool $state): string => $state
                                            ? 'success'
                                            : 'danger'
                                    )
                                    ->formatStateUsing(
                                        fn (bool $state): string => $state
                                            ? 'نشط'
                                            : 'غير نشط'
                                    )
                                    ->color('gray'),

                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء الفرع')
                                    ->icon('heroicon-o-calendar-days')
                                    ->dateTime('Y-m-d H:i'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->icon('heroicon-o-arrow-path')
                                    ->dateTime('Y-m-d H:i'),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
