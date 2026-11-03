<?php

namespace App\Filament\Resources\Governorates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GovernorateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->description('المعلومات التعريفية الخاصة بالمحافظة')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 3,
                        ])
                            ->schema([
                                TextEntry::make('code')
                                    ->label('رمز المحافظة')
                                    ->icon('heroicon-o-hashtag')
                                    ->copyable()
                                    ->placeholder('غير محدد'),

                                TextEntry::make('name_ar')
                                    ->label('اسم المحافظة بالعربية')
                                    ->icon('heroicon-o-map')
                                    ->weight('bold')
                                    ->placeholder('غير محدد'),

                                TextEntry::make('name_en')
                                    ->label('اسم المحافظة بالإنجليزية')
                                    ->icon('heroicon-o-language')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('حالة المحافظة')
                    ->description('حالة المحافظة وإمكانية استخدامها في النظام')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('حالة المحافظة')
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
                    ->description('تواريخ إنشاء بيانات المحافظة وآخر تحديث لها')
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
