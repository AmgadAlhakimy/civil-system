@php
    $themeName = app(\App\Services\SettingService::class)->get('theme', 'gold');
    $theme = \App\Support\Themes::get($themeName);
    $css = $theme['css'];
@endphp

<style id="civil-system-theme">

    :root {
        --app-primary: {{ $css['primary'] }};
        --app-primary-dark: {{ $css['primary-dark'] }};
        --app-primary-light: {{ $css['primary-light'] }};

        --app-sidebar: {{ $css['sidebar'] }};
        --app-sidebar-hover: {{ $css['sidebar-hover'] }};

        --app-surface: {{ $css['surface'] }};
        --app-background: {{ $css['background'] }};
        --app-border: {{ $css['border'] }};
    }

    /* الصفحة */

    .fi-body,
    .fi-main,
    .fi-main-ctn {
        background-color: var(--app-background) !important;
    }

    /* Sidebar */

    .fi-sidebar {
        background-color: var(--app-sidebar) !important;
    }

    .fi-sidebar-group-label {
        color: #ffffff !important;
    }

    .fi-sidebar-item-btn {
        background-color: transparent !important;
        color: #ffffff !important;
    }

    .fi-sidebar-item-label,
    .fi-sidebar-item-icon {
        color: #ffffff !important;
    }

    .fi-sidebar-item-btn:hover {
        background-color: var(--app-sidebar-hover) !important;
        color: #ffffff !important;
    }

    .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
    .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
        color: #ffffff !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
        background-color: var(--app-primary) !important;
        color: #ffffff !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon {
        color: #ffffff !important;
    }

    /* Header */

    .fi-header {
        border-bottom-color: var(--app-border) !important;
    }

    /* Sections */

    .fi-section {
        border-color: var(--app-border) !important;
    }

    /* Tables */

    .fi-ta-outer {
        border-color: var(--app-border) !important;
    }

    /* Primary */

    .fi-btn-color-primary {
        --c-400: var(--app-primary);
        --c-500: var(--app-primary);
        --c-600: var(--app-primary-dark);
    }

    /* Inputs */

    .fi-input:focus,
    .fi-select:focus,
    .fi-textarea:focus {
        border-color: var(--app-primary) !important;
        --tw-ring-color: var(--app-primary) !important;
    }

    /* Tabs */

    .fi-tabs-tab.fi-active {
        color: var(--app-primary) !important;
    }

    /* Pagination */

    .fi-pagination-item.fi-active {
        background-color: var(--app-primary) !important;
    }

</style>

<script>
    (() => {

        const themes = @json(\App\Support\Themes::all());

        /*
        |--------------------------------------------------------------------------
        | Apply Theme
        |--------------------------------------------------------------------------
        */

        function applyTheme(themeName) {

            const theme = themes[themeName] ?? themes.gold;

            if (!theme || !theme.css) {
                return;
            }

            const css = theme.css;
            const root = document.documentElement;

            /*
            |--------------------------------------------------------------------------
            | Application Variables
            |--------------------------------------------------------------------------
            */

            root.style.setProperty(
                '--app-primary',
                css.primary
            );

            root.style.setProperty(
                '--app-primary-dark',
                css['primary-dark']
            );

            root.style.setProperty(
                '--app-primary-light',
                css['primary-light']
            );

            root.style.setProperty(
                '--app-sidebar',
                css.sidebar
            );

            root.style.setProperty(
                '--app-sidebar-hover',
                css['sidebar-hover']
            );

            root.style.setProperty(
                '--app-surface',
                css.surface
            );

            root.style.setProperty(
                '--app-background',
                css.background
            );

            root.style.setProperty(
                '--app-border',
                css.border
            );

            /*
            |--------------------------------------------------------------------------
            | Filament Primary Colors
            |--------------------------------------------------------------------------
            |
            | هذه مهمة حتى لا يرجع Filament إلى اللون الافتراضي
            |
            */

            root.style.setProperty(
                '--primary-50',
                css['primary-light']
            );

            root.style.setProperty(
                '--primary-100',
                css['primary-light']
            );

            root.style.setProperty(
                '--primary-500',
                css.primary
            );

            root.style.setProperty(
                '--primary-600',
                css.primary
            );

            root.style.setProperty(
                '--primary-700',
                css['primary-dark']
            );

            /*
            |--------------------------------------------------------------------------
            | Save
            |--------------------------------------------------------------------------
            */

            localStorage.setItem(
                'civil-system-theme',
                themeName
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Global Function
        |--------------------------------------------------------------------------
        */

        window.applyCivilTheme = applyTheme;

        /*
        |--------------------------------------------------------------------------
        | Apply Saved Theme Immediately
        |--------------------------------------------------------------------------
        */

        const savedTheme =
            localStorage.getItem('civil-system-theme');

        if (savedTheme && themes[savedTheme]) {

            applyTheme(savedTheme);

        } else {

            applyTheme('{{ $themeName }}');

        }

        /*
        |--------------------------------------------------------------------------
        | Livewire Init
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'livewire:init',
            () => {

                Livewire.on(
                    'theme-updated',
                    (event) => {

                        applyTheme(event.theme);

                    }
                );

            }
        );

        /*
        |--------------------------------------------------------------------------
        | Livewire SPA Navigation
        |--------------------------------------------------------------------------
        |
        | هذه النقطة تحل المشكلة التي تحدث عند الانتقال
        | من Settings إلى Citizens / Passports / Dashboard...
        |
        */

        document.addEventListener(
            'livewire:navigated',
            () => {

                const theme =
                    localStorage.getItem(
                        'civil-system-theme'
                    );

                if (
                    theme &&
                    themes[theme]
                ) {

                    applyTheme(theme);

                }

            }
        );

    })();
</script>
