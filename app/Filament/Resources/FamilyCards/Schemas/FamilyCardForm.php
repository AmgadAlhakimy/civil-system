<?php

namespace App\Filament\Resources\FamilyCards\Schemas;

use App\Models\Citizen;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FamilyCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات البطاقة العائلية')
                    ->description('المعلومات الأساسية للبطاقة العائلية')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('head_id')
                                    ->label('رب الأسرة')
                                    ->relationship(
                                        name: 'head',
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
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار رب الأسرة',
                                    ]),

                                TextInput::make('card_number')
                                    ->label('رقم البطاقة العائلية')
                                    ->required()
                                    ->maxLength(11)
                                    ->rule('regex:/^[0-9]{11}$/')
                                    ->unique(
                                        table: 'family_cards',
                                        column: 'card_number',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'رقم البطاقة العائلية مطلوب',
                                        'regex' => 'رقم البطاقة العائلية يجب أن يتكون من 11 رقمًا بالضبط',
                                        'unique' => 'رقم البطاقة العائلية مسجل مسبقًا',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('أفراد الأسرة')
                    ->description('إضافة أفراد الأسرة المرتبطين بالبطاقة')
                    ->icon('heroicon-o-users')
                    ->schema([

                        Repeater::make('members')
                            ->relationship('members')
                            ->label('أفراد الأسرة')
                            ->schema([

                                Grid::make(3)
                                    ->schema([

                                        Select::make('citizen_id')
                                            ->label('المواطن')
                                            ->relationship(
                                                name: 'citizen',
                                                titleAttribute: 'first_name',
                                                modifyQueryUsing: function ($query, $get) {
                                                    $headId = $get('../../head_id');

                                                    if ($headId) {
                                                        $query->where('id', '!=', $headId);
                                                    }
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
                                            ->distinct()
                                            ->validationMessages([
                                                'required' => 'يرجى اختيار المواطن',
                                                'distinct' => 'لا يمكن إضافة نفس المواطن أكثر من مرة',
                                            ]),

                                        Select::make('relationship')
                                            ->label('صلة القرابة')
                                            ->options([
                                                'spouse' => 'زوج / زوجة',
                                                'child' => 'ابن / ابنة',
                                                'father' => 'أب',
                                                'mother' => 'أم',
                                                'brother' => 'أخ',
                                                'sister' => 'أخت',
                                                'other' => 'أخرى',
                                            ])
                                            ->required()
                                            ->native(false)
                                            ->validationMessages([
                                                'required' => 'يرجى تحديد صلة القرابة',
                                            ]),

                                        Select::make('is_active')
                                            ->label('الحالة')
                                            ->options([
                                                true => 'فعال',
                                                false => 'غير فعال',
                                            ])
                                            ->default(true)
                                            ->required()
                                            ->native(false),

                                        Textarea::make('notes')
                                            ->label('ملاحظات')
                                            ->rows(2)
                                            ->maxLength(500)
                                            ->columnSpanFull(),

                                    ]),
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(
                                function (array $data): array {
                                    $data['added_by'] = auth()->id();
                                    $data['is_active'] = true;

                                    return $data;
                                }
                            )
                            ->addActionLabel('إضافة فرد')
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                if (empty($state['citizen_id'])) {
                                    return 'فرد جديد';
                                }

                                $citizen = Citizen::find($state['citizen_id']);

                                if (! $citizen) {
                                    return 'فرد جديد';
                                }

                                return collect([
                                    $citizen->first_name,
                                    $citizen->father_name,
                                    $citizen->middle_name,
                                    $citizen->last_name,
                                ])
                                    ->filter()
                                    ->join(' ');
                            }),

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
