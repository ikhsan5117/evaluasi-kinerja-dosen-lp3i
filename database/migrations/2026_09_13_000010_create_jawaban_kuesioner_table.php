<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluasi_id')->constrained('evaluasi')->onDelete('cascade');
            $table->foreignId('pertanyaan_id')->constrained('pertanyaan')->onDelete('cascade');
            $table->unsignedTinyInteger('skor'); // 1, 2, 3, 4, 5
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['evaluasi_id', 'pertanyaan_id'], 'jawaban_unique_eval_pertanyaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_kuesioner');
    }
};
