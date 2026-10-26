<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleInfolist
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

        return $schema->components([
            Section::make('بيانات الدور')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    TextEntry::make('name')
                        ->label('اسم الدور')
                        ->placeholder('غير محدد')
                        ->columnSpanFull(),

                    TextEntry::make('created_at')
                        ->label('تاريخ الإنشاء')
                        ->dateTime('Y-m-d H:i')
                        ->placeholder('غير متوفر'),

                    TextEntry::make('updated_at')
                        ->label('آخر تحديث')
                        ->dateTime('Y-m-d H:i')
                        ->placeholder('غير متوفر'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('الصلاحيات الممنوحة')
                ->icon('heroicon-o-key')
                ->description('الوحدات والصلاحيات الممنوحة لهذا الدور فقط.')
                ->schema([
                    RepeatableEntry::make('permissions')
                        ->label('الصلاحيات')
                        ->state(function ($record) use ($moduleLabels): array {
                            return $record->permissions()
                                ->where('guard_name', 'web')
                                ->orderBy('module')
                                ->orderBy('name')
                                ->get()
                                ->groupBy(
                                    fn ($permission) => $permission->module
                                        ?? 'other'
                                )
                                ->map(function ($permissions, $module) use ($moduleLabels): array {
                                    return [
                                        'module_label' => $moduleLabels[$module]
                                            ?? ($module === 'other'
                                                ? 'صلاحيات أخرى'
                                                : $module),
                                        'permission_names' => $permissions
                                            ->map(
                                                fn ($permission) => $permission->name_ar
                                                    ?: $permission->name
                                            )
                                            ->values()
                                            ->all(),
                                    ];
                                })
                                ->values()
                                ->all();
                        })
                        ->schema([
                            TextEntry::make('module_label')
                                ->label('الوحدة')
                                ->weight('bold'),

                            TextEntry::make('permission_names')
                                ->label('الصلاحيات')
                                ->listWithLineBreaks()
                                ->bulleted(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
