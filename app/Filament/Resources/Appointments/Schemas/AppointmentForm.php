<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Appointment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات الموعد')
                    ->description('المعلومات الأساسية للموعد')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('citizen_id')
                                    ->label('المواطن')
                                    ->relationship(
                                        name: 'citizen',
                                        titleAttribute: 'full_name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المواطن',
                                        'exists' => 'المواطن المحدد غير صالح',
                                    ]),

                                Select::make('branch_id')
                                    ->label('الفرع')
                                    ->relationship(
                                        name: 'branch',
                                        titleAttribute: 'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الفرع',
                                        'exists' => 'الفرع المحدد غير صالح',
                                    ]),

                                Select::make('service_type')
                                    ->label('نوع الخدمة')
                                    ->options([
                                        'passport_new' => 'إصدار جواز سفر',
                                        'passport_renew' => 'تجديد جواز سفر',
                                        'passport_lost' => 'بدل فاقد لجواز السفر',
                                        'passport_damaged' => 'بدل تالف لجواز السفر',

                                        'national_id_new' => 'إصدار بطاقة شخصية',
                                        'national_id_renew' => 'تجديد بطاقة شخصية',
                                        'national_id_lost' => 'بدل فاقد للبطاقة الشخصية',
                                        'national_id_damaged' => 'بدل تالف للبطاقة الشخصية',

                                        'family_card_new' => 'إصدار بطاقة عائلية',
                                        'family_card_renew' => 'تجديد بطاقة عائلية',

                                        'birth_certificate' => 'إصدار شهادة ميلاد',
                                        'death_certificate' => 'شهادة وفاة',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->rules([
                                        function ($get, $record) {
                                            return function (string $attribute, $value, $fail) use ($get, $record) {

                                                $citizenId = $get('citizen_id');

                                                if (! $citizenId || ! $value) {
                                                    return;
                                                }

                                                $query = Appointment::query()
                                                    ->where('citizen_id', $citizenId)
                                                    ->where('service_type', $value)
                                                    ->whereIn('status', [
                                                        'pending',
                                                        'confirmed',
                                                    ]);

                                                if ($record) {
                                                    $query->whereKeyNot($record->getKey());
                                                }

                                                if ($query->exists()) {
                                                    $fail(
                                                        'هذا المواطن لديه موعد مسبق لنفس الخدمة وما زال قيد الانتظار أو مؤكدًا.'
                                                    );
                                                }
                                            };
                                        },
                                    ])
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار نوع الخدمة',
                                    ]),

                                DatePicker::make('appointment_date')
                                    ->label('تاريخ الموعد')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->format('Y-m-d')
                                    ->minDate(now()->startOfDay())
                                    ->validationMessages([
                                        'required' => 'تاريخ الموعد مطلوب',
                                        'date' => 'تاريخ الموعد غير صالح',
                                    ]),

                                TimePicker::make('appointment_time')
                                    ->label('وقت الموعد')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->validationMessages([
                                        'required' => 'وقت الموعد مطلوب',
                                    ]),

                                Select::make('status')
                                    ->label('حالة الموعد')
                                    ->options([
                                        'pending' => 'قيد الانتظار',
                                        'confirmed' => 'مؤكد',
                                        'attended' => 'تم الحضور',
                                        'cancelled' => 'ملغي',
                                        'no_show' => 'لم يحضر',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->native(false)
                                    ->disabled()
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'حالة الموعد مطلوبة',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات مرتبطة بالموعد')
                    ->icon('heroicon-o-information-circle')
                    ->schema([

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
