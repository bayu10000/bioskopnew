<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Booking';

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $pluralModelLabel = 'Orders';
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('user_name')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

                DatePicker::make('tanggal')->required(),
                TimePicker::make('jam')->required(),
            TextInput::make('total_harga')
                ->numeric()
                ->required(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'cancelled' => 'Cancelled',
                ])
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
    
                Tables\Columns\TextColumn::make('showtime.film.judul')
                    ->label('Film')
                    ->sortable(),
    
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total harga')
                    ->money('IDR')
                    ->sortable(),
    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
    
    
    
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
