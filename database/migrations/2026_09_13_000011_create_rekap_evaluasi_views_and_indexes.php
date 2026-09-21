<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan index untuk performa pencarian dan relasi kuesioner
        Schema::table('evaluasi', function (Blueprint $table) {
            $table->index('kelas_mata_kuliah_id', 'idx_eval_kelas');
            $table->index('kuesioner_id', 'idx_eval_kuesioner');
        });

        Schema::table('jawaban_kuesioner', function (Blueprint $table) {
            $table->index('evaluasi_id', 'idx_jawaban_eval');
            $table->index('pertanyaan_id', 'idx_jawaban_pertanyaan');
            $table->index('skor', 'idx_jawaban_skor');
        });

        Schema::table('pertanyaan', function (Blueprint $table) {
            $table->index(['kuesioner_id', 'kategori'], 'idx_pertanyaan_kues_kat');
        });

        // 2. Buat Database View untuk rekapitulasi evaluasi kelas otomatis di level database
        DB::statement("DROP VIEW IF EXISTS view_rekap_evaluasi_kelas");
        DB::statement("
            CREATE VIEW view_rekap_evaluasi_kelas AS
            SELECT 
                kmk.id AS kelas_mata_kuliah_id,
                kmk.dosen_id,
                kmk.mata_kuliah_id,
                kmk.periode_id,
                kmk.nama_kelas,
                COUNT(DISTINCT e.id) AS total_responden,
                AVG(CASE WHEN p.kategori IN ('Pedagogik', 'Metode Pembelajaran') THEN jk.skor ELSE NULL END) AS avg_pedagogik,
                AVG(CASE WHEN p.kategori = 'Profesional' THEN jk.skor ELSE NULL END) AS avg_profesional,
                AVG(CASE WHEN p.kategori = 'Kepribadian' THEN jk.skor ELSE NULL END) AS avg_kepribadian,
                AVG(CASE WHEN p.kategori = 'Sosial' THEN jk.skor ELSE NULL END) AS avg_sosial,
                AVG(jk.skor) AS avg_total
            FROM kelas_mata_kuliah kmk
            LEFT JOIN evaluasi e ON e.kelas_mata_kuliah_id = kmk.id
            LEFT JOIN jawaban_kuesioner jk ON jk.evaluasi_id = e.id
            LEFT JOIN pertanyaan p ON p.id = jk.pertanyaan_id
            GROUP BY kmk.id, kmk.dosen_id, kmk.mata_kuliah_id, kmk.periode_id, kmk.nama_kelas
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_rekap_evaluasi_kelas");

        Schema::table('evaluasi', function (Blueprint $table) {
            $table->dropIndex('idx_eval_kelas');
            $table->dropIndex('idx_eval_kuesioner');
        });

        Schema::table('jawaban_kuesioner', function (Blueprint $table) {
            $table->dropIndex('idx_jawaban_eval');
            $table->dropIndex('idx_jawaban_pertanyaan');
            $table->dropIndex('idx_jawaban_skor');
        });

        Schema::table('pertanyaan', function (Blueprint $table) {
            $table->dropIndex('idx_pertanyaan_kues_kat');
        });
    }
};
