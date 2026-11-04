<?php

namespace App\Filament\Resources\IdentityCards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                        Grid::make()
                            ->columns(fn (string $operation): int => $operation === 'edit' ? 3 : 2)
                        ->schema([
                                Select::make('citizen_id')
                                    ->label('المواطن')
                                    ->relationship(
                                        name: 'citizen',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: function ($query, $record = null) {
                                            $query->orderBy('first_name')
                                                ->orderBy('father_name')
                                                ->orderBy('middle_name')
                                                ->orderBy('last_name');

                                            if (! $record) {
                                                $query->where(
                                                    'verification_status',
                                                    'approved'
                                                );
                                            }

                                            return $query;
                                        }
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

                                Select::make('status')
                                    ->label('الإجراء')
                                    ->options([
                                        'lost' => 'مفقودة',
                                        'damaged' => 'تالفة',
                                        'cancelled' => 'ملغاة',
                                    ])
                                    ->placeholder('اختر الإجراء')
                                    ->native(false)
                                    ->live()
                                    ->visible(
                                        fn (string $operation): bool => $operation === 'edit'
                                    )
                                    ->afterStateHydrated(
                                        function (Select $component, $state): void {
                                            if (! in_array($state, [
                                                'lost',
                                                'damaged',
                                                'cancelled',
                                            ], true)) {
                                                $component->state(null);
                                            }
                                        }
                                    )
                                    ->dehydrated(
                                        fn ($state): bool => filled($state)
                                    ),

                                Textarea::make('rejection_reason')
                                    ->label('سبب الإجراء')
                                    ->placeholder('اكتب سبب الإجراء')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->visible(
                                        fn (Get $get, string $operation): bool => $operation === 'edit'
                                            && in_array($get('status'), [
                                                'lost',
                                                'damaged',
                                                'cancelled',
                                            ], true)
                                    )
                                    ->required(
                                        fn (Get $get): bool => in_array($get('status'), [
                                            'lost',
                                            'damaged',
                                            'cancelled',
                                        ], true)
                                    )
                                    ->columnSpanFull(),
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
