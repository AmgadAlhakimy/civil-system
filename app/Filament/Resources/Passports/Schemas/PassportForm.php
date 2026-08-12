<?php

namespace App\Filament\Resources\Passports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PassportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات الجواز')
                    ->description('المعلومات الأساسية لجواز السفر')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                Select::make('citizen_id')
                                    ->label('المواطن')
                                    ->relationship('citizen', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المواطن',
                                    ]),

                                TextInput::make('passport_number')
                                    ->label('رقم الجواز')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique(
                                        table: 'passports',
                                        column: 'passport_number',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'رقم الجواز مطلوب',
                                        'unique' => 'رقم الجواز مسجل مسبقاً',
                                    ]),

                                Select::make('type')
                                    ->label('نوع الجواز')
                                    ->options([
                                        'ordinary' => 'عادي',
                                        'diplomatic' => 'دبلوماسي',
                                        'official' => 'رسمي',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار نوع الجواز',
                                    ]),

                                DatePicker::make('issue_date')
                                    ->label('تاريخ الإصدار')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->maxDate(now())
                                    ->default(now())
                                    ->closeOnDateSelection()
                                    ->validationMessages([
                                        'required' => 'يرجى تحديد تاريخ إصدار الجواز',
                                    ]),

                                DatePicker::make('expiry_date')
                                    ->label('تاريخ الانتهاء')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->minDate(fn ($get) => $get('issue_date'))
                                    ->closeOnDateSelection()
                                    ->columnSpanFull()
                                    ->validationMessages([
                                        'required' => 'يرجى تحديد تاريخ انتهاء الجواز',
                                        'after' => 'يجب أن يكون تاريخ الانتهاء بعد تاريخ الإصدار',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات وبيانات إضافية مرتبطة بالجواز')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                Textarea::make('notes')
                                    ->label('ملاحظات')
                                    ->rows(4)
                                    ->maxLength(1000),

                                Textarea::make('qr_code')
                                    ->label('رمز QR')
                                    ->rows(4),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
