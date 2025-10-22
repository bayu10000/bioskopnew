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
use Filament\Notifications\Notification; // 💡 Tambahkan ini

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
                            ->where('jam', $get('jam')); // Pastikan kombinasi unik film, ruangan, tanggal, jam
                    }),

                Select::make('ruangan_id')
                    ->label('Ruangan')
                    ->relationship('ruangan', 'nama')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('tanggal', null))
                    ->preload(),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->minDate(now()) // Hanya boleh tanggal hari ini dan ke depan
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('jam', null)),

                TimePicker::make('jam')
                    ->label('Jam Tayang')
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('ruangan_id', $get('ruangan_id'))
                            ->where('film_id', $get('film_id'))
                            ->where('tanggal', $get('tanggal'));
                    }),

                TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->required()
                    ->prefix('IDR'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('film.judul')
                    ->label('Film')
                    ->sortable()
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

                    // 💡 FUNGSI BARU: Bulk Action untuk membersihkan jadwal yang sudah lewat
                    Tables\Actions\BulkAction::make('clearOldShowtimes')
                        ->label('Hapus Jadwal Lewat (Otomatis Hapus Kursi)')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Penghapusan Jadwal Lewat')
                        ->modalDescription('Tindakan ini akan menghapus SEMUA jadwal tayang yang **waktu tayangnya sudah terlewat** dan **Kursi (Seats) yang berelasi akan ikut terhapus**. Lanjutkan?')
                        ->action(function () {
                            $currentDateTime = Carbon::now();
                            // Hapus semua yang waktu gabungannya sudah lewat dari sekarang
                            $deletedCount = \App\Models\Showtime::whereRaw("CONCAT(tanggal, ' ', jam) < ?", [$currentDateTime->format('Y-m-d H:i:s')])
                                ->delete();

                            Notification::make()
                                ->title('Pembersihan Berhasil!')
                                ->body("{$deletedCount} jadwal tayang yang sudah lewat berhasil dihapus. Kursi terkait otomatis ikut terhapus.")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // 💡 FILTER: Hanya tampilkan jadwal tayang yang belum terlewat.
        $currentDateTime = Carbon::now();

        // Gabungkan tanggal dan jam menjadi satu timestamp untuk perbandingan.
        // Hanya tampilkan yang MASA DEPAN (lebih besar dari sekarang).
        $query->whereRaw("CONCAT(tanggal, ' ', jam) > ?", [$currentDateTime->format('Y-m-d H:i:s')]);

        return $query;
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
