<?php

namespace Database\Seeders;

use App\Models\Periode;
use Illuminate\Database\Seeder;

class PeriodeSeeder extends Seeder
{
    public function run(): void
    {
        $periodes = [
            ['tahun_ajaran' => '2023/2024', 'semester' => 'Ganjil',  'status' => 'Nonaktif'],
            ['tahun_ajaran' => '2023/2024', 'semester' => 'Genap',   'status' => 'Nonaktif'],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'Ganjil',  'status' => 'Nonaktif'],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'Genap',   'status' => 'Nonaktif'],
            ['tahun_ajaran' => '2025/2026', 'semester' => 'Ganjil',  'status' => 'Nonaktif'],
            ['tahun_ajaran' => '2025/2026', 'semester' => 'Genap',   'status' => 'Nonaktif'],
            ['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil',  'status' => 'Aktif'],
        ];

        foreach ($periodes as $periode) {
            Periode::firstOrCreate(
                ['tahun_ajaran' => $periode['tahun_ajaran'], 'semester' => $periode['semester']],
                ['status' => $periode['status']]
            );
        }

        $this->command->info('✅ Periode seeded: ' . count($periodes) . ' periode.');
    }
}
