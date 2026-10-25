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
        $moduleLabels = [
            'users' => 'المستخدمون',
            'roles' => 'الأدوار والصلاحيات',
            'branches' => 'الفروع',
            'citizens' => 'المواطنون',
            'passports' => 'الجوازات',
            'identity_cards' => 'بطاقات الهوية',
            'family_cards' => 'البطاقات العائلية',
            'birth_certificates' => 'شهادات الميلاد',
            'death_certificates' => 'شهادات الوفاة',
            'appointments' => 'المواعيد',
            'reports' => 'التقارير',
            'backups' => 'النسخ الاحتياطية',
            'audit_logs' => 'سجل التدقيق',
        ];

        $modules = Permission::query()
            ->where('guard_name', 'web')
            ->whereNotNull('module')
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module')
            ->values();

        $sections = [];

        foreach ($modules as $index => $module) {
            $label = $moduleLabels[$module] ?? $module;

            $checkbox = CheckboxList::make("permissions_{$module}")
                ->label('اختر الصلاحيات المطلوبة')
                ->options(
                    Permission::query()
                        ->where('guard_name', 'web')
                        ->where('module', $module)
                        ->orderBy('name')
                        ->pluck('name_ar', 'id')
                        ->toArray()
                )
                ->columns(2)
                ->bulkToggleable()
                ->live()
                ->dehydrated(false);

            if ($index === 0) {
                $checkbox->rules([
                    function (Get $get): Closure {
                        return function (
                            string $attribute,
                                   $value,
                            Closure $fail
                        ) use ($get): void {
                            $selected = false;

                            foreach (
                                Permission::query()
                                    ->where('guard_name', 'web')
                                    ->whereNotNull('module')
                                    ->distinct()
                                    ->pluck('module') as $module
                            ) {
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

            $sections[] = Section::make($label)
                ->heading($label)
                ->collapsible()
                ->collapsed($index !== 0)
                ->schema([$checkbox])
                ->columnSpanFull();
        }

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
