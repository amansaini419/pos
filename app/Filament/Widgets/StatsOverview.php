<?php

namespace App\Filament\Widgets;

use App\Enums\SubAdmin\SubAdminRoleEnum;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = [
        'xl' => 6,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales Completed', '5/7'),
            Stat::make('Total Pending Debt', '8/17'),
            Stat::make('Total Sales Completed', '11/40'),
            Stat::make('Total Sales Completed', '1/5'),

            Stat::make('Approved Sales Order', '6/14'),
            Stat::make('Total Delivered Products', '30/34'),
            Stat::make('Total Commission Earned', 'GHs 24,500/ GHs 70,000'),
            Stat::make('Total Approved Customers', '6/20'),
        ];
    }

    /* public static function canView(): bool
    {
        return auth()->user()->hasRole(SubAdminRoleEnum::ADMIN->value);
    } */
}
