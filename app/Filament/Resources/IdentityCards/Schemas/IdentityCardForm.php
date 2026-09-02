<?php

namespace App\Filament\Resources\IdentityCards\Schemas;

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
                                    ->relationship(
                                        name: 'citizen',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: fn ($query) => $query
                                            ->orderBy('first_name')
                                            ->orderBy('father_name')
                                            ->orderBy('middle_name')
                                            ->orderBy('last_name')
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn ($record) => collect([
                                                $record->first_name,
                                                $record->father_name,
                                                $record->middle_name,
                                                $record->last_name,
                                            ])
                                                ->filter()
                                                ->join(' ')
                                            . ' — ' . $record->national_id
                                    )
                                    ->searchable([
                                        'first_name',
                                        'father_name',
                                        'middle_name',
                                        'last_name',
                                        'national_id',
                                    ])
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->disabled(fn ($record): bool => $record !== null)
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المواطن',
                                    ]),

                                TextInput::make('id_number')
                                    ->label('رقم البطاقة')
                                    ->required()
                                    ->maxLength(11)
                                    ->rule('regex:/^[0-9]{11}$/')
                                    ->unique(
                                        table: 'identity_cards',
                                        column: 'id_number',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'رقم البطاقة مطلوب',
                                        'regex' => 'رقم البطاقة يجب أن يتكون من 11 رقمًا بالضبط',
                                        'unique' => 'رقم البطاقة مسجل مسبقًا',
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
