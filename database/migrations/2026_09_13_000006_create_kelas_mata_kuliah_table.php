<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periode')->onDelete('cascade');
            $table->string('nama_kelas', 50); // e.g. TI-2A, MI-1B
            $table->string('ruangan', 50)->nullable();
            $table->string('jadwal', 100)->nullable(); // e.g. Senin, 08:00 - 10:00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_mata_kuliah');
    }
};
