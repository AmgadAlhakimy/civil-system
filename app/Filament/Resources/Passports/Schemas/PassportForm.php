<?php

namespace App\Filament\Resources\Passports\Schemas;

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
                        Grid::make(6)
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
                                    ->columnSpan(3)
                                    ->disabled(
                                        fn ($record): bool => $record?->status !== null
                                            && ! in_array($record->status, [
                                                'pending',
                                                'rejected',
                                            ], true)
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المواطن',
                                    ]),

                                TextInput::make('passport_number')
                                    ->label('رقم الجواز')
                                    ->required()
                                    ->maxLength(9)
                                    ->rule('regex:/^[0-9]{9}$/')
                                    ->unique(
                                        table: 'passports',
                                        column: 'passport_number',
                                        ignoreRecord: true,
                                    )
                                    ->columnSpan(2)
                                    ->disabled(
                                        fn ($record): bool => $record?->status !== null
                                            && ! in_array($record->status, [
                                                'pending',
                                                'rejected',
                                            ], true)
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'رقم الجواز مطلوب',
                                        'regex' => 'رقم الجواز يجب أن يتكون من 9 أرقام بالضبط',
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
                                    ->columnSpan(1)
                                    ->disabled(
                                        fn ($record): bool => $record?->status !== null
                                            && ! in_array($record->status, [
                                                'pending',
                                                'rejected',
                                            ], true)
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار نوع الجواز',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('الملاحظات والبيانات الإضافية المرتبطة بالجواز')
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
