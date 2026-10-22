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

                // ═══════════════════════════════════════════════
                // القسم 1: البيانات الأساسية
                // ═══════════════════════════════════════════════
                Section::make('البيانات الأساسية')
                    ->description('الاسم والرمز التعريفي للمحافظة')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->placeholder('مثال: أمانة العاصمة، صنعاء، عدن')
                            ->required()
                            ->maxLength(100)
                            ->autofocus(),

                        TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->placeholder('مثال: Sanaa, Aden')
                            ->required()
                            ->maxLength(100)
                            ->rules(['alpha_spaces'])
                            ->helperText('استخدم الحروف الإنجليزية فقط'),

                        TextInput::make('code')
                            ->label('رمز المحافظة')
                            ->placeholder('مثال: SNA, ADN, TAIZ')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_num'])
                            ->helperText('رمز قصير فريد (بالأحرف الكبيرة عادة)')
                            ->disabledOn('edit'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ═══════════════════════════════════════════════
                // القسم 2: الحالة والترتيب
                // ═══════════════════════════════════════════════
                Section::make('الحالة والترتيب')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('مفعّلة')
                            ->helperText('عند الإيقاف، لن تظهر المحافظة في التطبيق')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),

                        TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->helperText('الأرقام الأصغر تظهر أولاً')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}