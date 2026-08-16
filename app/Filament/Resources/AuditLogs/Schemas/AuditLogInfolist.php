<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات عملية التدقيق')
                    ->description('المعلومات الأساسية للعملية المسجلة في النظام')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                            'xl' => 4,
                        ])
                            ->schema([

                                TextEntry::make('causer.name')
                                    ->label('المستخدم')
                                    ->placeholder('النظام')
                                    ->weight('bold')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('user_role')
                                    ->label('دور المستخدم')
                                    ->placeholder('غير محدد')
                                    ->badge()
                                    ->icon('heroicon-o-shield-check'),

                                TextEntry::make('event')
                                    ->label('العملية')
                                    ->badge()
                                    ->color(
                                        fn (?string $state): string => match ($state) {
                                            'created' => 'success',
                                            'updated' => 'warning',
                                            'deleted' => 'danger',
                                            'restored' => 'info',
                                            default => 'gray',
                                        }
                                    )
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'created' => 'إضافة',
                                            'updated' => 'تعديل',
                                            'deleted' => 'حذف',
                                            'restored' => 'استعادة',
                                            default => $state ?? 'غير محدد',
                                        }
                                    )
                                    ->icon('heroicon-o-bolt'),

                                TextEntry::make('created_at')
                                    ->label('تاريخ العملية')
                                    ->dateTime('Y-m-d H:i:s')
                                    ->weight('bold')
                                    ->icon('heroicon-o-clock'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('السجل المتأثر')
                    ->description('السجل الذي تمت عليه العملية')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('subject_name')
                                    ->label('السجل')
                                    ->state(fn ($record): string => self::getSubjectName($record))
                                    ->weight('bold')
                                    ->size('lg')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('subject_type')
                                    ->label('نوع السجل')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'App\\Models\\Citizen' => 'مواطن',
                                            'App\\Models\\Passport' => 'جواز سفر',
                                            'App\\Models\\FamilyCard' => 'بطاقة عائلية',
                                            'App\\Models\\FamilyMember' => 'فرد أسرة',
                                            'App\\Models\\IdentityCard' => 'بطاقة شخصية',
                                            'App\\Models\\BirthCertificate' => 'شهادة ميلاد',
                                            'App\\Models\\DeathCertificate' => 'شهادة وفاة',
                                            default => $state
                                                ? class_basename($state)
                                                : 'غير محدد',
                                        }
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-tag'),

                                TextEntry::make('subject_id')
                                    ->label('معرف السجل')
                                    ->placeholder('غير متوفر')
                                    ->copyable()
                                    ->copyMessage('تم نسخ معرف السجل')
                                    ->icon('heroicon-o-identification'),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('تفاصيل التغييرات')
                    ->description('مقارنة البيانات قبل وبعد تنفيذ العملية')
                    ->icon('heroicon-o-arrows-right-left')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 3,
                        ])
                            ->schema([
                                TextEntry::make('changes_field_header')
                                    ->hiddenLabel()
                                    ->default('الحقل')
                                    ->weight('bold'),

                                TextEntry::make('changes_old_header')
                                    ->hiddenLabel()
                                    ->default('القيمة السابقة')
                                    ->weight('bold')
                                    ->color('danger'),

                                TextEntry::make('changes_new_header')
                                    ->hiddenLabel()
                                    ->default('القيمة الجديدة')
                                    ->weight('bold')
                                    ->color('success'),
                            ]),

                        RepeatableEntry::make('changes_rows')
                            ->hiddenLabel()
                            ->contained(false)
                            ->state(function ($record): array {
                                $properties = $record->properties;

                                if ($properties instanceof \Illuminate\Support\Collection) {
                                    $properties = $properties->toArray();
                                }

                                $old = $properties['old'] ?? [];
                                $new = $properties['attributes'] ?? [];

                                $fields = array_unique(
                                    array_merge(
                                        array_keys($old),
                                        array_keys($new)
                                    )
                                );

                                $fields = array_filter(
                                    $fields,
                                    fn ($field) => $field !== 'updated_at'
                                );

                                return collect($fields)
                                    ->map(
                                        fn ($field): array => [
                                            'field' => self::fieldLabel($field),
                                            'old' => self::translateValue($old[$field] ?? null),
                                            'new' => self::translateValue($new[$field] ?? null),
                                        ]
                                    )
                                    ->values()
                                    ->all();
                            })
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        TextEntry::make('field')
                                            ->hiddenLabel()
                                            ->weight('bold'),

                                        TextEntry::make('old')
                                            ->hiddenLabel()
                                            ->color('danger'),

                                        TextEntry::make('new')
                                            ->hiddenLabel()
                                            ->color('success'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('معلومات جلسة المستخدم')
                    ->description('بيانات الاتصال والبيئة التي تم من خلالها تنفيذ العملية')
                    ->icon('heroicon-o-computer-desktop')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                            'xl' => 4,
                        ])
                            ->schema([

                                TextEntry::make('ip_address')
                                    ->label('عنوان IP')
                                    ->placeholder('غير متوفر')
                                    ->copyable()
                                    ->copyMessage('تم نسخ عنوان IP')
                                    ->icon('heroicon-o-globe-alt'),

                                TextEntry::make('device_type')
                                    ->label('نوع الجهاز')
                                    ->formatStateUsing(
                                        fn (?string $state): string => match ($state) {
                                            'desktop' => 'حاسوب مكتبي',
                                            'mobile' => 'هاتف محمول',
                                            'tablet' => 'جهاز لوحي',
                                            default => $state ?? 'غير محدد',
                                        }
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-device-phone-mobile'),

                                TextEntry::make('browser')
                                    ->label('المتصفح')
                                    ->placeholder('غير معروف')
                                    ->icon('heroicon-o-globe-alt'),

                                TextEntry::make('user_agent')
                                    ->label('User Agent')
                                    ->placeholder('غير متوفر')
                                    ->copyable()
                                    ->copyMessage('تم نسخ User Agent')
                                    ->columnSpanFull(),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('وصف العملية')
                    ->description('الوصف البشري للعملية التي تمت على السجل')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('description')
                            ->label('الوصف')
                            ->state(
                                fn ($record): string => match ($record->event) {
                                    'created' => 'تمت إضافة السجل إلى النظام.',
                                    'updated' => 'تم تعديل بيانات السجل.',
                                    'deleted' => 'تم حذف السجل من النظام.',
                                    'restored' => 'تمت استعادة السجل.',
                                    default => $record->description ?: 'تم تنفيذ العملية على السجل.',
                                }
                            ),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات النظام')
                    ->description('المعلومات التقنية الخاصة بسجل التدقيق')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('id')
                                    ->label('معرف سجل التدقيق')
                                    ->copyable()
                                    ->copyMessage('تم نسخ معرف سجل التدقيق'),

                                TextEntry::make('causer_id')
                                    ->label('معرف المستخدم')
                                    ->placeholder('غير متوفر')
                                    ->copyable()
                                    ->copyMessage('تم نسخ معرف المستخدم'),

                                TextEntry::make('batch_uuid')
                                    ->label('Batch UUID')
                                    ->placeholder('غير متوفر')
                                    ->copyable()
                                    ->copyMessage('تم نسخ Batch UUID'),

                                TextEntry::make('log_name')
                                    ->label('اسم السجل')
                                    ->placeholder('default'),

                                TextEntry::make('causer_type')
                                    ->label('نوع المستخدم')
                                    ->placeholder('غير متوفر'),

                                TextEntry::make('created_at')
                                    ->label('تاريخ إنشاء السجل')
                                    ->dateTime('Y-m-d H:i:s'),

                            ]),
                    ])
                    ->columnSpanFull(),

            ]);
    }

    private static function getSubjectName($record): string
    {
        $subject = $record->subject;

        if ($subject) {
            return match ($record->subject_type) {
                'App\\Models\\Citizen' => trim(
                    "{$subject->first_name} {$subject->father_name} {$subject->middle_name} {$subject->last_name}"
                ) ?: 'مواطن',

                'App\\Models\\Passport' => $subject->passport_number
                    ?? $subject->number
                    ?? 'جواز سفر',

                'App\\Models\\FamilyCard' => $subject->card_number
                    ?? 'بطاقة عائلية',

                'App\\Models\\IdentityCard' => $subject->id_number
                    ?? $subject->card_number
                    ?? $subject->identity_number
                    ?? 'بطاقة شخصية',

                default => class_basename($record->subject_type),
            };
        }

        $properties = $record->properties;

        if ($properties instanceof \Illuminate\Support\Collection) {
            $properties = $properties->toArray();
        }

        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];

        $data = !empty($attributes) ? $attributes : $old;

        return match ($record->subject_type) {
            'App\\Models\\Citizen' => trim(
                ($data['first_name'] ?? '') . ' ' .
                ($data['father_name'] ?? '') . ' ' .
                ($data['middle_name'] ?? '') . ' ' .
                ($data['last_name'] ?? '')
            ) ?: 'مواطن',

            'App\\Models\\Passport' => $data['passport_number']
                ?? $data['number']
                ?? 'جواز سفر',

            'App\\Models\\FamilyCard' => $data['card_number']
                ?? 'بطاقة عائلية',

            'App\\Models\\IdentityCard' => $data['id_number']
                ?? $data['card_number']
                ?? $data['identity_number']
                ?? 'بطاقة شخصية',

            'App\\Models\\BirthCertificate' => 'شهادة ميلاد',

            'App\\Models\\DeathCertificate' => 'شهادة وفاة',

            'App\\Models\\FamilyMember' => 'فرد أسرة',

            default => $record->subject_type
                ? class_basename($record->subject_type)
                : 'السجل المتأثر',
        };
    }

    private static function getOldValues($record): string
    {
        $properties = $record->properties;

        if ($properties instanceof \Illuminate\Support\Collection) {
            $properties = $properties->toArray();
        }

        $values = $properties['old'] ?? [];

        if (empty($values)) {
            return 'لا توجد قيمة سابقة';
        }

        return collect($values)
            ->map(
                fn ($value, $key): string =>
                    self::fieldLabel($key) . ': ' . self::translateValue($value)
            )
            ->implode("\n");
    }

    private static function getNewValues($record): string
    {
        $properties = $record->properties;

        if ($properties instanceof \Illuminate\Support\Collection) {
            $properties = $properties->toArray();
        }

        $values = $properties['attributes'] ?? [];

        if (empty($values)) {
            return 'لا توجد قيمة جديدة';
        }

        return collect($values)
            ->map(
                fn ($value, $key): string =>
                    self::fieldLabel($key) . ': ' . self::translateValue($value)
            )
            ->implode("\n");
    }

    private static function fieldLabel(string $field): string
    {
        return match ($field) {
            'national_id' => 'الرقم الوطني',
            'first_name' => 'الاسم الأول',
            'middle_name' => 'الاسم الأوسط',
            'last_name' => 'اسم العائلة',
            'father_name' => 'اسم الأب',
            'mother_name' => 'اسم الأم',
            'birth_date' => 'تاريخ الميلاد',
            'birth_place' => 'مكان الميلاد',
            'gender' => 'الجنس',
            'marital_status' => 'الحالة الاجتماعية',
            'occupation' => 'المهنة',
            'address' => 'العنوان',
            'phone' => 'رقم الهاتف',
            'email' => 'البريد الإلكتروني',
            'photo' => 'الصورة',
            'face_data' => 'بيانات الوجه',
            'fingerprint_data' => 'بيانات البصمة',
            'is_active' => 'حالة النشاط',
            'verified_at' => 'تاريخ التحقق',
            'verified_by' => 'تم التحقق بواسطة',
            'status' => 'الحالة',
            'issue_date' => 'تاريخ الإصدار',
            'expiry_date' => 'تاريخ الانتهاء',
            'id_number' => 'رقم البطاقة',
            'card_number' => 'رقم البطاقة',
            default => $field,
        };
    }

    private static function translateValue($value): string
    {
        return match ($value) {
            'single' => 'أعزب',
            'married' => 'متزوج',
            'widowed' => 'أرمل',
            'divorced' => 'مطلق',
            'male' => 'ذكر',
            'female' => 'أنثى',
            'active' => 'سارية',
            'pending' => 'قيد الانتظار',
            'expired' => 'منتهية',
            'cancelled' => 'ملغاة',
            'lost' => 'مفقودة',
            'damaged' => 'تالفة',
            true => 'نعم',
            false => 'لا',
            null => '—',
            default => is_scalar($value)
                ? (string) $value
                : json_encode($value, JSON_UNESCAPED_UNICODE),
        };
    }
}
