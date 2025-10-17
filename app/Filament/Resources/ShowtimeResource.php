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
use Illuminate\Validation\Rules\Unique;
use Carbon\Carbon;

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
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('tanggal', null))
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('ruangan_id', $get('ruangan_id'))
                            ->where('tanggal', $get('tanggal'))
                            ->where('jam', $get('jam'));
                    }),

                Select::make('ruangan_id')
                    ->label('Ruangan')
                    ->relationship('ruangan', 'nama')
                    ->required()
                    ->preload()
                    ->live(),

                DatePicker::make('tanggal')
                    ->label('Tanggal Tayang')
                    ->required()
                    ->live()
                    ->minDate(function (Get $get) {
                        $filmId = $get('film_id');
                        if ($filmId) {
                            $film = Film::find($filmId);
                            return $film ? Carbon::parse($film->tanggal_mulai) : null;
                        }
                        return null;
                    })
                    ->maxDate(function (Get $get) {
                        $filmId = $get('film_id');
                        if ($filmId) {
                            $film = Film::find($filmId);
                            return $film ? Carbon::parse($film->tanggal_selesai) : null;
                        }
                        return null;
                    }),

                Select::make('jam')
                    ->label('Jam Tayang')
                    ->required()
                    ->options([
                        '10:00' => '10:00',
                        '16:00' => '16:00',
                        '19:00' => '19:00',
                        '22:00' => '22:00',
                    ])
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('ruangan_id', $get('ruangan_id'))
                            ->where('film_id', $get('film_id'))
                            ->where('tanggal', $get('tanggal'));
                    }),


                TextInput::make('harga')
                    ->label('Harga Tiket')
                    ->required()
                    ->prefix('Rp')
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

    // ... (Bagian tengah file)

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // 💡 FILTER: Hanya tampilkan jadwal tayang yang belum terlewat.
        $currentDateTime = Carbon::now();

        // Gabungkan tanggal dan jam menjadi satu timestamp untuk perbandingan.
        // Gunakan where() dengan klausa raw untuk membandingkan secara akurat.
        $query->whereRaw("CONCAT(tanggal, ' ', jam) > ?", [$currentDateTime->format('Y-m-d H:i:s')]);
        // Menggunakan '>' alih-alih '>=' untuk menghilangkan jadwal yang sudah dimulai/sudah terlewat 1 detik.
        // Jika Anda ingin jadwal tetap terlihat selama jam tayang, gunakan '>='.

        return $query;
    }

    // ... (Bagian bawah file)
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
