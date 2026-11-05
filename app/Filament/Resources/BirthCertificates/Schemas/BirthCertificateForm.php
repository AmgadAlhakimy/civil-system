<?php

namespace App\Filament\Resources\BirthCertificates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                                    ->searchPrompt('ابحث باسم الطفل')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->disabled(
                                        fn (string $operation): bool => $operation === 'edit'
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الطفل',
                                        'exists' => 'الطفل المحدد غير صالح',
                                    ]),

                                Select::make('branch_id')
                                    ->label('جهة الإصدار')
                                    ->relationship(
                                        name: 'branch',
                                        titleAttribute: 'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الفرع المُصدِر للشهادة',
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

                                            $query
                                                ->where('verification_status', 'approved')
                                                ->where('gender', 'male');

                                            if ($childId) {
                                                $query->where('id', '!=', $childId);
                                            }

                                            return $query
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
                                    ])
                                    ->searchPrompt('ابحث باسم الأب')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الأب',
                                        'exists' => 'الأب المحدد غير صالح',
                                    ]),

                                Select::make('mother_id')
                                    ->label('الأم')
                                    ->relationship(
                                        name: 'mother',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: function ($query, $get) {
                                            $childId = $get('child_id');

                                            $query
                                                ->where('verification_status', 'approved')
                                                ->where('gender', 'female');

                                            if ($childId) {
                                                $query->where('id', '!=', $childId);
                                            }

                                            return $query
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
                                    ])
                                    ->searchPrompt('ابحث باسم الأم')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الأم',
                                        'exists' => 'الأم المحددة غير صالحة',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات وبيانات إضافية مرتبطة بالشهادة')
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
