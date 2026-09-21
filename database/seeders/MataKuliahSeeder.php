<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Data mata kuliah diekstrak dari gambar jadwal.
     * Format: ['nama_matkul', 'sks', 'kode_prodi']
     */
    public function run(): void
    {
        $mataKuliahs = [
            // === OAA ===
            ['nama' => 'English for General Communication 1',           'sks' => 4, 'prodi' => 'OAA'],
            ['nama' => 'Computer for Office 1',                          'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Personality Development & Communication Skills', 'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Business Correspondence 1',                      'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Accounting for Business',                        'sks' => 4, 'prodi' => 'OAA'],
            ['nama' => 'Modern Office Administration',                   'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Administration Principle',                       'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Human Resources Management 1',                   'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Mentoring of Religion',                          'sks' => 0, 'prodi' => 'OAA'],
            ['nama' => 'Stock Exchange',                                  'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Industrial Psychology',                           'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Banking and Non Banking',                        'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Computer for Database',                          'sks' => 4, 'prodi' => 'OAA'],
            ['nama' => 'Taxation',                                        'sks' => 4, 'prodi' => 'OAA'],
            ['nama' => 'Logistic Management',                             'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'K3 & ISO',                                        'sks' => 2, 'prodi' => 'OAA'],
            ['nama' => 'Digital Literacy',                                'sks' => 2, 'prodi' => 'OAA'],

            // === AIS ===
            ['nama' => 'English for Workplace Communication',             'sks' => 2, 'prodi' => 'AIS'],
            ['nama' => 'Advance Accounting',                              'sks' => 4, 'prodi' => 'AIS'],
            ['nama' => 'Financial Management',                            'sks' => 2, 'prodi' => 'AIS'],
            ['nama' => 'Cost Accounting Practice',                        'sks' => 2, 'prodi' => 'AIS'],
            ['nama' => 'Accounting System',                               'sks' => 4, 'prodi' => 'AIS'],
            ['nama' => 'Tax Accounting',                                  'sks' => 2, 'prodi' => 'AIS'],
            ['nama' => 'English for General Communication 1',             'sks' => 4, 'prodi' => 'AIS'],
            ['nama' => 'Computer for Office 1',                           'sks' => 2, 'prodi' => 'AIS'],
            ['nama' => 'Computer for Office 2',                           'sks' => 2, 'prodi' => 'AIS'],
            ['nama' => 'Accounting Principle',                            'sks' => 4, 'prodi' => 'AIS'],
            ['nama' => 'Taxation 1',                                      'sks' => 4, 'prodi' => 'AIS'],
            ['nama' => 'Basic Economic',                                  'sks' => 2, 'prodi' => 'AIS'],
            ['nama' => 'Personality Development & Communication Skills',  'sks' => 2, 'prodi' => 'AIS'],

            // === ASE ===
            ['nama' => 'Framework Programming',                           'sks' => 4, 'prodi' => 'ASE'],
            ['nama' => 'Network Security',                                'sks' => 2, 'prodi' => 'ASE'],
            ['nama' => 'Design Graphics 2',                              'sks' => 2, 'prodi' => 'ASE'],
            ['nama' => 'Mobile Programming',                              'sks' => 4, 'prodi' => 'ASE'],
            ['nama' => 'System Design Analyst',                           'sks' => 2, 'prodi' => 'ASE'],
            ['nama' => 'English for General Communication 1',             'sks' => 4, 'prodi' => 'ASE'],
            ['nama' => 'Web Design',                                      'sks' => 4, 'prodi' => 'ASE'],
            ['nama' => 'Algorithm & Basic Programming',                   'sks' => 4, 'prodi' => 'ASE'],
            ['nama' => 'Computer for Office 1',                           'sks' => 2, 'prodi' => 'ASE'],
            ['nama' => 'Introduction to Computer Technology',             'sks' => 2, 'prodi' => 'ASE'],
            ['nama' => 'Computer for Office 2',                           'sks' => 2, 'prodi' => 'ASE'],
            ['nama' => 'Personality Development & Communication Skills',  'sks' => 2, 'prodi' => 'ASE'],
        ];

        $prodis = ProgramStudi::pluck('id', 'kode_prodi')->toArray();
        $counter = 0;

        foreach ($mataKuliahs as $index => $mk) {
            $kode = strtoupper($mk['prodi']) . '-MK-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $prodiId = $prodis[$mk['prodi']] ?? null;

            if (!$prodiId) continue;

            MataKuliah::firstOrCreate(
                ['kode_matkul' => $kode],
                [
                    'nama_matkul'       => $mk['nama'],
                    'sks'               => $mk['sks'],
                    'program_studi_id'  => $prodiId,
                    'status'            => 'Aktif',
                ]
            );
            $counter++;
        }

        $this->command->info("✅ Mata Kuliah seeded: {$counter} mata kuliah.");
    }
}
