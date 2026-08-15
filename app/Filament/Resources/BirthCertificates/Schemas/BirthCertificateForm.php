<?php

namespace App\Filament\Resources\BirthCertificates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BirthCertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات شهادة الميلاد')
                    ->description('المعلومات الأساسية لشهادة الميلاد')
                    ->icon('heroicon-o-document-text')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('child_id')
                                    ->label('الطفل')
                                    ->relationship(
                                        name: 'child',
                                        titleAttribute: 'full_name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الطفل',
                                        'exists' => 'الطفل المحدد غير صالح',
                                    ]),

                                TextInput::make('certificate_number')
                                    ->label('رقم شهادة الميلاد')
                                    ->required()
                                    ->rules([
                                        'digits:11',
                                    ])
                                    ->unique(
                                        table: 'birth_certificates',
                                        column: 'certificate_number',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'رقم شهادة الميلاد مطلوب',
                                        'digits' => 'يجب أن يتكون رقم شهادة الميلاد من 11 رقمًا بالضبط',
                                        'unique' => 'رقم شهادة الميلاد مسجل مسبقًا',
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
                                        'active' => 'سارية',
                                        'cancelled' => 'ملغاة',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->native(false)
                                    ->disabled()
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'حالة الشهادة مطلوبة',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات الوالدين')
                    ->description('بيانات والد ووالدة الطفل')
                    ->icon('heroicon-o-users')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('father_id')
                                    ->label('الأب')
                                    ->relationship(
                                        name: 'father',
                                        titleAttribute: 'full_name',
                                        modifyQueryUsing: function ($query, $get) {
                                            $childId = $get('child_id');

                                            $query->where('gender', 'male');

                                            if ($childId) {
                                                $query->where('id', '!=', $childId);
                                            }
                                        },
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الأب',
                                        'exists' => 'لا يمكن اختيار الطفل نفسه كأب',
                                    ]),

                                Select::make('mother_id')
                                    ->label('الأم')
                                    ->relationship(
                                        name: 'mother',
                                        titleAttribute: 'full_name',
                                        modifyQueryUsing: function ($query, $get) {
                                            $childId = $get('child_id');

                                            $query->where('gender', 'female');

                                            if ($childId) {
                                                $query->where('id', '!=', $childId);
                                            }
                                        },
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الأم',
                                        'exists' => 'لا يمكن اختيار الطفل نفسه كأم',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات وبيانات إضافية مرتبطة بالشهادة')
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
