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
        // Metode 'up' digunakan untuk menambahkan kolom
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom 'username'
            // String dengan panjang default 255
            // Harus unik (tidak boleh ada dua user dengan username yang sama)
            // Ditempatkan setelah kolom 'name'
            $table->string('username')->unique()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Metode 'down' digunakan untuk menghapus kolom jika migrasi di-rollback
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom 'username'
            $table->dropColumn('username');
        });
    }
};
