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
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class CitizenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('البيانات الشخصية')
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
                                                ->required()
                                                ->unique(
                                                    table: 'citizens',
                                                    column: 'national_id',
                                                    ignoreRecord: true,
                                                )
                                                ->rules([
                                                    'digits:11',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'حقل الرقم الوطني مطلوب',
                                                    'digits' => 'يجب أن يكون الرقم الوطني مكوناً من 11 رقماً',
                                                    'unique' => 'هذا الرقم الوطني مسجل مسبقاً لمواطن آخر',
                                                ]),

                                            TextInput::make('first_name')
                                                ->label('الاسم الأول')
                                                ->required()
                                                ->minLength(2)
                                                ->maxLength(50)
                                                ->rules([
                                                    'string',
                                                    'regex:/^(?=.*\p{Arabic})[\p{Arabic}\s]+$/u',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'حقل الاسم الأول مطلوب',
                                                    'min' => 'يجب ألا يقل الاسم الأول عن حرفين',
                                                    'max' => 'يجب ألا يتجاوز الاسم الأول 50 حرفاً',
                                                    'regex' => 'يجب أن يحتوي الاسم الأول على حروف عربية فقط',
                                                ]),

                                            TextInput::make('father_name')
                                                ->label('اسم الأب')
                                                ->required()
                                                ->minLength(2)
                                                ->maxLength(50)
                                                ->rules([
                                                    'string',
                                                    'regex:/^(?=.*\p{Arabic})[\p{Arabic}\s]+$/u',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'حقل اسم الأب مطلوب',
                                                    'min' => 'يجب ألا يقل اسم الأب عن حرفين',
                                                    'max' => 'يجب ألا يتجاوز اسم الأب 50 حرفاً',
                                                    'regex' => 'يجب أن يحتوي اسم الأب على حروف عربية فقط',
                                                ]),

                                            TextInput::make('middle_name')
                                                ->label('اسم الجد')
                                                ->required()
                                                ->minLength(2)
                                                ->maxLength(50)
                                                ->rules([
                                                    'string',
                                                    'regex:/^(?=.*\p{Arabic})[\p{Arabic}\s]+$/u',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'حقل اسم الجد مطلوب',
                                                    'min' => 'يجب ألا يقل اسم الجد عن حرفين',
                                                    'max' => 'يجب ألا يتجاوز اسم الجد 50 حرفاً',
                                                    'regex' => 'يجب أن يحتوي اسم الجد على حروف عربية فقط',
                                                ]),

                                            TextInput::make('last_name')
                                                ->label('اللقب / العائلة')
                                                ->required()
                                                ->minLength(2)
                                                ->maxLength(50)
                                                ->rules([
                                                    'string',
                                                    'regex:/^(?=.*\p{Arabic})[\p{Arabic}\s]+$/u',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'حقل اللقب مطلوب',
                                                    'min' => 'يجب ألا يقل اللقب عن حرفين',
                                                    'max' => 'يجب ألا يتجاوز اللقب 50 حرفاً',
                                                    'regex' => 'يجب أن يحتوي اللقب على حروف عربية فقط',
                                                ]),

                                            TextInput::make('mother_name')
                                                ->label('اسم الأم')
                                                ->required()
                                                ->minLength(2)
                                                ->maxLength(50)
                                                ->rules([
                                                    'string',
                                                    'regex:/^(?=.*\p{Arabic})[\p{Arabic}\s]+$/u',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'حقل اسم الأم مطلوب',
                                                    'min' => 'يجب ألا يقل اسم الأم عن حرفين',
                                                    'max' => 'يجب ألا يتجاوز اسم الأم 50 حرفاً',
                                                    'regex' => 'يجب أن يحتوي اسم الأم على حروف عربية فقط',
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
                                        ->disk('local')
                                        ->directory('citizens/photos')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'الصورة الشخصية مطلوبة',
                                            'image' => 'يجب أن يكون الملف صورة صحيحة',
                                            'max' => 'يجب ألا يتجاوز حجم الصورة 2 ميجابايت',
                                        ])
                                        ->columnSpanFull(),
                                ])
                                ->columnSpanFull(),
                        ]),

                    Step::make('بيانات الميلاد')
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
                                                ->maxDate(today())
                                                ->rules([
                                                    'before_or_equal:today',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'يرجى تحديد تاريخ الميلاد',
                                                    'before_or_equal' => 'لا يمكن أن يكون تاريخ الميلاد في المستقبل',
                                                ]),

                                            TextInput::make('birth_place')
                                                ->label('مكان الميلاد')
                                                ->required()
                                                ->minLength(2)
                                                ->maxLength(255)
                                                ->rules([
                                                    'string',
                                                    'regex:/^(?=.*\p{Arabic})[\p{Arabic}\s]+$/u',
                                                ])
                                                ->validationMessages([
                                                    'required' => 'حقل مكان الميلاد مطلوب',
                                                    'min' => 'يجب ألا يقل مكان الميلاد عن حرفين',
                                                    'max' => 'يجب ألا يتجاوز مكان الميلاد 255 حرفاً',
                                                    'regex' => 'يجب أن يحتوي مكان الميلاد على حروف عربية فقط',
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
                    Step::make('الحالة ')
                        ->icon('heroicon-o-shield-check')
                        ->visible(fn (string $operation): bool => $operation === 'edit')
                        ->schema([
                            Section::make('حالة الحساب')
                                ->description('تحديد حالة حساب المواطن')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('حساب المواطن نشط')
                                        ->default(false)
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->inline(false),
                                ])
                                ->columnSpanFull(),
                        ]),

                    Step::make('الاتصال والعنوان')
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
                                                ->unique(
                                                    table: 'citizens',
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
                                                    'max' => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرفاً',
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
                                        ->maxLength(500)
                                        ->validationMessages([
                                            'required' => 'حقل العنوان مطلوب',
                                            'min' => 'يجب أن يكون العنوان تفصيلياً أكثر',
                                            'max' => 'يجب ألا يتجاوز العنوان 500 حرف',
                                        ])
                                        ->columnSpanFull(),
                                ])
                                ->columnSpanFull(),
                        ]),

                    Step::make('البيانات الحيوية')
                        ->description('اختياري')
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
                                        ->disk('local')
                                        ->directory('citizens/faces')
                                        ->helperText('رفع بصمة الوجه اختياري ويمكن إنهاء تسجيل المواطن بدون رفعها.')
                                        ->validationMessages([
                                            'image' => 'يجب أن يكون الملف صورة صحيحة',
                                            'max' => 'يجب ألا يتجاوز حجم الصورة 3 ميجابايت',
                                        ])
                                        ->columnSpanFull(),
                                ])
                                ->columnSpanFull(),
                        ]),
                ])
                    ->submitAction(
                        new HtmlString(
                            '<button type="submit" class="fi-btn fi-btn-color-primary fi-btn-size-md" style="background-color: var(--app-primary) !important; color: #ffffff !important;">
                                        إضافة المواطن
                                     </button>'
                        )
                    )
                    ->columnSpanFull(),
            ]);
    }
}
