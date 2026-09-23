<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        $prodis = [
            ['kode_prodi' => 'ASE', 'nama_prodi' => 'Application Software Engineering', 'jenjang' => 'D3'],
            ['kode_prodi' => 'OAA', 'nama_prodi' => 'Office Administration Automatization', 'jenjang' => 'D3'],
            ['kode_prodi' => 'AIS', 'nama_prodi' => 'Accounting Information System', 'jenjang' => 'D3'],
        ];

        foreach ($prodis as $prodi) {
            ProgramStudi::updateOrCreate(
                ['kode_prodi' => $prodi['kode_prodi']],
                $prodi
            );
        }

        $this->command->info('✅ Program Studi seeded: ' . count($prodis) . ' prodi.');
    }
}
