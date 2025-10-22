<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            // 1. Hapus foreign key lama (wajib, agar bisa menambahkan yang baru)
            $table->dropForeign(['showtime_id']);

            // 2. Tambahkan foreign key baru dengan ON DELETE CASCADE
            $table->foreign('showtime_id')
                ->references('id')
                ->on('showtimes')
                ->onDelete('cascade'); // ✅ INI KUNCINYA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropForeign(['showtime_id']);
            $table->foreign('showtime_id')
                ->references('id')
                ->on('showtimes');
        });
    }
};
