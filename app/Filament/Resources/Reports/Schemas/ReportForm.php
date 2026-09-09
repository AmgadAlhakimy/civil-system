<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات التقرير')
                    ->description('المعلومات الأساسية للتقرير')
                    ->icon('heroicon-o-document-chart-bar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('اسم التقرير')
                                    ->placeholder('مثال: تقرير المواطنين')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(150)
                                    ->trim()
                                    ->validationMessages([
                                        'required' => 'اسم التقرير مطلوب',
                                        'min' => 'اسم التقرير يجب أن يحتوي على 3 أحرف على الأقل',
                                        'max' => 'اسم التقرير لا يمكن أن يتجاوز 150 حرف',
                                    ]),

                                Select::make('report_type')
                                    ->label('نوع التقرير')
                                    ->options([
                                        'citizens' => 'تقرير المواطنين',
                                        'birth_certificates' => 'تقرير شهادات الميلاد',
                                        'identity_cards' => 'تقرير البطاقات الشخصية',
                                        'family_cards' => 'تقرير البطاقات العائلية',
                                        'passports' => 'تقرير الجوازات',
                                        'appointments' => 'تقرير المواعيد',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->in([
                                        'citizens',
                                        'birth_certificates',
                                        'identity_cards',
                                        'family_cards',
                                        'passports',
                                        'appointments',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function ($set) {
                                        $set('gender', null);
                                        $set('marital_status', null);
                                        $set('from_date', null);
                                        $set('to_date', null);
                                        $set('status_filter', null);
                                    })
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار نوع التقرير',
                                        'in' => 'نوع التقرير المحدد غير صالح',
                                    ]),

                                Select::make('format')
                                    ->label('صيغة التقرير')
                                    ->options([
                                        'pdf' => 'PDF',
                                        'excel' => 'Excel',
                                    ])
                                    ->default('pdf')
                                    ->required()
                                    ->native(false)
                                    ->in([
                                        'pdf',
                                        'excel',
                                    ])
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار صيغة التقرير',
                                        'in' => 'صيغة التقرير المحددة غير صالحة',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('الفلاتر')
                    ->description('حدد معايير البحث التي تريد استخدامها في التقرير')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('gender')
                                    ->label('الجنس')
                                    ->options([
                                        'male' => 'ذكر',
                                        'female' => 'أنثى',
                                    ])
                                    ->placeholder('جميع المواطنين')
                                    ->native(false)
                                    ->in([
                                        'male',
                                        'female',
                                    ])
                                    ->validationMessages([
                                        'in' => 'قيمة الجنس المحددة غير صالحة',
                                    ]),

                                Select::make('marital_status')
                                    ->label('الحالة الاجتماعية')
                                    ->options([
                                        'single' => 'أعزب',
                                        'married' => 'متزوج',
                                        'divorced' => 'مطلق',
                                        'widowed' => 'أرمل',
                                    ])
                                    ->placeholder('جميع الحالات')
                                    ->native(false)
                                    ->in([
                                        'single',
                                        'married',
                                        'divorced',
                                        'widowed',
                                    ])
                                    ->validationMessages([
                                        'in' => 'الحالة الاجتماعية المحددة غير صالحة',
                                    ]),
                            ])
                            ->visible(fn ($get) => $get('report_type') === 'citizens'),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('from_date')
                                    ->label('من تاريخ')
                                    ->placeholder('اختر تاريخ البداية')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->prefixIcon('heroicon-o-calendar-days')

                                    // لا يسمح بتاريخ مستقبلي
                                    ->maxDate(
                                        fn ($get) => $get('report_type') === 'appointments'
                                            ? null
                                            : now()->toDateString()
                                    )

                                    // عند اختيار تاريخ البداية،
                                    // يتم وضع اليوم الحالي تلقائيًا في تاريخ النهاية
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if (
                                            blank($state) ||
                                            $get('report_type') === 'appointments'
                                        ) {
                                            return;
                                        }

                                        $currentToDate = $get('to_date');
                                        $today = now()->toDateString();

                                        // إذا لم يتم اختيار تاريخ نهاية،
                                        // نضع اليوم الحالي تلقائيًا.
                                        if (blank($currentToDate)) {
                                            $set('to_date', $today);

                                            return;
                                        }

                                        // إذا كان تاريخ النهاية الحالي
                                        // قبل تاريخ البداية، نصححه إلى اليوم الحالي.
                                        if ($currentToDate < $state) {
                                            $set(
                                                'to_date',
                                                $state > $today
                                                    ? $today
                                                    : $today
                                            );
                                        }
                                    })
                                    ->closeOnDateSelection()
                                    ->rule(function ($get) {
                                        return function (
                                            string $attribute,
                                                   $value,
                                            \Closure $fail
                                        ) use ($get) {
                                            if (
                                                filled($value) &&
                                                filled($get('to_date')) &&
                                                $value > $get('to_date')
                                            ) {
                                                $fail(
                                                    'تاريخ البداية يجب أن يكون قبل أو مساويًا لتاريخ النهاية.'
                                                );
                                            }

                                            if (
                                                $get('report_type') !== 'appointments' &&
                                                filled($value) &&
                                                $value > now()->toDateString()
                                            ) {
                                                $fail(
                                                    'لا يمكن اختيار تاريخ مستقبلي لهذا النوع من التقارير.'
                                                );
                                            }
                                        };
                                    })
                                    ->validationMessages([
                                        'date' => 'يرجى إدخال تاريخ بداية صحيح',
                                    ]),

                                DatePicker::make('to_date')
                                    ->label('إلى تاريخ')
                                    ->placeholder('اختر تاريخ النهاية')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->prefixIcon('heroicon-o-calendar-days')

                                    // يجب ألا يكون تاريخ النهاية قبل تاريخ البداية
                                    ->minDate(
                                        fn ($get) => $get('from_date')
                                    )

                                    // اليوم الحالي هو آخر تاريخ مسموح
                                    // للتقارير العادية.
                                    // المواعيد يمكن أن تحتوي على تاريخ مستقبلي.
                                    ->maxDate(
                                        fn ($get) => $get('report_type') === 'appointments'
                                            ? null
                                            : now()->toDateString()
                                    )

                                    ->live()
                                    ->closeOnDateSelection()
                                    ->rule(function ($get) {
                                        return function (
                                            string $attribute,
                                                   $value,
                                            \Closure $fail
                                        ) use ($get) {
                                            if (
                                                filled($value) &&
                                                filled($get('from_date')) &&
                                                $value < $get('from_date')
                                            ) {
                                                $fail(
                                                    'تاريخ النهاية يجب أن يكون بعد أو مساويًا لتاريخ البداية.'
                                                );
                                            }

                                            if (
                                                $get('report_type') !== 'appointments' &&
                                                filled($value) &&
                                                $value > now()->toDateString()
                                            ) {
                                                $fail(
                                                    'لا يمكن اختيار تاريخ مستقبلي لهذا النوع من التقارير.'
                                                );
                                            }
                                        };
                                    })
                                    ->validationMessages([
                                        'date' => 'يرجى إدخال تاريخ نهاية صحيح',
                                    ]),
                            ])
                            ->visible(fn ($get) => in_array(
                                $get('report_type'),
                                [
                                    'citizens',
                                    'birth_certificates',
                                    'identity_cards',
                                    'family_cards',
                                    'passports',
                                    'appointments',
                                ],
                                true
                            )),

                        Select::make('status_filter')
                            ->label('حالة الموعد')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'confirmed' => 'مؤكد',
                                'attended' => 'تم الحضور',
                                'cancelled' => 'ملغي',
                                'no_show' => 'لم يحضر',
                            ])
                            ->placeholder('جميع الحالات')
                            ->native(false)
                            ->in([
                                'pending',
                                'confirmed',
                                'attended',
                                'cancelled',
                                'no_show',
                            ])
                            ->visible(
                                fn ($get) => $get('report_type') === 'appointments'
                            )
                            ->validationMessages([
                                'in' => 'حالة الموعد المحددة غير صالحة',
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
