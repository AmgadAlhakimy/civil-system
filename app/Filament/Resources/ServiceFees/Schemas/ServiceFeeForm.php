<?php

namespace App\Filament\Resources\ServiceFees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceFeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الخدمة والرسوم')
                    ->description('اسم الخدمة وتصنيفها وقيمة رسومها بالريال اليمني')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('service_name')
                                    ->label('اسم الخدمة')
                                    ->placeholder('مثال: إصدار بطاقة شخصية جديدة')
                                    ->required()
                                    ->maxLength(150)
                                    ->validationMessages([
                                        'required' => 'يرجى إدخال اسم الخدمة.',
                                        'max' => 'يجب ألا يتجاوز اسم الخدمة 150 حرفًا.',
                                    ]),

                                Select::make('category')
                                    ->label('تصنيف الخدمة')
                                    ->placeholder('اختر تصنيف الخدمة')
                                    ->options([
                                        'identity_card' => 'بطاقة شخصية',
                                        'passport' => 'جواز سفر',
                                        'family_card' => 'بطاقة عائلية',
                                        'birth_certificate' => 'شهادة ميلاد',
                                        'death_certificate' => 'شهادة وفاة',
                                        'other' => 'خدمات أخرى',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->searchable()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار تصنيف الخدمة.',
                                    ]),

                                TextInput::make('amount')
                                    ->label('قيمة الرسوم')
                                    ->placeholder('أدخل المبلغ')
                                    ->required()
                                    ->numeric()
                                    ->rules([
                                        'numeric',
                                        'min:0',
                                        'max:999999.99',
                                        'regex:/^\d+(\.\d{1,2})?$/',
                                    ])
                                    ->suffix('ريال يمني')
                                    ->inputMode('decimal')
                                    ->validationMessages([
                                        'required' => 'يرجى إدخال قيمة الرسوم.',
                                        'numeric' => 'يجب أن تكون قيمة الرسوم رقمًا.',
                                        'min' => 'لا يمكن أن تكون قيمة الرسوم سالبة.',
                                        'max' => 'قيمة الرسوم تتجاوز الحد الأقصى المسموح به 999999.99.',
                                        'regex' => 'يجب أن تكون قيمة الرسوم رقمًا صحيحًا أو عشريًا بمنزلتين عشريتين كحد أقصى.',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('الحالة والملاحظات')
                    ->description('إدارة تفعيل الخدمة وإضافة التفاصيل الإضافية')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('حالة الخدمة')
                            ->helperText('عند تفعيل الخدمة، يمكن استخدامها بحسب صلاحيات ومنطق النظام.')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),

                        Textarea::make('notes')
                            ->label('ملاحظات إضافية')
                            ->placeholder('اكتب أي تفاصيل أو تعليمات متعلقة بالرسوم')
                            ->rows(4)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'يجب ألا تتجاوز الملاحظات 500 حرف.',
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
