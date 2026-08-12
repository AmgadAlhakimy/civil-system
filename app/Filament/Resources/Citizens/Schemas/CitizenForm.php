<?php

namespace App\Filament\Resources\Citizens\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class CitizenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('بيانات المواطن')
                    ->tabs([
                        Tabs\Tab::make('البيانات الشخصية')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('بيانات الهوية')
                                    ->description('المعلومات الأساسية للمواطن')
                                    ->icon('heroicon-o-identification')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([


                                                TextInput::make('national_id')
                                                    ->label('الرقم الوطني')
                                                    ->numeric()
                                                    ->maxLength(11)
                                                    ->required()
                                                    ->unique(
                                                        table: 'citizens',
                                                        column: 'national_id',
                                                        ignoreRecord: true,
                                                    )
                                                    ->rules([
                                                        'numeric',
                                                        'digits:11',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل الرقم الوطني مطلوب',
                                                        'numeric' => 'يجب أن يتكون الرقم الوطني من أرقام فقط',
                                                        'digits' => 'يجب أن يكون الرقم الوطني مكوناً من 11 رقماً',
                                                        'unique' => 'هذا الرقم الوطني مسجل مسبقاً لمواطن آخر',
                                                    ]),


                                                TextInput::make('first_name')
                                                    ->label('الاسم الأول')
                                                    ->required()
                                                    ->minLength(2)
                                                    ->rules([
                                                        'string',
                                                        'regex:/^[\p{Arabic}\s]+$/u',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل الاسم الأول مطلوب',
                                                        'regex' => 'يجب أن يحتوي الاسم على حروف عربية فقط',
                                                        'min' => 'يجب ألا يقل الاسم عن حرفين',
                                                    ]),

                                                TextInput::make('father_name')
                                                    ->label('اسم الأب')
                                                    ->required()
                                                    ->minLength(2)
                                                    ->rules([
                                                        'string',
                                                        'regex:/^[\p{Arabic}\s]+$/u',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل اسم الأب مطلوب',
                                                        'regex' => 'يجب أن يحتوي اسم الأب على حروف عربية فقط',
                                                        'min' => 'يجب ألا يقل اسم الأب عن حرفين',
                                                    ]),

                                                TextInput::make('middle_name')
                                                    ->label('اسم الجد')
                                                    ->required()
                                                    ->minLength(2)
                                                    ->rules([
                                                        'string',
                                                        'regex:/^[\p{Arabic}\s]+$/u',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل اسم الجد مطلوب',
                                                        'regex' => 'يجب أن يحتوي اسم الجد على حروف عربية فقط',
                                                        'min' => 'يجب ألا يقل الاسم عن حرفين',
                                                    ]),

                                                TextInput::make('last_name')
                                                    ->label('اللقب / العائلة')
                                                    ->required()
                                                    ->minLength(2)
                                                    ->rules([
                                                        'string',
                                                        'regex:/^[\p{Arabic}\s]+$/u',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل اللقب مطلوب',
                                                        'regex' => 'يجب أن يحتوي اللقب على حروف عربية فقط',
                                                        'min' => 'يجب ألا يقل اللقب عن حرفين',
                                                    ]),

                                                TextInput::make('mother_name')
                                                    ->label('اسم الأم')
                                                    ->required()
                                                    ->minLength(2)
                                                    ->rules([
                                                        'string',
                                                        'regex:/^[\p{Arabic}\s]+$/u',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل اسم الأم مطلوب',
                                                        'regex' => 'يجب أن يحتوي اسم الأم على حروف عربية فقط',
                                                        'min' => 'يجب ألا يقل الاسم عن حرفين',
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('الصورة الشخصية')
                                    ->description('صورة واضحة وحديثة للمواطن')
                                    ->icon('heroicon-o-camera')
                                    ->schema([
                                        FileUpload::make('photo')
                                            ->label('الصورة الشخصية')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('220')
                                            ->maxSize(2048)
                                            ->directory('citizens/photos')
                                            ->required()
                                            ->validationMessages([
                                                'required' => 'الصورة الشخصية مطلوبة',
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('بيانات الميلاد')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Section::make('معلومات الميلاد')
                                    ->description('بيانات الميلاد والجنس')
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                DatePicker::make('birth_date')
                                                    ->label('تاريخ الميلاد')
                                                    ->required()
                                                    ->native(false)
                                                    ->maxDate(now())
                                                    ->rules([
                                                        'before:today',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'يرجى تحديد تاريخ الميلاد',
                                                        'before' => 'لا يمكن أن يكون تاريخ الميلاد في المستقبل',
                                                    ]),

                                                TextInput::make('birth_place')
                                                    ->label('مكان الميلاد')
                                                    ->required()
                                                    ->minLength(2)
                                                    ->rules([
                                                        'string',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل مكان الميلاد مطلوب',
                                                        'min' => 'يجب أن يكون مكان الميلاد صحيحاً',
                                                    ]),

                                                Select::make('gender')
                                                    ->label('الجنس')
                                                    ->options([
                                                        'male' => 'ذكر',
                                                        'female' => 'أنثى',
                                                    ])
                                                    ->required()
                                                    ->native(false)
                                                    ->validationMessages([
                                                        'required' => 'يرجى اختيار الجنس',
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('الحالة الاجتماعية')
                            ->icon('heroicon-o-heart')
                            ->schema([
                                Section::make('المعلومات الاجتماعية والمهنية')
                                    ->description('الحالة الاجتماعية والمهنة الحالية')
                                    ->icon('heroicon-o-briefcase')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('marital_status')
                                                    ->label('الحالة الاجتماعية')
                                                    ->options([
                                                        'single' => 'أعزب',
                                                        'married' => 'متزوج',
                                                        'divorced' => 'مطلق',
                                                        'widowed' => 'أرمل',
                                                    ])
                                                    ->required()
                                                    ->native(false)
                                                    ->validationMessages([
                                                        'required' => 'يرجى اختيار الحالة الاجتماعية',
                                                    ]),

                                                TextInput::make('occupation')
                                                    ->label('المهنة')
                                                    ->placeholder('مثال: مهندس، مدرس، موظف...')
                                                    ->maxLength(255),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('الاتصال والعنوان')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('معلومات الاتصال')
                                    ->description('بيانات التواصل مع المواطن')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('phone')
                                                    ->label('رقم الهاتف')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(9)
                                                    ->unique(
                                                        table: 'citizens',
                                                        column: 'phone',
                                                        ignoreRecord: true,
                                                    )
                                                    ->rules([
                                                        'regex:/^7[0-9]{8}$/',
                                                    ])
                                                    ->validationMessages([
                                                        'required' => 'حقل رقم الهاتف مطلوب',
                                                        'regex' => 'رقم الهاتف غير صالح (يجب أن يبدأ بـ 7 ويتكون من 9 أرقام)',
                                                        'unique' => 'رقم الهاتف هذا مسجل مسبقاً لمواطن آخر',
                                                    ]),

                                                TextInput::make('email')
                                                    ->label('البريد الإلكتروني')
                                                    ->email()
                                                    ->maxLength(255)
                                                    ->unique(
                                                        table: 'citizens',
                                                        column: 'email',
                                                        ignoreRecord: true,
                                                    )
                                                    ->rules([
                                                        'nullable',
                                                        'email',
                                                    ])
                                                    ->validationMessages([
                                                        'email' => 'صيغة البريد الإلكتروني غير صحيحة',
                                                        'unique' => 'البريد الإلكتروني مسجل مسبقاً',
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('العنوان')
                                    ->description('عنوان السكن الحالي')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
                                        Textarea::make('address')
                                            ->label('العنوان التفصيلي')
                                            ->required()
                                            ->rows(4)
                                            ->minLength(5)
                                            ->validationMessages([
                                                'required' => 'حقل العنوان مطلوب',
                                                'min' => 'يجب أن يكون العنوان تفصيلياً أكثر',
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('البيانات الحيوية')
                            ->icon('heroicon-o-finger-print')
                            ->schema([
                                Section::make('البيانات الحيوية')
                                    ->description('البيانات الحيوية الخاصة بالمواطن')
                                    ->icon('heroicon-o-finger-print')
                                    ->schema([
                                        FileUpload::make('face_data')
                                            ->label('بصمة الوجه')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('220')
                                            ->maxSize(3072)
                                            ->directory('citizens/faces')
                                            ->helperText('ارفع صورة واضحة للوجه لاستخدامها في التحقق الحيوي.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('الحالة والنظام')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('حالة الحساب')
                                    ->description('تحديد حالة حساب المواطن')
                                    ->icon('heroicon-o-check-circle')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('حساب المواطن نشط')
                                            ->default(true)
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
