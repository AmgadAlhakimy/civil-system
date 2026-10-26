<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Models\Governorate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الفرع')
                    ->description('المعلومات الأساسية للفرع')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->label('رمز الفرع')
                                    ->required()
                                    ->placeholder('SNA-01')
                                    ->unique(ignoreRecord: true)
                                    ->minLength(3)
                                    ->maxLength(20)
                                    ->regex('/^[A-Za-z0-9-]+$/')
                                    ->dehydrateStateUsing(
                                        fn (?string $state): ?string => $state !== null
                                            ? strtoupper(trim($state))
                                            : null
                                    )
                                    ->validationMessages([
                                        'required' => 'حقل رمز الفرع مطلوب',
                                        'unique' => 'رمز الفرع مستخدم مسبقاً',
                                        'min' => 'يجب ألا يقل رمز الفرع عن 3 أحرف',
                                        'max' => 'يجب ألا يتجاوز رمز الفرع 20 حرفاً',
                                        'regex' => 'الرمز يجب أن يحتوي على أحرف إنجليزية أو أرقام أو شرطة فقط',
                                    ]),

                                TextInput::make('name')
                                    ->label('اسم الفرع')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(100)
                                    ->dehydrateStateUsing(
                                        fn (?string $state): ?string => $state !== null
                                            ? trim(preg_replace('/\s+/u', ' ', $state))
                                            : null
                                    )
                                    ->validationMessages([
                                        'required' => 'حقل اسم الفرع مطلوب',
                                        'min' => 'يجب ألا يقل اسم الفرع عن 3 أحرف',
                                        'max' => 'يجب ألا يتجاوز اسم الفرع 100 حرف',
                                    ]),

                                Select::make('governorate_id')
                                    ->label('المحافظة')
                                    ->options(
                                        fn (): array => Governorate::query()
                                            ->where('is_active', true)
                                            ->orderBy('name_ar')
                                            ->pluck('name_ar', 'id')
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->placeholder('اختر المحافظة')
                                    ->columnSpanFull()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المحافظة التابعة للفرع',
                                    ]),

                                Textarea::make('address')
                                    ->label('عنوان الفرع')
                                    ->required()
                                    ->minLength(5)
                                    ->maxLength(500)
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->dehydrateStateUsing(
                                        fn (?string $state): ?string => $state !== null
                                            ? trim(preg_replace('/\s+/u', ' ', $state))
                                            : null
                                    )
                                    ->validationMessages([
                                        'required' => 'حقل عنوان الفرع مطلوب',
                                        'min' => 'يجب ألا يقل عنوان الفرع عن 5 أحرف',
                                        'max' => 'يجب ألا يتجاوز عنوان الفرع 500 حرف',
                                    ]),

                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->required()
                                    ->minLength(9)
                                    ->maxLength(9)
                                    ->unique(ignoreRecord: true)
                                    ->rules([
                                        'digits:9',
                                        'regex:/^7[0-9]{8}$/',
                                    ])
                                    ->inputMode('numeric')
                                    ->validationMessages([
                                        'required' => 'حقل رقم الهاتف مطلوب',
                                        'digits' => 'يجب أن يتكون رقم الهاتف من 9 أرقام',
                                        'regex' => 'رقم الهاتف يجب أن يبدأ بـ 7',
                                        'unique' => 'رقم الهاتف مستخدم مسبقاً',
                                    ]),

                                TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->nullable()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100)
                                    ->validationMessages([
                                        'email' => 'صيغة البريد الإلكتروني غير صحيحة',
                                        'unique' => 'البريد الإلكتروني مستخدم مسبقاً',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('إدارة الفرع')
                    ->description('حالة الفرع والمدير المسؤول')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('الفرع نشط')
                                    ->default(true)
                                    ->required()
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->columnSpan(1),

                                Select::make('manager_id')
                                    ->label('مدير الفرع')
                                    ->placeholder('اختر مدير الفرع')
                                    ->relationship(
                                        name: 'manager',
                                        titleAttribute: 'full_name',
                                        modifyQueryUsing: fn (Builder $query) => $query
                                            ->where('account_type', 'employee')
                                            ->orderBy('full_name')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
