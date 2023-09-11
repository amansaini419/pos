<?php

namespace App\Filament\Widgets;

use App\Enums\SubAdmin\SubAdminRoleEnum;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = null;

    protected function getColumns(): int {
        return 4;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales Completed', '5/7')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),

            Stat::make('Total Pending Debt', '8/17')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),

            Stat::make('Total Sales Completed', '11/40')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),

            Stat::make('Total Sales Completed', '1/5')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),

            Stat::make('Approved Sales Order', '6/14')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),

            Stat::make('Total Delivered Products', '30/34')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),

            Stat::make('Total Commission Earned', 'GHs 24,500 / GHs 70,000')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),

            Stat::make('Total Approved Customers', '6/20')
                ->extraAttributes([
                    'class' => 'dashboard-card'
                ]),
        ];
    }

    /* public static function canView(): bool
    {
        return auth()->user()->hasRole(SubAdminRoleEnum::ADMIN->value);
    } */
}
