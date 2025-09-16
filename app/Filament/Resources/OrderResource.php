<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Booking';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $pluralModelLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Form fields
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID Order')->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('showtime.film.judul')
                    ->label('Film')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_tiket')
                    ->label('Jumlah Tiket')
                    ->sortable(),
                Tables\Columns\TextColumn::make('seats.nomor_kursi')
                    ->label('Nomor Kursi')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'pending'   => 'warning',
                        'paid'      => 'success',
                        'cancelled' => 'danger',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal') // ✅ Mengambil langsung dari model Order
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam') // ✅ Tambahkan kolom jam
                    ->label('Jam')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn(Order $record): bool => $record->status === 'pending')
                    ->action(function (Order $record) {
                        $record->status = 'paid';
                        $record->save();
                        Notification::make()
                            ->title('Order Confirmed!')
                            ->success()
                            ->send();
                    })
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
            return $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
