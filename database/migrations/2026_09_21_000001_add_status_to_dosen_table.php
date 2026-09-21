<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            if (!Schema::hasColumn('dosen', 'status')) {
                $table->string('status', 20)->default('Aktif')->after('jabatan_fungsional');
            }
        });

        Schema::table('mata_kuliah', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_kuliah', 'status')) {
                $table->string('status', 20)->default('Aktif')->after('program_studi_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            if (Schema::hasColumn('dosen', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('mata_kuliah', function (Blueprint $table) {
            if (Schema::hasColumn('mata_kuliah', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
