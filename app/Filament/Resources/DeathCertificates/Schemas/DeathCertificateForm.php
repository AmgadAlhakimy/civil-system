<?php

namespace App\Filament\Resources\DeathCertificates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeathCertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات شهادة الوفاة')
                    ->description('المعلومات الأساسية لشهادة الوفاة')
                    ->icon('heroicon-o-document-text')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('deceased_id')
                                    ->label('المتوفى')
                                    ->relationship(
                                        name: 'deceased',
                                        titleAttribute: 'full_name',
                                        modifyQueryUsing: function ($query, $get, $record) {
                                            $query->where(function ($query) use ($record) {
                                                $query->whereDoesntHave('deathCertificates');

                                                if ($record?->deceased_id) {
                                                    $query->orWhere(
                                                        'id',
                                                        $record->deceased_id
                                                    );
                                                }
                                            });
                                        },
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المتوفى',
                                        'exists' => 'المواطن المحدد غير صالح',
                                    ]),

                                TextInput::make('certificate_number')
                                    ->label('رقم شهادة الوفاة')
                                    ->required()
                                    ->numeric()
                                    ->rules([
                                        'digits:11',
                                    ])
                                    ->unique(
                                        table: 'death_certificates',
                                        column: 'certificate_number',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'رقم شهادة الوفاة مطلوب',
                                        'digits' => 'يجب أن يتكون رقم شهادة الوفاة من 11 رقمًا بالضبط',
                                        'unique' => 'رقم شهادة الوفاة مسجل مسبقًا',
                                    ]),

                                DatePicker::make('death_date')
                                    ->label('تاريخ الوفاة')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->maxDate(now())
                                    ->validationMessages([
                                        'required' => 'تاريخ الوفاة مطلوب',
                                        'date' => 'تاريخ الوفاة غير صالح',
                                    ]),

                                TextInput::make('place_of_death')
                                    ->label('مكان الوفاة')
                                    ->required()
                                    ->maxLength(100)
                                    ->validationMessages([
                                        'required' => 'مكان الوفاة مطلوب',
                                        'max' => 'مكان الوفاة طويل جدًا',
                                    ]),

                                TextInput::make('cause_of_death')
                                    ->label('سبب الوفاة')
                                    ->required()
                                    ->maxLength(200)
                                    ->validationMessages([
                                        'required' => 'سبب الوفاة مطلوب',
                                        'max' => 'سبب الوفاة طويل جدًا',
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
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'تاريخ الإصدار مطلوب',
                                    ]),

                                Select::make('status')
                                    ->label('حالة الشهادة')
                                    ->options([
                                        'pending' => 'قيد الانتظار',
                                        'approved' => 'معتمدة',
                                        'cancelled' => 'ملغاة',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->native(false)
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpanFull()
                                    ->validationMessages([
                                        'required' => 'حالة الشهادة مطلوبة',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات وبيانات إضافية مرتبطة بشهادة الوفاة')
                    ->icon('heroicon-o-information-circle')
                    ->schema([

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
