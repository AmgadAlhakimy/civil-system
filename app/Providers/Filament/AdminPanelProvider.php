<?php

namespace App\Providers\Filament;

use App\Services\SettingService;
use App\Support\Themes;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TodayStats;
use App\Filament\Widgets\TransactionsChart;
use App\Filament\Widgets\BranchesOverview;
use App\Filament\Widgets\RecentActivities;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = app(SettingService::class);

        $themeName = $settings->get('theme', 'cyan');

        $theme = Themes::get($themeName);

        return $panel
            ->default()
            ->id('admin')
            ->path('dashboard')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('السجل المدني')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(
                'panels::head.end',
                fn (): string => view('filament.theme', [
                    'theme' => Themes::get(
                        app(SettingService::class)->get(
                            'theme',
                            'cyan'
                        )
                    ),
                ])->render(),
            )
            ->renderHook(
                'panels::body.end',
                fn (): string => <<<'HTML'
<script>
function disableHtmlValidation() {
    document.querySelectorAll('form').forEach(function (form) {
        form.setAttribute('novalidate', 'novalidate');
    });
}

document.addEventListener('DOMContentLoaded', disableHtmlValidation);

document.addEventListener('livewire:navigated', disableHtmlValidation);

document.addEventListener('livewire:init', function () {
    disableHtmlValidation();

    Livewire.hook('morph.updated', function () {
        disableHtmlValidation();
    });
});
</script>
HTML
            )
            ->colors($theme['colors'])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('240px')
            ->globalSearch()
            ->navigationGroups([
                'النظام',
                'السجل المدني',
            ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )
            ->widgets([
                StatsOverview::class,
                TodayStats::class,
                TransactionsChart::class,
                BranchesOverview::class,
                RecentActivities::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
