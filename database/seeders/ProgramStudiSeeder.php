<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        $prodis = [
            ['kode_prodi' => 'OAA', 'nama_prodi' => 'Otomatisasi dan Akuntansi', 'jenjang' => 'D3'],
            ['kode_prodi' => 'AIS', 'nama_prodi' => 'Akuntansi dan Informatika', 'jenjang' => 'D3'],
            ['kode_prodi' => 'ASE', 'nama_prodi' => 'Administrasi Sistem Elektronika', 'jenjang' => 'D3'],
        ];

        foreach ($prodis as $prodi) {
            ProgramStudi::firstOrCreate(
                ['kode_prodi' => $prodi['kode_prodi']],
                $prodi
            );
        }

        $this->command->info('✅ Program Studi seeded: ' . count($prodis) . ' prodi.');
    }
}
