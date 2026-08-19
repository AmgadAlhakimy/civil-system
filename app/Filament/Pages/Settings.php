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

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $settings = app(SettingService::class);

        $this->form->fill([
            /*
            |--------------------------------------------------------------------------
            | General
            |--------------------------------------------------------------------------
            */

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

            'locale' => $settings->get(
                'locale',
                'ar'
            ),

            /*
            |--------------------------------------------------------------------------
            | Appearance
            |--------------------------------------------------------------------------
            */

            'theme' => $settings->get(
                'theme',
                'gold'
            ),

            'sidebar_collapsed' => $settings->get(
                'sidebar_collapsed',
                false
            ),

            /*
            |--------------------------------------------------------------------------
            | Civil Registry
            |--------------------------------------------------------------------------
            */

            'national_id_length' => $settings->get(
                'national_id_length',
                11
            ),

            'family_card_number_length' => $settings->get(
                'family_card_number_length',
                11
            ),

            /*
            |--------------------------------------------------------------------------
            | Security
            |--------------------------------------------------------------------------
            */

            'activity_log_enabled' => $settings->get(
                'activity_log_enabled',
                true
            ),

            /*
            |--------------------------------------------------------------------------
            | Backup
            |--------------------------------------------------------------------------
            */

            'automatic_backup_enabled' => $settings->get(
                'automatic_backup_enabled',
                true
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | General Settings
                |--------------------------------------------------------------------------
                */

                Section::make('الإعدادات العامة')
                    ->description('الإعدادات الأساسية للنظام')
                    ->schema([

                        TextInput::make('system_name')
                            ->label('اسم النظام')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('organization_name')
                            ->label('اسم الجهة')
                            ->maxLength(255),

                        Select::make('timezone')
                            ->label('المنطقة الزمنية')
                            ->options([
                                'Asia/Aden' => 'اليمن - Asia/Aden',
                                'Asia/Riyadh' => 'السعودية - Asia/Riyadh',
                                'UTC' => 'UTC',
                            ])
                            ->required(),

                        Select::make('locale')
                            ->label('لغة النظام')
                            ->options([
                                'ar' => 'العربية',
                                'en' => 'English',
                            ])
                            ->required(),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Appearance
                |--------------------------------------------------------------------------
                */

                Section::make('المظهر والواجهة')
                    ->description('تخصيص مظهر لوحة التحكم وواجهة النظام')
                    ->schema([

                        Select::make('theme')
                            ->label('لون النظام')
                            ->options(Themes::options())
                            ->default('gold')
                            ->required()
                            ->live(),

                        Toggle::make('sidebar_collapsed')
                            ->label('طي القائمة الجانبية')
                            ->default(false),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Civil Registry
                |--------------------------------------------------------------------------
                */

                Section::make('إعدادات السجل المدني')
                    ->description('إعدادات أرقام السجل المدني')
                    ->schema([

                        TextInput::make('national_id_length')
                            ->label('طول الرقم الوطني')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->required(),

                        TextInput::make('family_card_number_length')
                            ->label('طول رقم البطاقة العائلية')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->required(),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Security
                |--------------------------------------------------------------------------
                */

                Section::make('إعدادات الأمان')
                    ->description('إعدادات حماية ومراقبة النظام')
                    ->schema([

                        Toggle::make('activity_log_enabled')
                            ->label('تفعيل سجل الأنشطة')
                            ->default(true),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Backup
                |--------------------------------------------------------------------------
                */

                Section::make('إعدادات النسخ الاحتياطي')
                    ->description('إعدادات النسخ الاحتياطي للنظام')
                    ->schema([

                        Toggle::make('automatic_backup_enabled')
                            ->label('تفعيل النسخ الاحتياطي التلقائي')
                            ->default(true),

                    ]),

            ])
            ->statePath('data');
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = app(SettingService::class);

        /*
        |--------------------------------------------------------------------------
        | General
        |--------------------------------------------------------------------------
        */

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
            'locale',
            $data['locale'],
            'general',
            'لغة النظام'
        );

        /*
        |--------------------------------------------------------------------------
        | Appearance
        |--------------------------------------------------------------------------
        */

        $settings->set(
            'theme',
            $data['theme'],
            'appearance',
            'ثيم واجهة النظام',
            true
        );

        $settings->set(
            'sidebar_collapsed',
            (bool) $data['sidebar_collapsed'],
            'appearance',
            'طي القائمة الجانبية افتراضيًا',
            true
        );

        /*
        |--------------------------------------------------------------------------
        | Civil Registry
        |--------------------------------------------------------------------------
        */

        $settings->set(
            'national_id_length',
            (int) $data['national_id_length'],
            'civil_registry',
            'طول الرقم الوطني'
        );

        $settings->set(
            'family_card_number_length',
            (int) $data['family_card_number_length'],
            'civil_registry',
            'طول رقم البطاقة العائلية'
        );

        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        $settings->set(
            'activity_log_enabled',
            (bool) $data['activity_log_enabled'],
            'security',
            'تفعيل سجل الأنشطة'
        );

        /*
        |--------------------------------------------------------------------------
        | Backup
        |--------------------------------------------------------------------------
        */

        $settings->set(
            'automatic_backup_enabled',
            (bool) $data['automatic_backup_enabled'],
            'backup',
            'تفعيل النسخ الاحتياطي التلقائي'
        );

        /*
        |--------------------------------------------------------------------------
        | Apply Theme Immediately
        |--------------------------------------------------------------------------
        |
        | لا نحتاج إلى window.location.reload()
        |
        | theme.blade.php يحتوي على:
        |
        | window.applyCivilTheme()
        |
        | وهذه الدالة تقوم بتحديث:
        | - اللون الأساسي
        | - Sidebar
        | - Hover
        | - الخلفية
        | - الحدود
        | - ألوان Filament الأساسية
        |
        */

        $this->js("
            window.applyCivilTheme('{$data['theme']}');
        ");

        /*
        |--------------------------------------------------------------------------
        | Notification
        |--------------------------------------------------------------------------
        */

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->body('تم تطبيق المظهر الجديد مباشرة.')
            ->success()
            ->send();
    }
}
