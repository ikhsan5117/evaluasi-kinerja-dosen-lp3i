<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\KelasMataKuliah;
use App\Models\MataKuliah;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Database\Seeder;

class KelasMataKuliahSeeder extends Seeder
{
    /**
     * Data kelas-mata kuliah-dosen-jadwal dari gambar jadwal LP3I.
     * Data di bawah adalah rekonstruksi dari foto jadwal (WhatsApp Image).
     *
     * Format setiap entri:
     * ['kelas', 'nama_matkul', 'nama_dosen', 'hari', 'waktu', 'tahun_ajaran', 'semester']
     */
    public function run(): void
    {
        // Gunakan periode Ganjil 2026/2027 sebagai periode aktif untuk jadwal ini
        $periodeAktif = Periode::where('tahun_ajaran', '2026/2027')
            ->where('semester', 'Ganjil')
            ->first();

        if (!$periodeAktif) {
            $this->command->error('❌ Periode aktif tidak ditemukan. Jalankan PeriodeSeeder terlebih dahulu.');
            return;
        }

        // Data jadwal dari gambar - AIS-24-001
        $jadwalData = [
            // === AIS-24-001 ===
            ['kelas' => 'AIS-24-001', 'matkul' => 'English for Workplace Communication', 'dosen' => 'Diki Purnomo',            'hari' => null,    'waktu' => null],
            ['kelas' => 'AIS-24-001', 'matkul' => 'Advance Accounting',                 'dosen' => 'Syafrialdi',              'hari' => null,    'waktu' => null],
            ['kelas' => 'AIS-24-001', 'matkul' => 'Financial Management',               'dosen' => 'H. Niantoro Soetrisno',   'hari' => 'Rabu',  'waktu' => '13.00 - 14.50'],
            ['kelas' => 'AIS-24-001', 'matkul' => 'Cost Accounting Practice',           'dosen' => 'Syafrialdi',              'hari' => null,    'waktu' => null],
            ['kelas' => 'AIS-24-001', 'matkul' => 'Accounting System',                  'dosen' => 'Fera Luthidarini Pranita', 'hari' => 'Kamis', 'waktu' => '08.00 - 11.40'],
            ['kelas' => 'AIS-24-001', 'matkul' => 'Tax Accounting',                     'dosen' => 'Asrial',                  'hari' => 'Rabu',  'waktu' => '18.30 - 21.40'],
            ['kelas' => 'AIS-24-001', 'matkul' => 'K3 & ISO',                           'dosen' => 'Taupiq Azhari Siregar',   'hari' => null,    'waktu' => null],
            ['kelas' => 'AIS-24-001', 'matkul' => 'Digital Literacy',                   'dosen' => 'Dian Ikha Pramayanti',   'hari' => null,    'waktu' => null],

            // === ASE-24-001 ===
            ['kelas' => 'ASE-24-001', 'matkul' => 'Framework Programming',              'dosen' => 'Anas Fajar Pratama',      'hari' => 'Sabtu', 'waktu' => '08.00 - 11.40'],
            ['kelas' => 'ASE-24-001', 'matkul' => 'Digital Literacy',                   'dosen' => 'Dian Ikha Pramayanti',   'hari' => null,    'waktu' => null],
            ['kelas' => 'ASE-24-001', 'matkul' => 'Network Security',                   'dosen' => 'Halim Fathi',             'hari' => null,    'waktu' => null],
            ['kelas' => 'ASE-24-001', 'matkul' => 'Design Graphics 2',                  'dosen' => 'Danang Purnomo',          'hari' => null,    'waktu' => null],
            ['kelas' => 'ASE-24-001', 'matkul' => 'English for Workplace Communication','dosen' => 'Diki Purnomo',            'hari' => null,    'waktu' => null],
            ['kelas' => 'ASE-24-001', 'matkul' => 'Mobile Programming',                 'dosen' => 'Joko Kristianto',         'hari' => 'Kamis', 'waktu' => '18.30 - 21.40'],
            ['kelas' => 'ASE-24-001', 'matkul' => 'System Design Analyst',              'dosen' => 'Imam Ma\'ruf Nugroho',    'hari' => null,    'waktu' => null],
            ['kelas' => 'ASE-24-001', 'matkul' => 'K3 & ISO',                           'dosen' => 'Taupiq Azhari Siregar',   'hari' => null,    'waktu' => null],

            // === OAA-24-001 ===
            ['kelas' => 'OAA-24-001', 'matkul' => 'English for General Communication 1','dosen' => 'Roby Wahyudi',           'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-001', 'matkul' => 'Stock Exchange',                     'dosen' => 'Nurfalah Nazila',         'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-001', 'matkul' => 'Industrial Psychology',               'dosen' => 'Fera Luthidarini Pranita','hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-001', 'matkul' => 'Banking and Non Banking',            'dosen' => 'Nurfalah Nazila',         'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-001', 'matkul' => 'Computer for Database',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-001', 'matkul' => 'Taxation',                            'dosen' => 'Asrial',                  'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-001', 'matkul' => 'Logistic Management',                'dosen' => 'Tita Putri Astuti',       'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-001', 'matkul' => 'K3 & ISO',                           'dosen' => 'Nisa Pamulaningtyas',     'hari' => 'Rabu',  'waktu' => '08.00 - 09.40'],
            ['kelas' => 'OAA-24-001', 'matkul' => 'Digital Literacy',                   'dosen' => 'Dadang Surya Kencana',    'hari' => 'Jumat', 'waktu' => '18.30 - 21.40'],

            // === OAA-24-002 ===
            ['kelas' => 'OAA-24-002', 'matkul' => 'English for General Communication 1','dosen' => 'Roby Wahyudi',           'hari' => 'Rabu',  'waktu' => '18.30 - 21.40'],
            ['kelas' => 'OAA-24-002', 'matkul' => 'Computer for Office 1',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-002', 'matkul' => 'Personality Development & Communication Skills','dosen' => 'Kemalia Witna Sari','hari' => null,'waktu' => null],
            ['kelas' => 'OAA-24-002', 'matkul' => 'Business Correspondence 1',          'dosen' => 'Lukmanul Ramdhan',        'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-24-002', 'matkul' => 'Accounting for Business',            'dosen' => 'Syafrialdi',              'hari' => 'Senin', 'waktu' => '18.30 - 21.40'],
            ['kelas' => 'OAA-24-002', 'matkul' => 'Modern Office Administration',       'dosen' => 'Lukmanul Ramdhan',        'hari' => 'Jumat', 'waktu' => '18.30 - 21.30'],
            ['kelas' => 'OAA-24-002', 'matkul' => 'Administration Principle',           'dosen' => 'Tita Putri Astuti',       'hari' => 'Selasa','waktu' => '18.30 - 21.40'],
            ['kelas' => 'OAA-24-002', 'matkul' => 'Human Resources Management 1',       'dosen' => 'Nurfalah Nazila',         'hari' => null,    'waktu' => null],

            // === ASE-25-001 ===
            ['kelas' => 'ASE-25-001', 'matkul' => 'English for General Communication 1','dosen' => 'Muchammad Chusnan Aprianto','hari' => 'Jumat','waktu' => '13.00 - 16.30'],
            ['kelas' => 'ASE-25-001', 'matkul' => 'Personality Development & Communication Skills','dosen' => 'R. Mochamad Daud Rizky F.','hari' => 'Selasa','waktu' => '08.00 - 11.40'],
            ['kelas' => 'ASE-25-001', 'matkul' => 'Web Design',                         'dosen' => 'Imam Fajar',              'hari' => 'Jumat', 'waktu' => '08.00 - 11.30'],
            ['kelas' => 'ASE-25-001', 'matkul' => 'Algorithm & Basic Programming',      'dosen' => 'Widyatama Fajar',         'hari' => 'Sabtu', 'waktu' => '13.00 - 16.30'],
            ['kelas' => 'ASE-25-001', 'matkul' => 'Computer for Office 1',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],
            ['kelas' => 'ASE-25-001', 'matkul' => 'Introduction to Computer Technology','dosen' => 'Joko Kristianto',         'hari' => 'Senin', 'waktu' => '13.00 - 16.40'],
            ['kelas' => 'ASE-25-001', 'matkul' => 'Computer for Office 2',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],

            // === OAA-25-001 ===
            ['kelas' => 'OAA-25-001', 'matkul' => 'English for General Communication 1','dosen' => 'Roby Wahyudi',           'hari' => 'Senin', 'waktu' => '08.00 - 11.40'],
            ['kelas' => 'OAA-25-001', 'matkul' => 'Computer for Office 1',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-25-001', 'matkul' => 'Personality Development & Communication Skills','dosen' => 'Sonny Agustian Mustofa','hari' => null,'waktu' => null],
            ['kelas' => 'OAA-25-001', 'matkul' => 'Business Correspondence 1',          'dosen' => 'Ir. Suaha Bakhtiar',      'hari' => 'Senin', 'waktu' => '08.00 - 11.30'],
            ['kelas' => 'OAA-25-001', 'matkul' => 'Accounting for Business',            'dosen' => 'Syafrialdi',              'hari' => 'Kamis', 'waktu' => '08.00 - 11.40'],
            ['kelas' => 'OAA-25-001', 'matkul' => 'Modern Office Administration',       'dosen' => 'Dian Ikha Pramayanti',   'hari' => 'Rabu',  'waktu' => '08.00 - 11.40'],
            ['kelas' => 'OAA-25-001', 'matkul' => 'Administration Principle',           'dosen' => 'Jajat Sudrajat',          'hari' => 'Rabu',  'waktu' => '13.00 - 16.40'],
            ['kelas' => 'OAA-25-001', 'matkul' => 'Human Resources Management 1',       'dosen' => 'Nurfalah Nazila',         'hari' => null,    'waktu' => null],

            // === OAA-25-002 ===
            ['kelas' => 'OAA-25-002', 'matkul' => 'English for General Communication 1','dosen' => 'Roby Wahyudi',           'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-25-002', 'matkul' => 'Computer for Office 1',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-25-002', 'matkul' => 'Personality Development & Communication Skills','dosen' => 'Kemalia Witna Sari','hari' => null,'waktu' => null],
            ['kelas' => 'OAA-25-002', 'matkul' => 'Business Correspondence 1',          'dosen' => 'Lukmanul Ramdhan',        'hari' => null,    'waktu' => null],
            ['kelas' => 'OAA-25-002', 'matkul' => 'Accounting for Business',            'dosen' => 'Syafrialdi',              'hari' => 'Senin', 'waktu' => '18.30 - 21.40'],
            ['kelas' => 'OAA-25-002', 'matkul' => 'Modern Office Administration',       'dosen' => 'Lukmanul Ramdhan',        'hari' => 'Jumat', 'waktu' => '18.30 - 21.30'],
            ['kelas' => 'OAA-25-002', 'matkul' => 'Administration Principle',           'dosen' => 'Tita Putri Astuti',       'hari' => 'Selasa','waktu' => '18.30 - 21.40'],
            ['kelas' => 'OAA-25-002', 'matkul' => 'Human Resources Management 1',       'dosen' => 'Nurfalah Nazila',         'hari' => null,    'waktu' => null],

            // === AIS-25-001 ===
            ['kelas' => 'AIS-25-001', 'matkul' => 'English for General Communication 1','dosen' => 'Muchammad Chusnan Aprianto','hari' => 'Jumat','waktu' => '13.00 - 16.30'],
            ['kelas' => 'AIS-25-001', 'matkul' => 'Computer for Office 1',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],
            ['kelas' => 'AIS-25-001', 'matkul' => 'Computer for Office 2',              'dosen' => 'Joko Kristianto',         'hari' => null,    'waktu' => null],
            ['kelas' => 'AIS-25-001', 'matkul' => 'Accounting Principle',               'dosen' => 'Fera Luthidarini Pranita','hari' => 'Kamis','waktu' => '13.00 - 16.30'],
            ['kelas' => 'AIS-25-001', 'matkul' => 'Taxation 1',                         'dosen' => 'Asrial',                  'hari' => 'Senin', 'waktu' => '13.00 - 16.30'],
            ['kelas' => 'AIS-25-001', 'matkul' => 'Basic Economic',                     'dosen' => 'Nisa Pamulaningtyas',     'hari' => 'Senin', 'waktu' => '08.00 - 09.40'],
            ['kelas' => 'AIS-25-001', 'matkul' => 'Personality Development & Communication Skills','dosen' => 'R. Mochamad Daud Rizky F.','hari' => 'Selasa','waktu' => '08.00 - 11.40'],
        ];

        // Load semua dosen & matkul ke dalam array untuk lookup cepat
        $dosenMap = User::where('role', 'dosen')
            ->with('dosen') // eager load dosen relation
            ->get()
            ->keyBy('name');

        // Map nama -> dosen_id
        $dosenIds = [];
        foreach ($dosenMap as $nama => $user) {
            if ($user->dosen) {
                $dosenIds[$nama] = $user->dosen->id;
            }
        }

        $matkuls = MataKuliah::all()->keyBy('nama_matkul');

        $counter  = 0;
        $skipped  = 0;
        $periodeId = $periodeAktif->id;

        foreach ($jadwalData as $entry) {
            $dosenId  = $dosenIds[$entry['dosen']] ?? null;
            $matkulObj = $matkuls->get($entry['matkul']);

            if (!$dosenId || !$matkulObj) {
                $this->command->warn("⚠️  Skip: Dosen '{$entry['dosen']}' atau Matkul '{$entry['matkul']}' tidak ditemukan.");
                $skipped++;
                continue;
            }

            $jadwal = null;
            if ($entry['hari'] && $entry['waktu']) {
                $jadwal = $entry['hari'] . ', ' . $entry['waktu'];
            }

            KelasMataKuliah::firstOrCreate(
                [
                    'mata_kuliah_id' => $matkulObj->id,
                    'dosen_id'       => $dosenId,
                    'periode_id'     => $periodeId,
                    'nama_kelas'     => $entry['kelas'],
                ],
                [
                    'ruangan' => null,
                    'jadwal'  => $jadwal,
                ]
            );
            $counter++;
        }

        $this->command->info("✅ Kelas Mata Kuliah seeded: {$counter} entri berhasil, {$skipped} dilewati.");
    }
}
