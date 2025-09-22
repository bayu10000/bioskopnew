<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('sinopsis');

            $table->integer('durasi'); // dalam menit
            $table->string('poster')->nullable(); // path file poster
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
