<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('kelas_mata_kuliah_id')->constrained('kelas_mata_kuliah')->onDelete('cascade');
            $table->foreignId('kuesioner_id')->constrained('kuesioner')->onDelete('cascade');
            $table->date('tanggal_pengisian');
            $table->text('saran_masukan')->nullable();
            $table->enum('status', ['Selesai', 'Draft'])->default('Selesai');
            $table->timestamps();

            // Memastikan mahasiswa hanya mengisi 1 kali untuk kelas matkul + kuesioner yang sama
            $table->unique(['mahasiswa_id', 'kelas_mata_kuliah_id', 'kuesioner_id'], 'evaluasi_unique_mhs_kelas_kues');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi');
    }
};
