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
                                    )
                                    ->searchable([
                                        'first_name',
                                        'father_name',
                                        'middle_name',
                                        'last_name',
                                    ])
                                    ->searchPrompt('ابحث باسم المواطن')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(3)
                                    ->disabled(
                                        fn ($record, string $operation): bool => $operation === 'edit'
                                            || (
                                                $record?->status !== null
                                                && ! in_array($record->status, [
                                                    'pending',
                                                    'rejected',
                                                ], true)
                                            )
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المواطن',
                                    ]),

                                TextInput::make('passport_number')
                                    ->label('رقم الجواز')
                                    ->required()
                                    ->minLength(8)
                                    ->maxLength(8)
                                    ->rule('regex:/^[0-9]{8}$/')
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(2)
                                    ->disabled(
                                        fn ($record, string $operation): bool => $operation === 'edit'
                                            || (
                                                $record?->status !== null
                                                && ! in_array($record->status, [
                                                    'pending',
                                                    'rejected',
                                                ], true)
                                            )
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'رقم الجواز مطلوب',
                                        'min' => 'يجب أن يتكون رقم الجواز من 8 أرقام',
                                        'max' => 'يجب أن يتكون رقم الجواز من 8 أرقام',
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
                                        fn ($record, string $operation): bool => $operation === 'edit'
                                            || (
                                                $record?->status !== null
                                                && ! in_array($record->status, [
                                                    'pending',
                                                    'rejected',
                                                ], true)
                                            )
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار نوع الجواز',
                                        'in' => 'نوع الجواز المحدد غير صالح',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات المواطن باللغة الإنجليزية')
                    ->description('البيانات الإنجليزية المرتبطة بالمواطن')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Grid::make(3)
                            ->schema([

                                TextInput::make('citizen_first_name_en')
                                    ->label('الاسم الأول بالإنجليزية')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule("regex:/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/")
                                    ->validationMessages([
                                        'required' => 'الاسم الأول بالإنجليزية مطلوب',
                                        'max' => 'يجب ألا يتجاوز الاسم الأول 100 حرف',
                                        'regex' => 'يرجى إدخال الاسم الأول بالأحرف الإنجليزية فقط',
                                    ]),

                                TextInput::make('citizen_father_name_en')
                                    ->label('اسم الأب بالإنجليزية')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule("regex:/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/")
                                    ->validationMessages([
                                        'required' => 'اسم الأب بالإنجليزية مطلوب',
                                        'max' => 'يجب ألا يتجاوز اسم الأب 100 حرف',
                                        'regex' => 'يرجى إدخال اسم الأب بالأحرف الإنجليزية فقط',
                                    ]),

                                TextInput::make('citizen_middle_name_en')
                                    ->label('اسم الجد بالإنجليزية')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule("regex:/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/")
                                    ->validationMessages([
                                        'required' => 'اسم الجد بالإنجليزية مطلوب',
                                        'max' => 'يجب ألا يتجاوز اسم الجد 100 حرف',
                                        'regex' => 'يرجى إدخال اسم الجد بالأحرف الإنجليزية فقط',
                                    ]),

                                TextInput::make('citizen_last_name_en')
                                    ->label('اسم العائلة بالإنجليزية')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule("regex:/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/")
                                    ->validationMessages([
                                        'required' => 'اسم العائلة بالإنجليزية مطلوب',
                                        'max' => 'يجب ألا يتجاوز اسم العائلة 100 حرف',
                                        'regex' => 'يرجى إدخال اسم العائلة بالأحرف الإنجليزية فقط',
                                    ]),

                                TextInput::make('citizen_birth_place_en')
                                    ->label('مكان الميلاد بالإنجليزية')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule("regex:/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/")
                                    ->validationMessages([
                                        'required' => 'مكان الميلاد بالإنجليزية مطلوب',
                                        'max' => 'يجب ألا يتجاوز مكان الميلاد 100 حرف',
                                        'regex' => 'يرجى إدخال مكان الميلاد بالأحرف الإنجليزية فقط',
                                    ]),

                                TextInput::make('citizen_occupation_en')
                                    ->label('المهنة بالإنجليزية')
                                    ->required()
                                    ->maxLength(255)
                                    ->rule("regex:/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/")
                                    ->validationMessages([
                                        'required' => 'المهنة بالإنجليزية مطلوبة',
                                        'max' => 'يجب ألا تتجاوز المهنة 255 حرفًا',
                                        'regex' => 'يرجى إدخال المهنة بالأحرف الإنجليزية فقط',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('جهة إصدار الجواز')
                    ->description('بيانات الجهة المصدرة للجواز')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextInput::make('issuing_authority_ar')
                                    ->label('جهة الإصدار بالعربية')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule("regex:/^[\p{Arabic}\sـ\\-،.()]+$/u")
                                    ->validationMessages([
                                        'required' => 'جهة الإصدار بالعربية مطلوبة',
                                        'max' => 'يجب ألا تتجاوز جهة الإصدار 100 حرف',
                                        'regex' => 'يرجى إدخال جهة الإصدار بالأحرف العربية فقط',
                                    ]),

                                TextInput::make('issuing_authority_en')
                                    ->label('جهة الإصدار بالإنجليزية')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule("regex:/^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/")
                                    ->validationMessages([
                                        'required' => 'جهة الإصدار بالإنجليزية مطلوبة',
                                        'max' => 'يجب ألا تتجاوز جهة الإصدار 100 حرف',
                                        'regex' => 'يرجى إدخال جهة الإصدار بالأحرف الإنجليزية فقط',
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
