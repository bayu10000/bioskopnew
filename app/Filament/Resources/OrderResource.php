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
// 💡 BARU: Import OrderController untuk memanggil fungsi pembatalan
use App\Http\Controllers\OrderController;

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
                Tables\Columns\TextColumn::make('showtime.ruangan.nama')
                    ->label('Ruangan')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah_tiket')
                    ->label('Jumlah Tiket')
                    ->sortable(),
                Tables\Columns\TextColumn::make('seats.nomor_kursi')
                    ->label('Nomor Kursi')
                    ->badge()
                    ->sortable(),

                // 💡 BARU: Tambahkan Kolom qr_code_hash untuk referensi cepat/pencarian
                Tables\Columns\TextColumn::make('qr_code_hash')
                    ->label('QR Hash')
                    ->searchable()
                    ->limit(10) // Tampilkan hanya 10 karakter pertama
                    ->copyable()
                    ->copyMessage('QR Hash disalin!')
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan secara default

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'pending'   => 'warning',
                        'paid'      => 'success',
                        'cancelled' => 'danger',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Pembelian')
                    ->dateTime('d-m-Y H:i') // contoh: 22-09-2025 14:35
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(Order $record): bool => $record->status !== 'paid')
                    ->modalDescription('Menghapus pesanan ini (kecuali paid) tidak membebaskan kursi secara otomatis. Gunakan "Batalkan Pesanan" untuk membebaskan kursi.'),


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
                    }),

                // 💡 BARU: Action Pembatalan untuk Admin (Menggunakan helper dari Controller)
                Tables\Actions\Action::make('cancelOrder')
                    ->label('Batalkan Pesanan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pesanan?')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan pesanan ini? Kursi akan **dibebaskan (available)** dan detail kursi **tetap tersimpan** sebagai riwayat.')
                    ->visible(fn(Order $record): bool => in_array($record->status, ['pending', 'paid']))
                    ->action(function (Order $record) {
                        // Panggil fungsi helper statis
                        if (OrderController::releaseSeats($record)) {
                            Notification::make()
                                ->title('Pesanan Dibatalkan')
                                ->body('Kursi telah dibebaskan dan status pesanan #' . $record->id . ' diubah menjadi DIBATALKAN.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal Membatalkan')
                                ->body('Terjadi kesalahan saat memproses pembatalan dan membebaskan kursi.')
                                ->danger()
                                ->send();
                        }
                    }),

                // 💡 Action untuk menampilkan QR Code
                Tables\Actions\Action::make('viewQrCode')
                    ->label('Lihat QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->modalContent(fn(Order $record): \Illuminate\View\View => view(
                        'filament.admin.actions.order-qr-code-modal',
                        ['qrHash' => $record->qr_code_hash]
                    ))
                    ->modalHeading('QR Code Tiket')
                    ->modalSubmitAction(false) // Hilangkan tombol Submit
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn(Order $record): bool => $record->status === 'paid' && !empty($record->qr_code_hash)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Sebaiknya tidak menggunakan DeleteBulkAction untuk pesanan PAID
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ... (metode getEloquentQuery dan getPages tetap sama) ...
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
