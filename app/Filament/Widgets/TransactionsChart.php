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
                    'borderColor' => '#2563EB',
                    'backgroundColor' => '#2563EB',
                    'pointBackgroundColor' => '#2563EB',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
                ],
                [
                    'label' => 'الجوازات',
                    'data' => $passports,
                    'tension' => 0.4,
                    'borderColor' => '#D97706',
                    'backgroundColor' => '#D97706',
                    'pointBackgroundColor' => '#D97706',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
                ],
                [
                    'label' => 'بطاقات الأسرة',
                    'data' => $familyCards,
                    'tension' => 0.4,
                    'borderColor' => '#16A34A',
                    'backgroundColor' => '#16A34A',
                    'pointBackgroundColor' => '#16A34A',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
                ],
                [
                    'label' => 'شهادات الميلاد',
                    'data' => $birthCertificates,
                    'tension' => 0.4,
                    'borderColor' => '#9333EA',
                    'backgroundColor' => '#9333EA',
                    'pointBackgroundColor' => '#9333EA',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
                ],
                [
                    'label' => 'شهادات الوفاة',
                    'data' => $deathCertificates,
                    'tension' => 0.4,
                    'borderColor' => '#DC2626',
                    'backgroundColor' => '#DC2626',
                    'pointBackgroundColor' => '#DC2626',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
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
