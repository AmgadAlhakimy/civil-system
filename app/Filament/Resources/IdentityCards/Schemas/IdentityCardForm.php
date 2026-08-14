<?php

namespace App\Filament\Resources\IdentityCards\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IdentityCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات البطاقة الشخصية')
                    ->description('المعلومات الأساسية للبطاقة الشخصية')
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

                                TextInput::make('id_number')
                                    ->label('رقم البطاقة')
                                    ->required()
                                    ->numeric()
                                    ->rules([
                                        'digits:11',
                                    ])
                                    ->unique(
                                        table: 'identity_cards',
                                        column: 'id_number',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'رقم البطاقة مطلوب',
                                        'digits' => 'يجب أن يتكون رقم البطاقة من 11 رقمًا بالضبط',
                                        'numeric' => 'رقم البطاقة يجب أن يحتوي على أرقام فقط',
                                        'unique' => 'رقم البطاقة مسجل مسبقًا',
                                    ]),

                                DatePicker::make('issue_date')
                                    ->label('تاريخ الإصدار')
                                    ->default(now())
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->maxDate(now())
                                    ->disabled()
                                    ->dehydrated(),

                                DatePicker::make('expiry_date')
                                    ->label('تاريخ الانتهاء')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->minDate(fn ($get) => $get('issue_date'))
                                    ->closeOnDateSelection()
                                    ->validationMessages([
                                        'required' => 'يرجى تحديد تاريخ انتهاء البطاقة',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات وبيانات إضافية مرتبطة بالبطاقة')
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
