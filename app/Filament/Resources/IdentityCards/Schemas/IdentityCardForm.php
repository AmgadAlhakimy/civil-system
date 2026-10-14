<?php

namespace App\Filament\Resources\IdentityCards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                                        'required' => 'يرجى اختيار الفرع المُصدِر للبطاقة',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات وبيانات إضافية مرتبطة بالبطاقة')
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
