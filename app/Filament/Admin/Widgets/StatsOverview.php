<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Film;
use App\Models\Order;
use App\Models\Ruangan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // biar muncul paling atas

    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Film', Film::count())
                ->description('Total film yang tersedia')
                ->icon('heroicon-o-film')
                ->color('success'),

            Stat::make('Jumlah Ruangan', Ruangan::count())
                ->description('Studio aktif')
                ->icon('heroicon-o-building-library')
                ->color('primary'),

            Stat::make('Total Tiket Terjual', Order::sum('jumlah_tiket'))
                ->description('Tiket yang sudah dipesan')
                ->Icon('heroicon-m-ticket')
                ->color('success'),


            Stat::make('Total Pendapatan', 'Rp ' . number_format(Order::sum('total_harga'), 0, ',', '.'))
                ->description('Akumulasi semua transaksi')
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
        ];
    }
}
