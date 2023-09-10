<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BrandPopularityPieChart extends ChartWidget
{
    protected static ?string $heading = 'Brand popularity';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    protected static ?array $options = [
        /* 'plugins' => [
            'legend' => [
                'display' => true,
            ],
        ], */
        'scales' => [
            'x' => [
                'display' => false,
            ],
            'y' => [
                'display' => false,
            ],
        ]
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total deliveries',
                    'data' => [50, 160],
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                    ]
                ],
            ],
            'labels' => ['Pending Orders', 'Approved Sales'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
