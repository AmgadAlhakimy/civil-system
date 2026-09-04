@php
    $themeName = app(\App\Services\SettingService::class)->get('theme', 'cyan');
    $theme = \App\Support\Themes::get($themeName) ?? \App\Support\Themes::get('cyan');
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

    .fi-body,
    .fi-main,
    .fi-main-ctn {
        background-color: var(--app-background) !important;
    }

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

    .fi-header {
        border-bottom-color: var(--app-border) !important;
    }

    .fi-section {
        border-color: var(--app-border) !important;
    }

    .fi-ta-outer {
        border-color: var(--app-border) !important;
    }

    .fi-btn-color-primary,
    .fi-btn-color-primary:hover,
    .fi-btn-color-primary:focus,
    .fi-btn-color-primary:focus-visible,
    .fi-btn-color-primary:active,
    .fi-btn-color-primary:disabled {
        color: #ffffff !important;
    }

    .fi-btn-color-primary span,
    .fi-btn-color-primary svg,
    .fi-btn-color-primary:hover span,
    .fi-btn-color-primary:hover svg,
    .fi-btn-color-primary:focus span,
    .fi-btn-color-primary:focus svg,
    .fi-btn-color-primary:focus-visible span,
    .fi-btn-color-primary:focus-visible svg,
    .fi-btn-color-primary:active span,
    .fi-btn-color-primary:active svg,
    .fi-btn-color-primary:disabled span,
    .fi-btn-color-primary:disabled svg {
        color: #ffffff !important;
    }

    .civil-save-button,
    .civil-save-button:hover,
    .civil-save-button:focus,
    .civil-save-button:focus-visible,
    .civil-save-button:active,
    .civil-save-button:disabled {
        color: #ffffff !important;
    }

    .civil-save-button span,
    .civil-save-button svg,
    .civil-save-button:hover span,
    .civil-save-button:hover svg,
    .civil-save-button:focus span,
    .civil-save-button:focus svg,
    .civil-save-button:focus-visible span,
    .civil-save-button:active span,
    .civil-save-button:active svg,
    .civil-save-button:disabled span,
    .civil-save-button:disabled svg {
        color: #ffffff !important;
    }

    .fi-input:focus,
    .fi-select:focus,
    .fi-textarea:focus {
        border-color: var(--app-primary) !important;
        --tw-ring-color: var(--app-primary) !important;
    }

    .fi-tabs-tab.fi-active {
        color: var(--app-primary) !important;
    }

    .fi-pagination-item.fi-active {
        background-color: var(--app-primary) !important;
    }

</style>

<script>
    (() => {

        const themes = @json(\App\Support\Themes::all());

        function applyTheme(themeName) {

            const theme = themes[themeName];

            if (!theme || !theme.css) {
                return;
            }

            const css = theme.css;
            const root = document.documentElement;

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

            root.style.setProperty(
                '--primary-50',
                css['primary-light']
            );

            root.style.setProperty(
                '--primary-100',
                css['primary-light']
            );

            root.style.setProperty(
                '--primary-200',
                css['primary-light']
            );

            root.style.setProperty(
                '--primary-300',
                css['primary-light']
            );

            root.style.setProperty(
                '--primary-400',
                css.primary
            );

            root.style.setProperty(
                '--primary-500',
                css.primary
            );

            root.style.setProperty(
                '--primary-600',
                css['primary-dark']
            );

            root.style.setProperty(
                '--primary-700',
                css['primary-dark']
            );

            root.style.setProperty(
                '--primary-800',
                css['primary-dark']
            );

            root.style.setProperty(
                '--primary-900',
                css['primary-dark']
            );
        }

        window.applyCivilTheme = applyTheme;

        applyTheme('{{ $themeName }}');

        document.addEventListener(
            'livewire:init',
            () => {

                Livewire.on(
                    'theme-updated',
                    (event) => {

                        const themeName =
                            typeof event === 'string'
                                ? event
                                : event.theme;

                        applyTheme(themeName);

                    }
                );

            }
        );

        document.addEventListener(
            'livewire:navigated',
            () => {

                applyTheme('{{ $themeName }}');

            }
        );

    })();
</script>
