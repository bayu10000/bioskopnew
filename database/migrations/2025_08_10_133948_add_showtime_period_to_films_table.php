<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_add_showtime_period_to_films_table.php

    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('poster');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
