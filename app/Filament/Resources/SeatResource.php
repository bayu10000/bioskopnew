<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeatResource\Pages;
use App\Models\Seat;
use App\Models\Showtime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class SeatResource extends Resource
{
    protected static ?string $model = Seat::class;
    protected static ?string $navigationGroup = 'Seat And Date';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('showtime_id')
                ->label('Jadwal Tayang')
                ->relationship('showtime', 'id')
                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->tanggal} {$record->jam} - {$record->film->judul} ({$record->ruangan->nama})")
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('nomor_kursi')
                ->label('Nomor Kursi')
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options(['available' => 'Available', 'booked' => 'Booked'])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('showtime.film.judul')
                ->label('Film')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('showtime.ruangan.nama')
                ->label('Ruangan')
                ->sortable(),

            Tables\Columns\TextColumn::make('showtime.tanggal')
                ->label('Tanggal')
                ->sortable(),

            Tables\Columns\TextColumn::make('showtime.jam')
                ->label('Jam')
                ->sortable(),

            Tables\Columns\TextColumn::make('nomor_kursi')
                ->label('Nomor Kursi')
                ->searchable(),

            Tables\Columns\TextColumn::make('status')
                ->badge(),
        ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeats::route('/'),
            'create' => Pages\CreateSeat::route('/create'),
            'edit' => Pages\EditSeat::route('/{record}/edit'),
        ];
    }
}
