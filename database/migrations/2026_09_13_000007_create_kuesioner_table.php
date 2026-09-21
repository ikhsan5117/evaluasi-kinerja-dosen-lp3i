<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuesioner', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->default('Evaluasi Kinerja Dosen'); // Pedagogik, Profesional, Kepribadian, Sosial / Umum
            $table->foreignId('periode_id')->constrained('periode')->onDelete('cascade');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuesioner');
    }
};
