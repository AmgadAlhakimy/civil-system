<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                                    ->unique(
                                        table: 'branches',
                                        column: 'code',
                                        ignoreRecord: true,
                                    )
                                    ->minLength(2)
                                    ->maxLength(20)
                                    ->rules([
                                        'string',
                                        'regex:/^[A-Za-z0-9_-]+$/',
                                    ])
                                    ->validationMessages([
                                        'required' => 'حقل رمز الفرع مطلوب',
                                        'unique' => 'رمز الفرع مستخدم مسبقاً',
                                        'min' => 'يجب ألا يقل رمز الفرع عن حرفين',
                                        'max' => 'يجب ألا يتجاوز رمز الفرع 20 حرفاً',
                                        'regex' => 'رمز الفرع يجب أن يحتوي على أحرف إنجليزية أو أرقام أو شرطة فقط',
                                    ]),

                                TextInput::make('name')
                                    ->label('اسم الفرع')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(100)
                                    ->rules([
                                        'string',
                                        'regex:/^[\p{Arabic}\s]+$/u',
                                    ])
                                    ->validationMessages([
                                        'required' => 'حقل اسم الفرع مطلوب',
                                        'min' => 'يجب ألا يقل اسم الفرع عن 3 أحرف',
                                        'max' => 'يجب ألا يتجاوز اسم الفرع 100 حرف',
                                        'regex' => 'يجب أن يحتوي اسم الفرع على حروف عربية فقط',
                                    ]),

                                Textarea::make('address')
                                    ->label('عنوان الفرع')
                                    ->required()
                                    ->minLength(5)
                                    ->maxLength(500)
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->validationMessages([
                                        'required' => 'حقل عنوان الفرع مطلوب',
                                        'min' => 'يجب ألا يقل عنوان الفرع عن 5 أحرف',
                                        'max' => 'يجب ألا يتجاوز عنوان الفرع 500 حرف',
                                    ]),

                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->required()
                                    ->unique(
                                        table: 'branches',
                                        column: 'phone',
                                        ignoreRecord: true,
                                    )
                                    ->rules([
                                        'digits:9',
                                        'regex:/^7[0-9]{8}$/',
                                    ])
                                    ->validationMessages([
                                        'required' => 'حقل رقم الهاتف مطلوب',
                                        'unique' => 'رقم الهاتف هذا مستخدم مسبقاً',
                                        'digits' => 'يجب أن يتكون رقم الهاتف من 9 أرقام',
                                        'regex' => 'رقم الهاتف غير صالح (يجب أن يبدأ بـ 7 ويتكون من 9 أرقام)',
                                    ]),

                                TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->nullable()
                                    ->unique(
                                        table: 'branches',
                                        column: 'email',
                                        ignoreRecord: true,
                                    )
                                    ->maxLength(100)
                                    ->validationMessages([
                                        'email' => 'صيغة البريد الإلكتروني غير صحيحة',
                                        'unique' => 'البريد الإلكتروني مستخدم مسبقاً',
                                        'max' => 'يجب ألا يتجاوز البريد الإلكتروني 100 حرف',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('إدارة الفرع')
                    ->description('معلومات مدير الفرع وحالة الفرع')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextInput::make('manager_name')
                                    ->label('اسم مدير الفرع')
                                    ->nullable()
                                    ->minLength(3)
                                    ->maxLength(100)
                                    ->rules([
                                        'string',
                                        'regex:/^[\p{Arabic}\s]+$/u',
                                    ])
                                    ->validationMessages([
                                        'min' => 'يجب ألا يقل اسم مدير الفرع عن 3 أحرف',
                                        'max' => 'يجب ألا يتجاوز اسم مدير الفرع 100 حرف',
                                        'regex' => 'يجب أن يحتوي اسم مدير الفرع على حروف عربية فقط',
                                    ])
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label('الفرع نشط')
                                    ->default(true)
                                    ->required()
                                    ->inline(false)
                                    ->validationMessages([
                                        'required' => 'يرجى تحديد حالة الفرع',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
