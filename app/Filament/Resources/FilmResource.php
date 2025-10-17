<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Film;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\FilmResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FilmResource\RelationManagers;

class FilmResource extends Resource
{
    protected static ?string $model = Film::class;
    protected static ?string $navigationGroup = 'Film';
    protected static ?string $navigationIcon = 'heroicon-o-film';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('judul')->required(),
                Textarea::make('sinopsis')->required(),
                TextInput::make('aktor')->required(),
                TextInput::make('sutradara')->required(),

                Select::make('genres')
                    ->relationship('genres', 'nama')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label('Genre'),

                TextInput::make('durasi')->numeric()->required(),
                TextInput::make('link_trailer')->url(),
                FileUpload::make('poster')->disk('public')->directory('posters')->image()->required(),
                DatePicker::make('tanggal_mulai')->required(),
                DatePicker::make('tanggal_selesai')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')->searchable()->sortable(),
                TextColumn::make('sinopsis')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('genres.nama')
                    ->label('Genre')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('aktor')
                    ->label('Aktor')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sutradara')
                    ->label('Sutradara')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('durasi')
                    ->label('Durasi (menit)')
                    ->sortable(),

                TextColumn::make('tanggal_mulai')
                    ->date()
                    ->label('Mulai Tayang')
                    ->sortable(),

                TextColumn::make('tanggal_selesai')
                    ->date()
                    ->label('Selesai Tayang')
                    ->sortable(),
            ])
            ->filters([
                // filter bisa ditambahkan di sini
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListFilms::route('/'),
            'create' => Pages\CreateFilm::route('/create'),
            'edit' => Pages\EditFilm::route('/{record}/edit'),
        ];
    }
}
