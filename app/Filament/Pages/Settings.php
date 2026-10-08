<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use App\Support\Themes;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Settings extends Page
{
    protected string $view = 'filament.pages.settings';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'النظام';
    }

    public static function getNavigationLabel(): string
    {
        return 'الإعدادات';
    }

    public function getTitle(): string
    {
        return 'الإعدادات';
    }

    public function mount(): void
    {
        $settings = app(SettingService::class);

        $this->form->fill([
            'system_name' => $settings->get(
                'system_name',
                'السجل المدني'
            ),

            'organization_name' => $settings->get(
                'organization_name',
                ''
            ),

            'timezone' => $settings->get(
                'timezone',
                'Asia/Aden'
            ),

            'theme' => $settings->get(
                'theme',
                'cyan'
            ),

            'automatic_backup_enabled' => filter_var(
                $settings->get(
                    'automatic_backup_enabled',
                    true
                ),
                FILTER_VALIDATE_BOOLEAN
            ),

            'automatic_backup_frequency' => $settings->get(
                'automatic_backup_frequency',
                'daily'
            ),

            'automatic_backup_time' => $settings->get(
                'automatic_backup_time',
                '03:00'
            ),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('الإعدادات العامة')
                    ->description(
                        'المعلومات الأساسية والإعدادات العامة للنظام'
                    )
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('system_name')
                                    ->label('اسم النظام')
                                    ->placeholder('مثال: السجل المدني')
                                    ->prefixIcon(
                                        'heroicon-o-building-office-2'
                                    )
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' =>
                                            'حقل اسم النظام مطلوب',
                                        'min' =>
                                            'يجب ألا يقل اسم النظام عن 3 أحرف',
                                        'max' =>
                                            'يجب ألا يتجاوز اسم النظام 255 حرفًا',
                                    ]),

                                TextInput::make('organization_name')
                                    ->label('اسم الجهة')
                                    ->placeholder(
                                        'مثال: مصلحة الأحوال المدنية والسجل المدني'
                                    )
                                    ->prefixIcon(
                                        'heroicon-o-building-library'
                                    )
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' =>
                                            'حقل اسم الجهة مطلوب',
                                        'min' =>
                                            'يجب ألا يقل اسم الجهة عن 3 أحرف',
                                        'max' =>
                                            'يجب ألا يتجاوز اسم الجهة 255 حرفًا',
                                    ]),

                                Select::make('timezone')
                                    ->label('المنطقة الزمنية')
                                    ->options([
                                        'Asia/Aden' =>
                                            'اليمن - صنعاء (Asia/Aden)',
                                        'Asia/Riyadh' =>
                                            'السعودية - الرياض (Asia/Riyadh)',
                                        'UTC' =>
                                            'التوقيت العالمي (UTC)',
                                    ])
                                    ->prefixIcon('heroicon-o-clock')
                                    ->default('Asia/Aden')
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' =>
                                            'يرجى اختيار المنطقة الزمنية',
                                    ]),

                                Select::make('theme')
                                    ->label('لون النظام')
                                    ->options(
                                        collect(Themes::all())
                                            ->mapWithKeys(
                                                function (
                                                    array $theme,
                                                    string $key
                                                ): array {
                                                    $name =
                                                        $theme['name'] ?? $key;

                                                    $primary =
                                                        $theme['css']['primary']
                                                        ?? '#0891b2';

                                                    return [
                                                        $key =>
                                                            "<div class=\"flex items-center gap-3\">
                                                                <span
                                                                    class=\"w-7 h-7 rounded-full ring-2 ring-white shadow-sm\"
                                                                    style=\"background-color: {$primary}\"
                                                                ></span>
                                                                <span>{$name}</span>
                                                            </div>",
                                                    ];
                                                }
                                            )
                                            ->toArray()
                                    )
                                    ->allowHtml()
                                    ->prefixIcon('heroicon-o-swatch')
                                    ->default('cyan')
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' =>
                                            'يرجى اختيار لون النظام',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('إعدادات النسخ الاحتياطي')
                    ->description(
                        'إعدادات النسخ الاحتياطي التلقائي للنظام'
                    )
                    ->icon('heroicon-o-server-stack')
                    ->schema([
                        Toggle::make('automatic_backup_enabled')
                            ->label(
                                'تفعيل النسخ الاحتياطي التلقائي'
                            )
                            ->helperText(
                                'السماح للنظام بتنفيذ النسخ الاحتياطي تلقائيًا حسب الجدول المحدد.'
                            )
                            ->default(true)
                            ->live(),

                        Select::make('automatic_backup_frequency')
                            ->label('تكرار النسخ الاحتياطي')
                            ->options([
                                'daily' => 'يوميًا',
                                'weekly' => 'أسبوعيًا',
                                'monthly' => 'شهريًا',
                            ])
                            ->default('daily')
                            ->required()
                            ->native(false)
                            ->prefixIcon(
                                'heroicon-o-arrow-path'
                            ),

                        TimePicker::make('automatic_backup_time')
                            ->label('وقت التنفيذ')
                            ->default('03:00')
                            ->seconds(false)
                            ->required()
                            ->prefixIcon(
                                'heroicon-o-clock'
                            ),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = app(SettingService::class);

        $settings->set(
            'system_name',
            $data['system_name'],
            'general',
            'اسم النظام',
            true
        );

        $settings->set(
            'organization_name',
            $data['organization_name'],
            'general',
            'اسم الجهة',
            true
        );

        $settings->set(
            'timezone',
            $data['timezone'],
            'general',
            'المنطقة الزمنية'
        );

        $settings->set(
            'theme',
            $data['theme'],
            'appearance',
            'ثيم واجهة النظام',
            true
        );

        $settings->set(
            'automatic_backup_enabled',
            (bool) $data['automatic_backup_enabled'],
            'backup',
            'تفعيل النسخ الاحتياطي التلقائي'
        );

        $settings->set(
            'automatic_backup_frequency',
            $data['automatic_backup_frequency'],
            'backup',
            'تكرار النسخ الاحتياطي التلقائي'
        );

        $settings->set(
            'automatic_backup_time',
            $data['automatic_backup_time'],
            'backup',
            'وقت تنفيذ النسخ الاحتياطي التلقائي'
        );

        $this->js(
            'window.applyCivilTheme(' .
            json_encode($data['theme']) .
            ');'
        );

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->body('تم تطبيق الإعدادات الجديدة بنجاح.')
            ->success()
            ->send();
    }
}
