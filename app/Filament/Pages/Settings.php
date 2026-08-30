<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use App\Support\Themes;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                'gold'
            ),

            'activity_log_enabled' => $settings->get(
                'activity_log_enabled',
                true
            ),

            'automatic_backup_enabled' => $settings->get(
                'automatic_backup_enabled',
                true
            ),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('الإعدادات العامة')
                    ->description('المعلومات الأساسية والإعدادات العامة للنظام')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextInput::make('system_name')
                                    ->label('اسم النظام')
                                    ->placeholder('مثال: السجل المدني')
                                    ->prefixIcon('heroicon-o-building-office-2')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'حقل اسم النظام مطلوب',
                                        'min' => 'يجب ألا يقل اسم النظام عن 3 أحرف',
                                        'max' => 'يجب ألا يتجاوز اسم النظام 255 حرفًا',
                                    ]),

                                TextInput::make('organization_name')
                                    ->label('اسم الجهة')
                                    ->placeholder('مثال: مصلحة الأحوال المدنية والسجل المدني')
                                    ->prefixIcon('heroicon-o-building-library')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'حقل اسم الجهة مطلوب',
                                        'min' => 'يجب ألا يقل اسم الجهة عن 3 أحرف',
                                        'max' => 'يجب ألا يتجاوز اسم الجهة 255 حرفًا',
                                    ]),

                                Select::make('timezone')
                                    ->label('المنطقة الزمنية')
                                    ->options([
                                        'Asia/Aden' => 'اليمن - صنعاء (Asia/Aden)',
                                        'Asia/Riyadh' => 'السعودية - الرياض (Asia/Riyadh)',
                                        'UTC' => 'التوقيت العالمي (UTC)',
                                    ])
                                    ->prefixIcon('heroicon-o-clock')
                                    ->default('Asia/Aden')
                                    ->searchable()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار المنطقة الزمنية',
                                    ]),

                                Select::make('theme')
                                    ->label('لون النظام')
                                    ->options(
                                        collect(Themes::all())
                                            ->mapWithKeys(
                                                function (array $theme, string $key): array {
                                                    $name = $theme['name'] ?? $key;
                                                    $primary = $theme['css']['primary'] ?? '#d97706';

                                                    return [
                                                        $key => "
                                                            <div class=\"flex items-center gap-3\">
                                                                <span
                                                                    class=\"w-7 h-7 rounded-full ring-2 ring-white shadow-sm\"
                                                                    style=\"background-color: {$primary}\"
                                                                ></span>
                                                                <span>{$name}</span>
                                                            </div>
                                                        ",
                                                    ];
                                                }
                                            )
                                            ->toArray()
                                    )
                                    ->allowHtml()
                                    ->prefixIcon('heroicon-o-swatch')
                                    ->default('gold')
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (?string $state): void {
                                        if ($state) {
                                            $this->js(
                                                'window.applyCivilTheme(' . json_encode($state) . ');'
                                            );
                                        }
                                    })
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار لون النظام',
                                    ])
                                    ->helperText(
                                        'يتم تطبيق اللون مباشرة على واجهة النظام عند اختياره.'
                                    ),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('إعدادات الأمان')
                    ->description('إعدادات حماية ومراقبة النظام')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Toggle::make('activity_log_enabled')
                            ->label('تفعيل سجل الأنشطة')
                            ->helperText('تسجيل عمليات المستخدمين وتغييرات بيانات النظام لأغراض المراجعة والمتابعة.')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columnSpanFull(),

                Section::make('إعدادات النسخ الاحتياطي')
                    ->description('إعدادات النسخ الاحتياطي للنظام')
                    ->icon('heroicon-o-server-stack')
                    ->schema([

                        Toggle::make('automatic_backup_enabled')
                            ->label('تفعيل النسخ الاحتياطي التلقائي')
                            ->helperText('السماح للنظام بتنفيذ النسخ الاحتياطي التلقائي')
                            ->default(true),

                    ])
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
            'activity_log_enabled',
            (bool) $data['activity_log_enabled'],
            'security',
            'تفعيل سجل الأنشطة'
        );

        $settings->set(
            'automatic_backup_enabled',
            (bool) $data['automatic_backup_enabled'],
            'backup',
            'تفعيل النسخ الاحتياطي التلقائي'
        );

        $this->js(
            'window.applyCivilTheme(' . json_encode($data['theme']) . ');'
        );

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->body('تم تطبيق الإعدادات الجديدة بنجاح.')
            ->success()
            ->send();
    }
}
