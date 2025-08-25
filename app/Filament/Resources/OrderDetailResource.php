<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderDetailResource\Pages;
use App\Models\OrderDetail;
use App\Models\Showtime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderDetailResource extends Resource
{
    protected static ?string $model = OrderDetail::class;

    protected static ?string $navigationGroup = 'Booking';
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'Order Details';
    protected static ?string $pluralModelLabel = 'Order Details';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('order_id')
                ->label('Order')
                ->relationship('order', 'id')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('showtime_id')
                ->label('Showtime')
                ->relationship('showtime', 'id')
                ->getOptionLabelFromRecordUsing(function (Showtime $record) {
                    // label: 2025-08-12 • 19:00 • Judul Film
                    $film = $record->film ? $record->film->judul : '-';
                    return "{$record->tanggal} • {$record->jam} • {$film}";
                })
                ->searchable()
                ->preload()
                ->required(),

            Select::make('seat_id')
                ->label('Seat')
                ->relationship('seat', 'nomor_kursi')
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')
            ->label('User')
            ->sortable(),

            Tables\Columns\TextColumn::make('showtime.tanggal')
                ->label('Tanggal')
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('showtime.jam')
                ->label('Jam')
                ->sortable(),

            Tables\Columns\TextColumn::make('seat.nomor_kursi')
                ->label('Nomor Kursi')
                ->sortable(),

            Tables\Columns\TextColumn::make('showtime.film.judul')
                ->label('Film')
                ->sortable()
              
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
            'index'  => Pages\ListOrderDetails::route('/'),
            'create' => Pages\CreateOrderDetail::route('/create'),
            'edit'   => Pages\EditOrderDetail::route('/{record}/edit'),
        ];
    }
}
