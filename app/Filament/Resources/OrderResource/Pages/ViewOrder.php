<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pesanan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')->label('ID Order'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'cancelled' => 'danger',
                                    }),
                                TextEntry::make('user.name')->label('Nama Pelanggan'),
                                TextEntry::make('jumlah_tiket')->label('Jumlah Tiket'),
                            ]),
                        TextEntry::make('total_harga')->label('Total Harga')->money('IDR'),
                    ]),

                Section::make('Informasi Jadwal Tayang')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('showtime.film.judul')->label('Judul Film'),
                                TextEntry::make('showtime.ruangan')->label('Ruangan'),
                                TextEntry::make('showtime.tanggal')->label('Tanggal')->date(),
                                TextEntry::make('showtime.jam')->label('Jam'),
                            ]),
                    ]),

                Section::make('Detail Kursi')
                    ->schema([
                        RepeatableEntry::make('seats')
                            ->schema([
                                TextEntry::make('nomor_kursi')
                                    ->label('Nomor Kursi')
                                    ->badge(),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
