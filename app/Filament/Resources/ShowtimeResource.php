<?php

namespace App\Filament\Resources;

use App\Models\Film;
use App\Models\Showtime;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Ruangan;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ShowtimeResource\Pages;
use Illuminate\Validation\Rules\Unique; // ✅ Import Unique Rule

class ShowtimeResource extends Resource
{
    protected static ?string $model = Showtime::class;
    protected static ?string $navigationGroup = 'Seat And Date';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('film_id')
                    ->label('Film')
                    ->relationship('film', 'judul')
                    ->required()
                    ->live()
                    ->preload()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('ruangan_id', $get('ruangan_id'))
                            ->where('tanggal', $get('tanggal'))
                            ->where('jam', $get('jam'));
                    }),

                Select::make('ruangan_id')
                    ->label('Ruangan')
                    ->relationship('ruangan', 'nama')
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('film_id', $get('film_id'))
                            ->where('tanggal', $get('tanggal'))
                            ->where('jam', $get('jam'));
                    }),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('film_id', $get('film_id'))
                            ->where('ruangan_id', $get('ruangan_id'))
                            ->where('jam', $get('jam'));
                    })
                    ->minDate(function (Get $get) {
                        $film = Film::find($get('film_id'));
                        return $film ? $film->tanggal_mulai : now();
                    })
                    ->maxDate(function (Get $get) {
                        $film = Film::find($get('film_id'));
                        return $film ? $film->tanggal_selesai : null;
                    }),

                TimePicker::make('jam')
                    ->label('Jam Tayang')
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('film_id', $get('film_id'))
                            ->where('ruangan_id', $get('ruangan_id'))
                            ->where('tanggal', $get('tanggal'));
                    }),

                TextInput::make('harga')
                    ->label('Harga Tiket')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('film.judul')
                    ->label('Judul Film')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ruangan.nama')
                    ->label('Ruangan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam'),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShowtimes::route('/'),
            'create' => Pages\CreateShowtime::route('/create'),
            'edit' => Pages\EditShowtime::route('/{record}/edit'),
        ];
    }
}
