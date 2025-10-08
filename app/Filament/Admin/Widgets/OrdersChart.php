<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrdersChart extends ChartWidget
{
    // Properti statis harus dipertahankan sebagai nilai konstan
    protected static ?string $heading = 'Grafik Penjualan Tiket';

    protected static ?string $pollingInterval = '24h';

    // 💡 PERBAIKAN: Hak akses harus PUBLIC, sesuai dengan kelas induk Filament
    public function getHeading(): string
    {
        return 'Grafik Penjualan Tiket Tahun ' . Carbon::now()->year;
    }

    // Metode lain (getData, getType, getOptions) harus tetap protected/public 
    // sesuai dengan yang didefinisikan oleh Filament

    protected function getData(): array
    {
        $monthlySales = Order::query()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(jumlah_tiket) as total')
            )
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $labels = [];
        $values = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::createFromDate(null, $m, 1)->translatedFormat('F');

            $labels[] = $monthName;
            $values[] = $monthlySales[$m] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiket Terjual',
                    'data' => $values,
                    'backgroundColor' => '#1e3a8a',
                    'borderColor' => '#1e3a8a',
                    'borderWidth' => 2,
                    'borderRadius' => 5,
                    'hoverBackgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
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
