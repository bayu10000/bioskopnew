<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom 'kursi' dalam format JSON
            $table->json('kursi')->nullable()->after('jumlah_tiket');
        });

        // Hapus tabel order_details
        Schema::dropIfExists('order_details');
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rollback: Hapus kolom 'kursi'
            $table->dropColumn('kursi');
        });

        // Opsional: Jika Anda ingin bisa rollback sepenuhnya, buat ulang tabel order_details
        // Tapi karena Anda ingin menghapusnya, kita abaikan saja.
    }
};
