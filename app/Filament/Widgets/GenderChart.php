<?php

namespace App\Filament\Widgets;

use App\Models\Citizen;
use Filament\Widgets\ChartWidget;

class GenderChart extends ChartWidget
{
    protected ?string $heading = 'حسب الجنس';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $male = Citizen::where('gender', 'male')->count();

        $female = Citizen::where('gender', 'female')->count();

        return [
            'labels' => [
                'ذكور',
                'إناث',
            ],

            'datasets' => [
                [
                    'data' => [
                        $male,
                        $female,
                    ],

                    'backgroundColor' => [
                        '#2563EB',
                        '#DB2777',
                    ],

                    'borderColor' => [
                        '#1D4ED8',
                        '#BE185D',
                    ],

                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,

            'aspectRatio' => 1.5,

            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'padding' => 10,
                        'usePointStyle' => true,
                    ],
                ],
            ],

            'cutout' => '65%',
        ];
    }
}
