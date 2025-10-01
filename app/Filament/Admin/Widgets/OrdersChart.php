<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan Tiket';

    protected function getData(): array
    {
        $data = Order::selectRaw('MONTH(created_at) as month, SUM(jumlah_tiket) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $values = [];

        foreach (range(1, 12) as $m) {
            $labels[] = date('F', mktime(0, 0, 0, $m, 1));
            $values[] = $data[$m] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiket Terjual',
                    'data' => $values,
                    'backgroundColor' => '#1e3a8a',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // bisa diganti 'line' kalau mau
    }
}
