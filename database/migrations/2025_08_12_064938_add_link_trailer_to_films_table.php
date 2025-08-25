<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->string('link_trailer')->nullable()->after('durasi');
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn('link_trailer');
        });
    }
};
