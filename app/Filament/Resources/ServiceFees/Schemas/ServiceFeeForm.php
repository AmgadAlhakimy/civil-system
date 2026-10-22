<?php

namespace App\Filament\Resources\ServiceFees\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceFeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ═══════════════════════════════════════════════
                // القسم 1: بيانات الخدمة
                // ═══════════════════════════════════════════════
                Section::make('بيانات الخدمة')
                    ->description('المعرّف والاسم والفئة')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('service_key')
                            ->label('المفتاح (بالإنجليزية)')
                            ->helperText('مثال: new_id_card, renew_passport. لا تغيّره إلا إذا كنت تعرف ما تفعل.')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->disabledOn('edit'),   // يُمنع التعديل بعد الإنشاء

                        TextInput::make('service_name')
                            ->label('اسم الخدمة (بالعربية)')
                            ->placeholder('مثال: إصدار بطاقة شخصية جديدة')
                            ->required()
                            ->maxLength(255),

                        Select::make('category')
                            ->label('الفئة')
                            ->options([
                                'id_card' => 'بطاقة شخصية',
                                'passport' => 'جواز سفر',
                                'family_card' => 'بطاقة عائلية',
                                'certificate' => 'شهادات',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable(),
                    ])
                    ->columns(2),

                // ═══════════════════════════════════════════════
                // القسم 2: الرسوم
                // ═══════════════════════════════════════════════
                Section::make('الرسوم')
                    ->description('قيمة الرسوم والعملة')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextInput::make('amount')
                            ->label('قيمة الرسوم')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->default(0.00)
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->helperText('أدخل القيمة بالأرقام، مثال: 5000'),

                        Select::make('currency')
                            ->label('العملة')
                            ->options([
                                'YER' => 'ريال يمني (YER)',
                                'USD' => 'دولار أمريكي (USD)',
                                'SAR' => 'ريال سعودي (SAR)',
                            ])
                            ->required()
                            ->native(false)
                            ->default('YER'),
                    ])
                    ->columns(2),

                // ═══════════════════════════════════════════════
                // القسم 3: الحالة والملاحظات
                // ═══════════════════════════════════════════════
                Section::make('الحالة والملاحظات')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('مفعّلة')
                            ->helperText('عند الإيقاف، لن تظهر الخدمة للمواطنين في التطبيق.')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),

                        Textarea::make('notes')
                            ->label('ملاحظات (اختياري)')
                            ->placeholder('أي معلومات إضافية عن هذه الرسوم')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}