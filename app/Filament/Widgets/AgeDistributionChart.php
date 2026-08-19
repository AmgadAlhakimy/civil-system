<?php

namespace App\Filament\Widgets;

use App\Models\Citizen;
use Filament\Widgets\ChartWidget;

class AgeDistributionChart extends ChartWidget
{
    protected ?string $heading = 'الفئات العمرية';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $groups = [
            'أقل من 18' => 0,
            '18 - 30' => 0,
            '31 - 45' => 0,
            '46 - 60' => 0,
            'أكثر من 60' => 0,
        ];

        Citizen::whereNotNull('birth_date')
            ->get(['birth_date'])
            ->each(function ($citizen) use (&$groups) {

                $age = \Carbon\Carbon::parse($citizen->birth_date)
                    ->age;

                match (true) {
                    $age < 18 => $groups['أقل من 18']++,
                    $age <= 30 => $groups['18 - 30']++,
                    $age <= 45 => $groups['31 - 45']++,
                    $age <= 60 => $groups['46 - 60']++,
                    default => $groups['أكثر من 60']++,
                };
            });

        return [
            'labels' => array_keys($groups),

            'datasets' => [
                [
                    'label' => 'عدد المواطنين',
                    'data' => array_values($groups),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,

            'maintainAspectRatio' => false,

            'plugins' => [
                'legend' => [
                    'display' => false,
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
