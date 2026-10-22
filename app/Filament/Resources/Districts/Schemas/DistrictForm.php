<?php

namespace App\Filament\Resources\Districts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ═══════════════════════════════════════════════
                // القسم 1: المحافظة التابعة لها
                // ═══════════════════════════════════════════════
                Section::make('المحافظة')
                    ->description('المديرية تتبع محافظة واحدة')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Select::make('governorate_id')
                            ->label('المحافظة')
                            ->relationship('governorate', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('اختر المحافظة')
                            ->helperText('ابحث بالاسم لتسهيل الوصول'),
                    ])
                    ->columnSpanFull(),

                // ═══════════════════════════════════════════════
                // القسم 2: البيانات الأساسية
                // ═══════════════════════════════════════════════
                Section::make('البيانات الأساسية')
                    ->description('الاسم والرمز التعريفي')
                    ->icon('heroicon-o-map')
                    ->schema([
                        TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->placeholder('مثال: صنعاء القديمة، المنصورة')
                            ->required()
                            ->maxLength(150)
                            ->autofocus(),

                        TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->placeholder('مثال: Old Sanaa, Mansoura')
                            ->required()
                            ->maxLength(150)
                            ->rules(['alpha_spaces']),

                        TextInput::make('code')
                            ->label('رمز المديرية (اختياري)')
                            ->placeholder('مثال: SNA-OLD')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('اتركه فارغاً إن لم يكن هناك رمز رسمي'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ═══════════════════════════════════════════════
                // القسم 3: الحالة والترتيب
                // ═══════════════════════════════════════════════
                Section::make('الحالة والترتيب')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('مفعّلة')
                            ->helperText('عند الإيقاف، لن تظهر المديرية في التطبيق')
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