<?php

namespace App\Filament\Widgets;

use App\Models\BirthCertificate;
use App\Models\Citizen;
use App\Models\DeathCertificate;
use App\Models\FamilyCard;
use App\Models\Passport;
use Filament\Widgets\ChartWidget;

class TransactionsChart extends ChartWidget
{
    protected ?string $heading = 'المعاملات خلال آخر 7 أيام';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $labels = [];
        $citizens = [];
        $passports = [];
        $familyCards = [];
        $birthCertificates = [];
        $deathCertificates = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $labels[] = $date->translatedFormat('D');

            $citizens[] = Citizen::whereDate('created_at', $date)->count();

            $passports[] = Passport::whereDate('created_at', $date)->count();

            $familyCards[] = FamilyCard::whereDate('created_at', $date)->count();

            $birthCertificates[] = BirthCertificate::whereDate('created_at', $date)->count();

            $deathCertificates[] = DeathCertificate::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'المواطنون',
                    'data' => $citizens,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'الجوازات',
                    'data' => $passports,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'بطاقات الأسرة',
                    'data' => $familyCards,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'شهادات الميلاد',
                    'data' => $birthCertificates,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'شهادات الوفاة',
                    'data' => $deathCertificates,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
