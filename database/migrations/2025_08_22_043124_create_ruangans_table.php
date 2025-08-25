<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            if (Schema::hasColumn('showtimes', 'ruangan')) {
                $table->dropColumn('ruangan');
            }
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn('ruangan_id');
            $table->string('ruangan')->nullable();
        });
    }
};
