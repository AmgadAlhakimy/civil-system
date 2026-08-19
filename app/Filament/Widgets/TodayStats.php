<?php

namespace App\Filament\Widgets;

use App\Models\BirthCertificate;
use App\Models\Citizen;
use App\Models\DeathCertificate;
use App\Models\FamilyCard;
use App\Models\Passport;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodayStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $today = now()->toDateString();

        return [
            Stat::make(
                'معاملات اليوم',
                number_format(
                    Citizen::whereDate('created_at', $today)->count()
                    + Passport::whereDate('created_at', $today)->count()
                    + FamilyCard::whereDate('created_at', $today)->count()
                    + BirthCertificate::whereDate('created_at', $today)->count()
                    + DeathCertificate::whereDate('created_at', $today)->count()
                )
            )
                ->description('إجمالي المعاملات المسجلة اليوم')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make(
                'مواطنون جدد',
                number_format(
                    Citizen::whereDate('created_at', $today)->count()
                )
            )
                ->description('تم تسجيلهم اليوم')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),

            Stat::make(
                'جوازات اليوم',
                number_format(
                    Passport::whereDate('created_at', $today)->count()
                )
            )
                ->description('الجوازات المسجلة اليوم')
                ->descriptionIcon('heroicon-m-identification')
                ->color('info'),

            Stat::make(
                'الشهادات اليوم',
                number_format(
                    BirthCertificate::whereDate('created_at', $today)->count()
                    + DeathCertificate::whereDate('created_at', $today)->count()
                )
            )
                ->description('شهادات الميلاد والوفاة')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
        ];
    }
}
