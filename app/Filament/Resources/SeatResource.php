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
        return $form
            ->schema([
                Select::make('showtime_id')
                    ->label('Jadwal Tayang')
                    ->options(
                        Showtime::with('film')
                            ->get()
                            ->mapWithKeys(fn($s) => [
                                $s->id => $s->tanggal . ' - ' . ($s->film?->judul ?? 'Tanpa Film')
                            ])
                    )
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $showtime = Showtime::with(['film', 'ruangan'])->find($state);
                        $set('film_name', $showtime?->film?->judul);
                        $set('room_name', $showtime?->ruangan?->nama);
                    }),

                TextInput::make('film_name')
                    ->label('Nama Film')
                    ->disabled()
                    ->dehydrated(false), // tidak disimpan ke DB

                TextInput::make('room_name')
                    ->label('Ruangan')
                    ->disabled()
                    ->dehydrated(false), // tidak disimpan ke DB

                TextInput::make('nomor_kursi')
                    ->label('Nomor Kursi')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('showtime.film.judul')
                    ->label('Film')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('showtime.ruangan.nama')
                    ->label('Ruangan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('showtime.tanggal')
                    ->label('Tanggal Tayang')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nomor_kursi')
                    ->label('Nomor Kursi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [];
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
