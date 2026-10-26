<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        /*
        |--------------------------------------------------------------------------
        | أسماء الوحدات باللغة العربية
        |--------------------------------------------------------------------------
        */

        $moduleLabels = [
            'users' => 'المستخدمون',
            'roles' => 'الأدوار والصلاحيات',
            'branches' => 'الفروع',
            'governorates' => 'المحافظات',
            'districts' => 'المديريات',
            'citizens' => 'المواطنون',
            'passports' => 'الجوازات',
            'identity_cards' => 'بطاقات الهوية',
            'family_cards' => 'البطاقات العائلية',
            'birth_certificates' => 'شهادات الميلاد',
            'death_certificates' => 'شهادات الوفاة',
            'appointments' => 'المواعيد',
            'service_requests' => 'طلبات الخدمات',
            'service_fees' => 'رسوم الخدمات',
            'reports' => 'التقارير',
            'backups' => 'النسخ الاحتياطية',
            'audit_logs' => 'سجل التدقيق',
            'settings' => 'الإعدادات',
        ];

        /*
        |--------------------------------------------------------------------------
        | جلب الوحدات التي تحتوي على صلاحيات
        |--------------------------------------------------------------------------
        */

        $modules = Permission::query()
            ->where('guard_name', 'web')
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module')
            ->values();

        $sections = [];

        /*
        |--------------------------------------------------------------------------
        | إنشاء قسم لكل وحدة وصلاحياتها
        |--------------------------------------------------------------------------
        */

        foreach ($modules as $index => $module) {
            $label = $moduleLabels[$module] ?? $module;

            $permissions = Permission::query()
                ->where('guard_name', 'web')
                ->where('module', $module)
                ->orderBy('name')
                ->get(['id', 'name', 'name_ar']);

            $options = [];

            foreach ($permissions as $permission) {
                $arabicName = trim((string) $permission->name_ar);

                $options[$permission->id] = $arabicName !== ''
                    ? $arabicName
                    : $permission->name;
            }

            $checkbox = CheckboxList::make("permissions_{$module}")
                ->label('اختر الصلاحيات المطلوبة')
                ->options($options)
                ->columns(2)
                ->bulkToggleable()
                ->live()
                ->dehydrated(false);

            /*
            |--------------------------------------------------------------------------
            | التحقق من اختيار صلاحية واحدة على الأقل
            |--------------------------------------------------------------------------
            */

            if ($index === 0) {
                $checkbox->rules([
                    function (Get $get): Closure {
                        return function (
                            string $attribute,
                                   $value,
                            Closure $fail
                        ) use ($get): void {
                            $selected = false;

                            $modules = Permission::query()
                                ->where('guard_name', 'web')
                                ->whereNotNull('module')
                                ->where('module', '!=', '')
                                ->distinct()
                                ->pluck('module');

                            foreach ($modules as $module) {
                                $values = $get("permissions_{$module}");

                                if (is_array($values) && count($values) > 0) {
                                    $selected = true;
                                    break;
                                }
                            }

                            if (! $selected) {
                                $fail(
                                    'يجب اختيار صلاحية واحدة على الأقل قبل حفظ الدور.'
                                );
                            }
                        };
                    },
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | إضافة القسم إلى النموذج
            |--------------------------------------------------------------------------
            */

            $sections[] = Section::make($label)
                ->heading($label)
                ->collapsible()
                ->collapsed($index !== 0)
                ->schema([$checkbox])
                ->columnSpanFull();
        }

        /*
        |--------------------------------------------------------------------------
        | إرجاع النموذج
        |--------------------------------------------------------------------------
        */

        return $schema->components([
            Section::make('بيانات الدور')
                ->schema([
                    TextInput::make('name')
                        ->label('اسم الدور')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->placeholder('مثال: موظف التسجيل')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            ...$sections,
        ]);
    }
}
