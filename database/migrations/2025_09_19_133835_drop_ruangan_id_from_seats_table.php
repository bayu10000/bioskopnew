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
        // Hapus foreign key 'seats_ruangan_id_foreign'
        Schema::table('seats', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn('ruangan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambahkan kembali kolom dan foreign key jika rollback
        Schema::table('seats', function (Blueprint $table) {
            $table->foreignId('ruangan_id')->constrained('ruangans');
        });
    }
};
