<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Showtime;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class UpcomingShowtimes extends Widget
{
    protected static string $view = 'filament.admin.widgets.upcoming-showtimes';

    protected static ?string $heading = 'Jadwal Tayang Mendatang';

    public function getShowtimes()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        return Showtime::with(['film', 'ruangan'])
            ->whereBetween('tanggal', [$today, $tomorrow])
            ->orderBy('tanggal')
            ->orderBy('jam')
            ->get();
    }
}
