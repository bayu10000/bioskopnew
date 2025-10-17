<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderDetailResource\Pages;
use App\Models\OrderDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
            Select::make('order_id')
                ->label('ID Order')
                ->relationship('order', 'id')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('seat_id')
                ->label('Nomor Kursi')
                ->relationship('seat', 'nomor_kursi')
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->label('ID Order')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.user.name') // ✅ PERBAIKAN DI SINI
                    ->label('Pelanggan')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.showtime.film.judul') // ✅ PERBAIKAN DI SINI
                    ->label('Film')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.showtime.ruangan.nama')
                    ->label('Ruangan')
                    ->sortable()
                    ->searchable(),


                Tables\Columns\TextColumn::make('order.showtime.tanggal')
                    ->label('Jadwal Tayang')
                    ->getStateUsing(function ($record) {
                        if ($record->order && $record->order->showtime) {
                            return \Carbon\Carbon::parse($record->order->showtime->tanggal)->format('d-m-Y')
                                . ' ' .
                                $record->order->showtime->jam;
                        }
                        return '-';
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('seat.nomor_kursi') // ✅ Tetap sama karena relasi langsung
                    ->label('Nomor Kursi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.status') // ✅ PERBAIKAN DI SINI
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'primary' => 'done',
                        'danger'  => 'cancelled',
                    ])
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::check() && ! Auth::user()?->hasRole('admin')) {
            return $query->whereHas('order', function (Builder $q) {
                $q->where('user_id', Auth::id());
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderDetails::route('/'),
            'create' => Pages\CreateOrderDetail::route('/create'),
            'edit' => Pages\EditOrderDetail::route('/{record}/edit'),
        ];
    }
}
