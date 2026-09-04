<?php

namespace App\Filament\Resources\BirthCertificates\Schemas;

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
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الطفل',
                                        'exists' => 'الطفل المحدد غير صالح',
                                    ]),

                                TextInput::make('certificate_number')
                                    ->label('رقم شهادة الميلاد')
                                    ->required()
                                    ->maxLength(11)
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
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: function ($query, $get) {
                                            $childId = $get('child_id');

                                            $query->where('gender', 'male');

                                            if ($childId) {
                                                $query->where('id', '!=', $childId);
                                            }

                                            $query
                                                ->orderBy('first_name')
                                                ->orderBy('father_name')
                                                ->orderBy('middle_name')
                                                ->orderBy('last_name');
                                        },
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
                                        'national_id',
                                    ])
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
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: function ($query, $get) {
                                            $childId = $get('child_id');

                                            $query->where('gender', 'female');

                                            if ($childId) {
                                                $query->where('id', '!=', $childId);
                                            }

                                            $query
                                                ->orderBy('first_name')
                                                ->orderBy('father_name')
                                                ->orderBy('middle_name')
                                                ->orderBy('last_name');
                                        },
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
                                        'national_id',
                                    ])
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
