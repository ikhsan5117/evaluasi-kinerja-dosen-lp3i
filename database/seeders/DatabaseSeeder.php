<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // Buat akun Admin utama terlebih dahulu
        // ============================================================
        User::updateOrCreate(
            ['email' => 'admin@lp3i.ac.id'],
            [
                'name'     => 'Administrator LP3I',
                'role'     => 'admin',
                'password' => Hash::make('admin123#'),
            ]
        );

        $this->command->info('✅ Admin user created: admin@lp3i.ac.id | password: admin123#');

        // ============================================================
        // Jalankan semua seeder secara berurutan (dependency order)
        // ============================================================
        $this->call([
            ProgramStudiSeeder::class,      // 1. Program Studi (OAA, AIS, ASE)
            PeriodeSeeder::class,            // 2. Periode semester
            MataKuliahSeeder::class,        // 3. Mata Kuliah (dari gambar jadwal)
            DosenSeeder::class,             // 4. Dosen + User dosen (dari gambar jadwal)
            KelasMataKuliahSeeder::class,   // 5. Relasi Kelas - Matkul - Dosen
            MahasiswaSeeder::class,         // 6. Mahasiswa dari Excel (semua angkatan)
            KuesionerSeeder::class,         // 7. Kuesioner & 15 Butir Pertanyaan Evaluasi
        ]);

        $this->command->info('');
        $this->command->info('🎉 Semua data berhasil diimport ke database!');
        $this->command->info('   Login admin: admin@lp3i.ac.id | admin123# (atau admin)');
        $this->command->info('   Login dosen: [nama]@lp3i.ac.id | password123');
        $this->command->info('   Login mahasiswa: [nipd]@lp3i.ac.id | password123');
    }
}
