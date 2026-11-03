<?php

namespace App\Filament\Resources\Districts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\Rules\Unique;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->description('بيانات المديرية والمحافظة التابعة لها')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Select::make('governorate_id')
                            ->label('المحافظة')
                            ->relationship(
                                name: 'governorate',
                                titleAttribute: 'name_ar',
                                modifyQueryUsing: fn ($query) => $query
                                    ->where('is_active', true)
                                    ->orderBy('name_ar')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('اختر المحافظة')
                            ->validationMessages([
                                'required' => 'يرجى اختيار المحافظة التابعة لها المديرية',
                            ]),

                        TextInput::make('code')
                            ->label('رمز المديرية')
                            ->placeholder('مثال: SNA-OLD')
                            ->required()
                            ->minLength(2)
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[A-Za-z0-9_-]+$/')
                            ->dehydrateStateUsing(
                                fn (?string $state): ?string => $state !== null
                                    ? strtoupper(trim($state))
                                    : null
                            )
                            ->validationMessages([
                                'required' => 'حقل رمز المديرية مطلوب',
                                'min' => 'يجب ألا يقل رمز المديرية عن حرفين',
                                'max' => 'يجب ألا يتجاوز الرمز 10 أحرف',
                                'unique' => 'رمز المديرية مستخدم مسبقًا',
                                'regex' => 'الرمز يقبل الأحرف الإنجليزية والأرقام والشرطة والشرطة السفلية فقط',
                            ]),
                        TextInput::make('name_ar')
                            ->label('اسم المديرية بالعربية')
                            ->placeholder('مثال: صنعاء القديمة')
                            ->required()
                            ->minLength(2)
                            ->maxLength(150)
                            ->autofocus()
                            ->regex('/^[\p{Arabic}\s]+$/u')
                            ->unique(
                                modifyRuleUsing: fn (
                                    Unique $rule,
                                    Get $get
                                ) => $rule->where(
                                    'governorate_id',
                                    $get('governorate_id')
                                ),
                                ignoreRecord: true,
                            )
                            ->dehydrateStateUsing(
                                fn (?string $state): ?string => $state !== null
                                    ? trim(preg_replace('/\s+/u', ' ', $state))
                                    : null
                            )
                            ->validationMessages([
                                'required' => 'حقل اسم المديرية بالعربية مطلوب',
                                'min' => 'يجب ألا يقل اسم المديرية عن حرفين',
                                'max' => 'يجب ألا يتجاوز اسم المديرية 150 حرفًا',
                                'regex' => 'اسم المديرية يجب أن يحتوي على حروف عربية ومسافات فقط',
                                'unique' => 'اسم المديرية بالعربية موجود مسبقًا في هذه المحافظة',
                            ]),

                        TextInput::make('name_en')
                            ->label('اسم المديرية بالإنجليزية')
                            ->placeholder('مثال: Old Sanaa')
                            ->required()
                            ->minLength(2)
                            ->maxLength(150)
                            ->regex('/^[A-Za-z\s]+$/')
                            ->unique(
                                modifyRuleUsing: fn (
                                    Unique $rule,
                                    Get $get
                                ) => $rule->where(
                                    'governorate_id',
                                    $get('governorate_id')
                                ),
                                ignoreRecord: true,
                            )
                            ->dehydrateStateUsing(
                                fn (?string $state): ?string => $state !== null
                                    ? trim(preg_replace('/\s+/', ' ', $state))
                                    : null
                            )
                            ->validationMessages([
                                'required' => 'حقل اسم المديرية بالإنجليزية مطلوب',
                                'min' => 'يجب ألا يقل اسم المديرية عن حرفين',
                                'max' => 'يجب ألا يتجاوز اسم المديرية 150 حرفًا',
                                'regex' => 'اسم المديرية يجب أن يحتوي على حروف إنجليزية ومسافات فقط',
                                'unique' => 'اسم المديرية بالإنجليزية موجود مسبقًا في هذه المحافظة',
                            ]),


                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('حالة المديرية')
                    ->description('تحديد إمكانية استخدام المديرية في النظام')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('المديرية مفعّلة')
                            ->helperText(
                                'يمكن استبعاد المديريات غير المفعّلة من قوائم الاختيار'
                            )
                            ->default(true)
                            ->required()
                            ->onColor('success')
                            ->offColor('danger'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
