<?php

namespace App\Filament\Resources\Districts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DistrictInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->description('المعلومات التعريفية الخاصة بالمديرية')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextEntry::make('code')
                                    ->label('رمز المديرية')
                                    ->icon('heroicon-o-hashtag')
                                    ->copyable()
                                    ->placeholder('غير محدد'),

                                TextEntry::make('governorate.name_ar')
                                    ->label('المحافظة')
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('name_ar')
                                    ->label('اسم المديرية بالعربية')
                                    ->icon('heroicon-o-map')
                                    ->weight('bold')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('name_en')
                                    ->label('اسم المديرية بالإنجليزية')
                                    ->icon('heroicon-o-language')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('حالة المديرية')
                    ->description('حالة المديرية وإمكانية استخدامها في النظام')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('حالة المديرية')
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
                                            ? 'نشطة'
                                            : 'غير نشطة'
                                    ),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات السجل')
                    ->description('تواريخ إنشاء بيانات المديرية وآخر تحديث لها')
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
