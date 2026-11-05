<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات التقرير')
                    ->description('المعلومات الأساسية الخاصة بالتقرير')
                    ->icon('heroicon-o-document-chart-bar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('اسم التقرير')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(150)
                                    ->trim(),

                                Select::make('report_type')
                                    ->label('نوع التقرير')
                                    ->options([
                                        'citizens' => 'تقرير المواطنين',
                                        'birth_certificates' => 'تقرير شهادات الميلاد',
                                        'death_certificates' => 'تقرير شهادات الوفاة',
                                        'identity_cards' => 'تقرير البطاقات الشخصية',
                                        'family_cards' => 'تقرير البطاقات العائلية',
                                        'passports' => 'تقرير الجوازات',
                                        'appointments' => 'تقرير المواعيد',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->in([
                                        'citizens',
                                        'birth_certificates',
                                        'death_certificates',
                                        'identity_cards',
                                        'family_cards',
                                        'passports',
                                        'appointments',
                                    ])
                                    ->afterStateUpdated(function (Set $set): void {
                                        $set('gender', null);
                                        $set('marital_status', null);
                                        $set('from_date', null);
                                        $set('to_date', null);
                                        $set('status_filter', null);
                                    }),

                                Select::make('format')
                                    ->label('صيغة التقرير')
                                    ->options([
                                        'pdf' => 'PDF',
                                        'xlsx' => 'Excel',
                                    ])
                                    ->default('pdf')
                                    ->required()
                                    ->native(false)
                                    ->in(['pdf', 'xlsx']),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('فلاتر التقرير')
                    ->description('حدد معايير التصفية للحصول على البيانات المطلوبة')
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
                                    ->native(false)
                                    ->visible(fn (Get $get): bool => $get('report_type') === 'citizens'),

                                Select::make('marital_status')
                                    ->label('الحالة الاجتماعية')
                                    ->options([
                                        'single' => 'أعزب',
                                        'married' => 'متزوج',
                                        'divorced' => 'مطلق',
                                        'widowed' => 'أرمل',
                                    ])
                                    ->native(false)
                                    ->visible(fn (Get $get): bool => $get('report_type') === 'citizens'),

                                DatePicker::make('from_date')
                                    ->label('من تاريخ')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(fn (Get $get) => $get('report_type') === 'appointments' ? null : now()->toDateString())
                                    ->visible(fn (Get $get): bool => in_array($get('report_type'), [
                                        'citizens',
                                        'birth_certificates',
                                        'death_certificates',
                                        'identity_cards',
                                        'family_cards',
                                        'passports',
                                        'appointments',
                                    ], true))
                                    ->live()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                                        $toDate = $get('to_date');

                                        if (filled($state) && filled($toDate) && $toDate < $state) {
                                            $set('to_date', $state);
                                        }
                                    }),

                                DatePicker::make('to_date')
                                    ->label('إلى تاريخ')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->minDate(fn (Get $get) => $get('from_date'))
                                    ->maxDate(fn (Get $get) => $get('report_type') === 'appointments' ? null : now()->toDateString())
                                    ->visible(fn (Get $get): bool => in_array($get('report_type'), [
                                        'citizens',
                                        'birth_certificates',
                                        'death_certificates',
                                        'identity_cards',
                                        'family_cards',
                                        'passports',
                                        'appointments',
                                    ], true))
                                    ->afterOrEqual('from_date'),

                                Select::make('status_filter')
                                    ->label('حالة الموعد')
                                    ->options([
                                        'pending' => 'قيد الانتظار',
                                        'confirmed' => 'مؤكد',
                                        'attended' => 'تم الحضور',
                                        'cancelled' => 'ملغي',
                                        'no_show' => 'لم يحضر',
                                    ])
                                    ->native(false)
                                    ->visible(fn (Get $get): bool => $get('report_type') === 'appointments'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
