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
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Grid::make(6)
                            ->schema([

                                Select::make('citizen_id')
                                    ->label('المواطن')
                                    ->relationship(
                                        name: 'citizen',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: fn ($query) => $query
                                            ->where('verification_status', 'approved')
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
                                    ->searchPrompt('ابحث باسم المواطن أو رقمه الوطني')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
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
                                    ->minLength(9)
                                    ->maxLength(9)
                                    ->rule('regex:/^[0-9]{9}$/')
                                    ->unique(
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
                                        'min' => 'يجب أن يتكون رقم الجواز من 9 أرقام',
                                        'max' => 'يجب أن يتكون رقم الجواز من 9 أرقام',
                                        'regex' => 'رقم الجواز يجب أن يحتوي على أرقام فقط',
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
                                    ->in([
                                        'ordinary',
                                        'diplomatic',
                                        'official',
                                    ])
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
                                        'in' => 'نوع الجواز المحدد غير صالح',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('الملاحظات والبيانات الإضافية المرتبطة بالجواز')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(1)
                            ->schema([

                                Textarea::make('notes')
                                    ->label('ملاحظات')
                                    ->rows(4)
                                    ->maxLength(1000),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
