<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RekapResource\Pages;
use App\Models\Rekap;
use App\Models\Film;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Number;

class RekapResource extends Resource
{
    protected static ?string $model = Rekap::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    // protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Rekap Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('film_id')
                    ->label('Film')
                    ->options(Film::pluck('judul', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required(),

                Forms\Components\TextInput::make('ruangan')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TimePicker::make('jam_tayang')
                    ->label('Jam Tayang')
                    ->required(),

                Forms\Components\TextInput::make('total_tiket_terjual')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\TextInput::make('total_pendapatan')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('film.judul')
                    ->label('Film')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jam_tayang')
                    ->label('Jam Tayang'),
                Tables\Columns\TextColumn::make('total_tiket_terjual')
                    ->label('Tiket Terjual')
                    ->numeric()
                    ->sortable()
                    // 💡 Tambahkan sum untuk footer kolom ini
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('TOTAL')
                            // ->money(null, true)
                            ->numeric() // Hilangkan format mata uang di sini
                    ),
                Tables\Columns\TextColumn::make('total_pendapatan')
                    ->label('Total Pendapatan')
                    ->money('IDR', true)
                    ->sortable()
                    // 💡 Tambahkan sum untuk footer kolom ini
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('TOTAL')
                            ->money('IDR', true)
                    ),
            ])
            ->filters([
                // Filter Tanggal
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->placeholder(fn($state): string => 'Pilih Tanggal Mulai'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->placeholder(fn($state): string => 'Pilih Tanggal Selesai'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),

                // Filter Film
                Tables\Filters\SelectFilter::make('film_id')
                    ->label('Film')
                    ->options(Film::pluck('judul', 'id')->toArray())
                    ->searchable()
                    ->multiple(),
            ])
            ->headerActions([
                // (Dikosongkan sesuai permintaan auto-sync)
            ])
            ->defaultSort('tanggal', 'desc');
        // ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekaps::route('/'),
            'create' => Pages\CreateRekap::route('/create'),
            'edit' => Pages\EditRekap::route('/{record}/edit'),
        ];
    }
}
