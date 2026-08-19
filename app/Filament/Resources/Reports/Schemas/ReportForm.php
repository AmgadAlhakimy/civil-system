<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات التقرير')
                    ->description('المعلومات الأساسية للتقرير')
                    ->icon('heroicon-o-document-chart-bar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('اسم التقرير')
                                    ->placeholder('مثال: تقرير المواطنين')
                                    ->required()
                                    ->maxLength(150)
                                    ->validationMessages([
                                        'required' => 'اسم التقرير مطلوب',
                                        'max' => 'اسم التقرير لا يمكن أن يتجاوز 150 حرف',
                                    ]),

                                Select::make('report_type')
                                    ->label('نوع التقرير')
                                    ->options([
                                        'citizens' => 'تقرير المواطنين',
                                        'birth_certificates' => 'تقرير شهادات الميلاد',
                                        'identity_cards' => 'تقرير البطاقات الشخصية',
                                        'family_cards' => 'تقرير البطاقات العائلية',
                                        'passports' => 'تقرير الجوازات',
                                        'appointments' => 'تقرير المواعيد',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($set) {
                                        $set('gender', null);
                                        $set('marital_status', null);
                                        $set('from_date', null);
                                        $set('to_date', null);
                                        $set('status_filter', null);
                                    })
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار نوع التقرير',
                                    ]),

                                Select::make('format')
                                    ->label('صيغة التقرير')
                                    ->options([
                                        'pdf' => 'PDF',
                                        'excel' => 'Excel',
                                    ])
                                    ->default('pdf')
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار صيغة التقرير',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('الفلاتر')
                    ->description('حدد معايير البحث التي تريد استخدامها في التقرير')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('gender')
                                    ->label('الجنس')
                                    ->options([
                                        'male' => 'ذكر',
                                        'female' => 'أنثى',
                                    ])
                                    ->placeholder('جميع المواطنين')
                                    ->native(false),

                                Select::make('marital_status')
                                    ->label('الحالة الاجتماعية')
                                    ->options([
                                        'single' => 'أعزب',
                                        'married' => 'متزوج',
                                        'divorced' => 'مطلق',
                                        'widowed' => 'أرمل',
                                    ])
                                    ->placeholder('جميع الحالات')
                                    ->native(false),
                            ])
                            ->visible(fn ($get) => $get('report_type') === 'citizens'),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('from_date')
                                    ->label('من تاريخ')
                                    ->placeholder('اختر تاريخ البداية')
                                    ->native(false)
                                    ->maxDate(fn ($get) => $get('to_date')),

                                DatePicker::make('to_date')
                                    ->label('إلى تاريخ')
                                    ->placeholder('اختر تاريخ النهاية')
                                    ->native(false)
                                    ->minDate(fn ($get) => $get('from_date')),
                            ])
                            ->visible(fn ($get) => in_array(
                                $get('report_type'),
                                [
                                    'citizens',
                                    'birth_certificates',
                                    'identity_cards',
                                    'family_cards',
                                    'passports',
                                    'appointments',
                                ]
                            )),

                        Select::make('status_filter')
                            ->label('حالة الموعد')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'confirmed' => 'مؤكد',
                                'attended' => 'تم الحضور',
                                'cancelled' => 'ملغي',
                                'no_show' => 'لم يحضر',
                            ])
                            ->placeholder('جميع الحالات')
                            ->native(false)
                            ->visible(fn ($get) => $get('report_type') === 'appointments'),
                    ])
                    ->columnSpanFull(),

                Section::make('حالة التقرير')
                    ->description('الحالة الحالية للتقرير')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Select::make('status')
                            ->label('حالة التقرير')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'completed' => 'مكتمل',
                                'failed' => 'فشل',
                            ])
                            ->default('pending')
                            ->disabled()
                            ->dehydrated()
                            ->native(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
