<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuesioner_id')->constrained('kuesioner')->onDelete('cascade');
            $table->text('teks_pertanyaan');
            $table->enum('kategori', ['Pedagogik', 'Profesional', 'Kepribadian', 'Sosial'])->default('Pedagogik');
            $table->integer('nomor_urut')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan');
    }
};
