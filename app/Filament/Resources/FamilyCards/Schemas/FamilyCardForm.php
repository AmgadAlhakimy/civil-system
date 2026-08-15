<?php

namespace App\Filament\Resources\FamilyCards\Schemas;

use App\Models\Citizen;
use Filament\Forms\Components\DatePicker;
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
                                    ->relationship('head', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار رب الأسرة',
                                    ]),

                                TextInput::make('card_number')
                                    ->label('رقم البطاقة العائلية')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(11)
                                    ->rules([
                                        'digits:11',
                                    ])
                                    ->unique(
                                        table: 'family_cards',
                                        column: 'card_number',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'رقم البطاقة العائلية مطلوب',
                                        'digits' => 'يجب أن يتكون رقم البطاقة العائلية من 11 رقمًا بالضبط',
                                        'numeric' => 'رقم البطاقة يجب أن يحتوي على أرقام فقط',
                                        'unique' => 'رقم البطاقة العائلية مسجل مسبقًا',
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
                                    ->dehydrated(),

                                DatePicker::make('expiry_date')
                                    ->label('تاريخ الانتهاء')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->minDate(fn ($get) => $get('issue_date'))
                                    ->closeOnDateSelection()
                                    ->validationMessages([
                                        'required' => 'يرجى تحديد تاريخ انتهاء البطاقة',
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
                                                titleAttribute: 'full_name',
                                                modifyQueryUsing: function ($query, $get) {
                                                    $headId = $get('../../head_id');

                                                    if ($headId) {
                                                        $query->where('id', '!=', $headId);
                                                    }
                                                },
                                            )
                                            ->searchable()
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
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['added_by'] = auth()->id();
                                $data['is_active'] = true;

                                return $data;
                            })
                            ->addActionLabel('إضافة فرد')
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                if (empty($state['citizen_id'])) {
                                    return 'فرد جديد';
                                }

                                return Citizen::find($state['citizen_id'])?->full_name ?? 'فرد جديد';
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
