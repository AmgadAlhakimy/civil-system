<?php

namespace App\Filament\Widgets;

use App\Models\BirthCertificate;
use App\Models\Citizen;
use App\Models\DeathCertificate;
use App\Models\FamilyCard;
use App\Models\Passport;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(
                'المواطنون',
                number_format(Citizen::count())
            )
                ->description('إجمالي السجلات')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(
                'الجوازات',
                number_format(Passport::count())
            )
                ->description('الجوازات المسجلة')
                ->descriptionIcon('heroicon-m-identification')
                ->color('success'),

            Stat::make(
                'بطاقات الأسرة',
                number_format(FamilyCard::count())
            )
                ->description('البطاقات المسجلة')
                ->descriptionIcon('heroicon-m-home')
                ->color('warning'),

            Stat::make(
                'شهادات الميلاد',
                number_format(BirthCertificate::count())
            )
                ->description('الشهادات المسجلة')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make(
                'شهادات الوفاة',
                number_format(DeathCertificate::count())
            )
                ->description('الشهادات المسجلة')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('danger'),
        ];
    }
}
