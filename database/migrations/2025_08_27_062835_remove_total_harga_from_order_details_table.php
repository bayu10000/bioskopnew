<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Periksa apakah kolom 'total_harga' ada sebelum mencoba menghapusnya
        if (Schema::hasColumn('order_details', 'total_harga')) {
            Schema::table('order_details', function (Blueprint $table) {
                $table->dropColumn('total_harga');
            });
        }
    }

    public function down(): void
    {
        // Periksa apakah kolom 'total_harga' tidak ada sebelum menambahkannya kembali
        if (!Schema::hasColumn('order_details', 'total_harga')) {
            Schema::table('order_details', function (Blueprint $table) {
                $table->string('total_harga')->nullable(); // Menambahkannya kembali jika di-rollback
            });
        }
    }
};
