<?php

namespace App\Filament\Resources\Governorates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GovernorateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->description('اسم المحافظة بالعربية والإنجليزية ورمزها التعريفي')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        TextInput::make('code')
                            ->label('رمز المحافظة')
                            ->placeholder('مثال: SNA')
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
                                'required' => 'حقل رمز المحافظة مطلوب',
                                'min' => 'يجب ألا يقل الرمز عن حرفين',
                                'max' => 'يجب ألا يتجاوز الرمز 10 أحرف',
                                'unique' => 'رمز المحافظة مستخدم مسبقاً',
                                'regex' => 'الرمز يقبل الأحرف الإنجليزية والأرقام والشرطة والشرطة السفلية فقط',
                            ]),

                        TextInput::make('name_ar')
                            ->label('اسم المحافظة بالعربية')
                            ->placeholder('مثال: صنعاء')
                            ->required()
                            ->minLength(2)
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->autofocus()
                            ->regex('/^[\p{Arabic}\s]+$/u')
                            ->dehydrateStateUsing(
                                fn (?string $state): ?string => $state !== null
                                    ? trim(preg_replace('/\s+/u', ' ', $state))
                                    : null
                            )
                            ->validationMessages([
                                'required' => 'حقل اسم المحافظة بالعربية مطلوب',
                                'min' => 'يجب ألا يقل اسم المحافظة عن حرفين',
                                'max' => 'يجب ألا يتجاوز اسم المحافظة 100 حرف',
                                'unique' => 'اسم المحافظة بالعربية موجود مسبقًا، يرجى اختيار اسم آخر',
                                'regex' => 'اسم المحافظة بالعربية يجب أن يحتوي على حروف عربية ومسافات فقط',
                            ]),

                        TextInput::make('name_en')
                            ->label('اسم المحافظة بالإنجليزية')
                            ->placeholder('مثال: Sanaa')
                            ->required()
                            ->minLength(2)
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[A-Za-z\s]+$/')
                            ->dehydrateStateUsing(
                                fn (?string $state): ?string => $state !== null
                                    ? trim(preg_replace('/\s+/', ' ', $state))
                                    : null
                            )
                            ->validationMessages([
                                'required' => 'حقل اسم المحافظة بالإنجليزية مطلوب',
                                'min' => 'يجب ألا يقل اسم المحافظة عن حرفين',
                                'max' => 'يجب ألا يتجاوز اسم المحافظة 100 حرف',
                                'unique' => 'اسم المحافظة بالإنجليزية موجود مسبقًا، يرجى اختيار اسم آخر',
                                'regex' => 'اسم المحافظة بالإنجليزية يجب أن يحتوي على حروف إنجليزية ومسافات فقط',
                            ]),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('حالة المحافظة')
                    ->description('تحديد إمكانية استخدام المحافظة في النظام')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('المحافظة مفعّلة')
                            ->helperText(
                                'المحافظات غير المفعّلة يمكن استبعادها من قوائم الاختيار'
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
