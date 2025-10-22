<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\OrdersChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    // protected static ?string $navigationGroup = 'Laporan';
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            // OrdersChart::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
