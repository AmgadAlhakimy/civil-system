<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $title = 'لوحة التحكم';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'لوحة التحكم';

    protected static ?string $slug = 'dashboard';

    protected string $view = 'filament.pages.dashboard';

    public function getHeading(): string
    {
        $user = auth()->user();

        return 'مرحبًا بك، ' . ($user?->full_name ?? $user?->name);
    }

    public function getSubheading(): ?string
    {
        return 'نظرة عامة على نظام السجل المدني';
    }
}
