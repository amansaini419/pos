<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TotalSalesChart extends ChartWidget
{
    protected static ?string $heading = 'Total sales trend';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total sales',
                    'data' => [150, 160, 165, 200, 210, 320, 450],
                ],
            ],
            'labels' => ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
