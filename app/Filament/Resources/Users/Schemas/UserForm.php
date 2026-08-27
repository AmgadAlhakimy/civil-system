<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المستخدم')
                    ->description('المعلومات الأساسية للمستخدم')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('اسم المستخدم')
                                    ->required()
                                    ->unique(
                                        table: 'users',
                                        column: 'name',
                                        ignoreRecord: true,
                                    )
                                    ->validationMessages([
                                        'required' => 'حقل اسم المستخدم مطلوب',
                                        'unique' => 'اسم المستخدم مستخدم مسبقاً',
                                    ]),

                                TextInput::make('full_name')
                                    ->label('الاسم الكامل')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255)
                                    ->rules([
                                        'string',
                                        'regex:/^[\p{Arabic}\s]+$/u',
                                    ])
                                    ->validationMessages([
                                        'required' => 'حقل الاسم الكامل مطلوب',
                                        'min' => 'يجب ألا يقل الاسم الكامل عن 3 أحرف',
                                        'regex' => 'يجب أن يحتوي الاسم الكامل على حروف عربية فقط',
                                    ]),

                                TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->required()
                                    ->unique(
                                        table: 'users',
                                        column: 'email',
                                        ignoreRecord: true,
                                    )
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'حقل البريد الإلكتروني مطلوب',
                                        'email' => 'صيغة البريد الإلكتروني غير صحيحة',
                                        'unique' => 'البريد الإلكتروني مستخدم مسبقاً',
                                    ]),

                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->required()
                                    ->unique(
                                        table: 'users',
                                        column: 'phone',
                                        ignoreRecord: true,
                                    )
                                    ->rules([
                                        'digits:9',
                                        'regex:/^7[0-9]{8}$/',
                                    ])
                                    ->validationMessages([
                                        'required' => 'حقل رقم الهاتف مطلوب',
                                        'digits' => 'يجب أن يتكون رقم الهاتف من 9 أرقام',
                                        'regex' => 'رقم الهاتف غير صالح (يجب أن يبدأ بـ 7 ويتكون من 9 أرقام)',
                                        'unique' => 'رقم الهاتف هذا مستخدم مسبقاً',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات العمل')
                    ->description('الفرع والدور وحالة المستخدم')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('branch_id')
                                    ->label('الفرع')
                                    ->relationship('branch', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الفرع',
                                    ]),

                                Select::make('status')
                                    ->label('حالة المستخدم')
                                    ->options([
                                        'active' => 'نشط',
                                        'inactive' => 'غير نشط',
                                        'blocked' => 'محظور',
                                    ])
                                    ->default('active')
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى تحديد حالة المستخدم',
                                    ]),

                                Select::make('roles')
                                    ->label('الدور')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار دور المستخدم',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('الأمان')
                    ->description('إعداد كلمة المرور الخاصة بالمستخدم')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('كلمة المرور')
                                    ->password()
                                    ->revealable()
                                    ->required(
                                        fn (string $operation): bool => $operation === 'create'
                                    )
                                    ->dehydrated(
                                        fn (?string $state): bool => filled($state)
                                    )
                                    ->rules([
                                        'min:8',
                                    ])
                                    ->same('password_confirmation')
                                    ->validationMessages([
                                        'required' => 'حقل كلمة المرور مطلوب',
                                        'min' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل',
                                        'same' => 'كلمة المرور وتأكيدها غير متطابقين',
                                    ]),

                                TextInput::make('password_confirmation')
                                    ->label('تأكيد كلمة المرور')
                                    ->password()
                                    ->revealable()
                                    ->required(
                                        fn (string $operation): bool => $operation === 'create'
                                    )
                                    ->dehydrated(false)
                                    ->same('password')
                                    ->validationMessages([
                                        'required' => 'حقل تأكيد كلمة المرور مطلوب',
                                        'same' => 'كلمة المرور وتأكيدها غير متطابقين',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('الصورة الشخصية')
                    ->description('الصورة الشخصية للمستخدم')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        FileUpload::make('profile_photo')
                            ->label('الصورة الشخصية')
                            ->image()
                            ->disk('local')
                            ->directory('users/profile-photos')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
