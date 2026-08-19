<?php

namespace App\Support;

use Filament\Support\Colors\Color;

class Themes
{
    public static function all(): array
    {
        return [

            'blue' => [
                'name' => 'أزرق',
                'description' => 'مظهر أزرق احترافي',
                'colors' => [
                    'primary' => Color::Blue,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#2563eb',
                    'primary-dark' => '#1d4ed8',
                    'primary-light' => '#eff6ff',
                    'sidebar' => '#172554',
                    'sidebar-hover' => '#1e3a8a',
                    'surface' => '#ffffff',
                    'background' => '#f8fafc',
                    'border' => '#dbeafe',
                ],
            ],

            'gold' => [
                'name' => 'ذهبي',
                'description' => 'مظهر فاخر ورسمي',
                'colors' => [
                    'primary' => Color::Amber,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#d97706',
                    'primary-dark' => '#b45309',
                    'primary-light' => '#fffbeb',
                    'sidebar' => '#451a03',
                    'sidebar-hover' => '#78350f',
                    'surface' => '#ffffff',
                    'background' => '#fffbeb',
                    'border' => '#fde68a',
                ],
            ],

            'green' => [
                'name' => 'أخضر',
                'description' => 'مظهر هادئ ومؤسسي',
                'colors' => [
                    'primary' => Color::Emerald,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#059669',
                    'primary-dark' => '#047857',
                    'primary-light' => '#ecfdf5',
                    'sidebar' => '#064e3b',
                    'sidebar-hover' => '#065f46',
                    'surface' => '#ffffff',
                    'background' => '#f0fdf4',
                    'border' => '#a7f3d0',
                ],
            ],

            'purple' => [
                'name' => 'بنفسجي',
                'description' => 'مظهر عصري وأنيق',
                'colors' => [
                    'primary' => Color::Violet,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#7c3aed',
                    'primary-dark' => '#6d28d9',
                    'primary-light' => '#f5f3ff',
                    'sidebar' => '#2e1065',
                    'sidebar-hover' => '#4c1d95',
                    'surface' => '#ffffff',
                    'background' => '#faf5ff',
                    'border' => '#ddd6fe',
                ],
            ],

            'red' => [
                'name' => 'أحمر',
                'description' => 'مظهر قوي وجريء',
                'colors' => [
                    'primary' => Color::Red,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#dc2626',
                    'primary-dark' => '#b91c1c',
                    'primary-light' => '#fef2f2',
                    'sidebar' => '#450a0a',
                    'sidebar-hover' => '#7f1d1d',
                    'surface' => '#ffffff',
                    'background' => '#fef2f2',
                    'border' => '#fecaca',
                ],
            ],

            'pink' => [
                'name' => 'وردي',
                'description' => 'مظهر أنيق وناعم',
                'colors' => [
                    'primary' => Color::Pink,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#db2777',
                    'primary-dark' => '#be185d',
                    'primary-light' => '#fdf2f8',
                    'sidebar' => '#500724',
                    'sidebar-hover' => '#831843',
                    'surface' => '#ffffff',
                    'background' => '#fdf2f8',
                    'border' => '#fbcfe8',
                ],
            ],

            'cyan' => [
                'name' => 'سماوي',
                'description' => 'مظهر تقني ومنعش',
                'colors' => [
                    'primary' => Color::Cyan,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#0891b2',
                    'primary-dark' => '#0e7490',
                    'primary-light' => '#ecfeff',
                    'sidebar' => '#083344',
                    'sidebar-hover' => '#155e75',
                    'surface' => '#ffffff',
                    'background' => '#ecfeff',
                    'border' => '#a5f3fc',
                ],
            ],

            'orange' => [
                'name' => 'برتقالي',
                'description' => 'مظهر دافئ وحيوي',
                'colors' => [
                    'primary' => Color::Orange,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#ea580c',
                    'primary-dark' => '#c2410c',
                    'primary-light' => '#fff7ed',
                    'sidebar' => '#431407',
                    'sidebar-hover' => '#7c2d12',
                    'surface' => '#ffffff',
                    'background' => '#fff7ed',
                    'border' => '#fed7aa',
                ],
            ],

            'teal' => [
                'name' => 'تركوازي',
                'description' => 'مظهر حديث ومتوازن',
                'colors' => [
                    'primary' => Color::Teal,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#0d9488',
                    'primary-dark' => '#0f766e',
                    'primary-light' => '#f0fdfa',
                    'sidebar' => '#042f2e',
                    'sidebar-hover' => '#115e59',
                    'surface' => '#ffffff',
                    'background' => '#f0fdfa',
                    'border' => '#99f6e4',
                ],
            ],

            'indigo' => [
                'name' => 'نيلي',
                'description' => 'مظهر احترافي وعصري',
                'colors' => [
                    'primary' => Color::Indigo,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#4f46e5',
                    'primary-dark' => '#4338ca',
                    'primary-light' => '#eef2ff',
                    'sidebar' => '#1e1b4b',
                    'sidebar-hover' => '#312e81',
                    'surface' => '#ffffff',
                    'background' => '#eef2ff',
                    'border' => '#c7d2fe',
                ],
            ],

            'brown' => [
                'name' => 'بني',
                'description' => 'مظهر كلاسيكي ودافئ',
                'colors' => [
                    'primary' => Color::Amber,
                    'gray' => Color::Stone,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#92400e',
                    'primary-dark' => '#78350f',
                    'primary-light' => '#fffbeb',
                    'sidebar' => '#292524',
                    'sidebar-hover' => '#44403c',
                    'surface' => '#ffffff',
                    'background' => '#fafaf9',
                    'border' => '#e7e5e4',
                ],
            ],

            'slate' => [
                'name' => 'رمادي داكن',
                'description' => 'مظهر احترافي هادئ',
                'colors' => [
                    'primary' => Color::Slate,
                    'gray' => Color::Slate,
                    'success' => Color::Green,
                    'danger' => Color::Red,
                    'warning' => Color::Orange,
                    'info' => Color::Sky,
                ],
                'css' => [
                    'primary' => '#475569',
                    'primary-dark' => '#334155',
                    'primary-light' => '#f1f5f9',
                    'sidebar' => '#0f172a',
                    'sidebar-hover' => '#1e293b',
                    'surface' => '#ffffff',
                    'background' => '#f8fafc',
                    'border' => '#cbd5e1',
                ],
            ],
        ];
    }

    public static function get(string $theme): array
    {
        return static::all()[$theme] ?? static::all()['gold'];
    }

    public static function options(): array
    {
        return collect(static::all())
            ->mapWithKeys(fn (array $theme, string $key) => [
                $key => $theme['name'],
            ])
            ->all();
    }

    public static function css(string $theme): array
    {
        return static::get($theme)['css'];
    }
}
