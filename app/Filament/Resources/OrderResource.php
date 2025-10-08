<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
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

                Tables\Columns\TextColumn::make('qr_code_hash')
                    ->label('QR Hash')
                    ->searchable()
                    ->limit(10)
                    ->copyable()
                    ->copyMessage('QR Hash disalin!')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->dateTime('d-m-Y H:i')
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
                    ->label('Confirm as Paid')
                    ->icon('heroicon-o-currency-dollar')
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

                Tables\Actions\Action::make('viewQrCode')
                    ->label('Lihat QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->modalContent(fn(Order $record): \Illuminate\View\View => view(
                        'filament.admin.actions.order-qr-code-modal',
                        ['qrHash' => $record->qr_code_hash]
                    ))
                    ->modalHeading('QR Code Tiket')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn(Order $record): bool => $record->status === 'paid' && !empty($record->qr_code_hash)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // 💡 BARU: Bulk Action untuk Mark as Paid
                    Tables\Actions\BulkAction::make('markSelectedAsPaid')
                        ->label('Konfirmasi Pembayaran (Massal)')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Pembayaran Semua Pesanan yang Dipilih?')
                        ->modalDescription('Tindakan ini akan mengubah status semua pesanan yang dipilih menjadi **PAID**. Tindakan tidak dapat diurungkan.')
                        ->action(function (Collection $records) {
                            // Hitung jumlah pesanan yang statusnya 'pending'
                            $pendingCount = $records->where('status', 'pending')->count();

                            // Ubah status hanya untuk yang masih 'pending'
                            $records->where('status', 'pending')->each(function (Order $record) {
                                $record->status = 'paid';
                                $record->save();
                            });

                            if ($pendingCount > 0) {
                                Notification::make()
                                    ->title('Konfirmasi Massal Berhasil!')
                                    ->body("{$pendingCount} pesanan berhasil diubah statusnya menjadi PAID.")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Tidak Ada Perubahan')
                                    ->body('Semua pesanan yang dipilih sudah PAID atau CANCELLED.')
                                    ->warning()
                                    ->send();
                            }
                        }),

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
