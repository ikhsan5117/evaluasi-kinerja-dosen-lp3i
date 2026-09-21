<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DosenSeeder extends Seeder
{
    /**
     * Data dosen diekstrak dari gambar jadwal (WhatsApp Image 2026-09-13).
     * Format: ['nama_lengkap', 'gelar', 'kode_prodi', 'nidn_dummy']
     */
    public function run(): void
    {
        // Daftar dosen unik dari gambar jadwal
        // Nama + gelar dipisah untuk mengisi kolom dosen.gelar
        $dosenList = [
            // OAA
            ['nama' => 'Roby Wahyudi',          'gelar' => 'M.Pd',          'prodi' => 'OAA'],
            ['nama' => 'Nurfalah Nazila',        'gelar' => 'M.M',           'prodi' => 'OAA'],
            ['nama' => 'Fera Luthidarini Pranita','gelar' => 'S.E., M.M.',   'prodi' => 'OAA'],
            ['nama' => 'Joko Kristianto',        'gelar' => 'S.T.',          'prodi' => 'OAA'],
            ['nama' => 'Asrial',                 'gelar' => 'S.E., M.M.',    'prodi' => 'OAA'],
            ['nama' => 'Nisa Pamulaningtyas',    'gelar' => 'S.E., M.M.',    'prodi' => 'OAA'],
            ['nama' => 'Dadang Surya Kencana',   'gelar' => 'S.E., M.M.',    'prodi' => 'OAA'],
            ['nama' => 'Tita Putri Astuti',      'gelar' => 'M.M',           'prodi' => 'OAA'],
            ['nama' => 'Syafrialdi',             'gelar' => 'B.Ac., S.E',    'prodi' => 'OAA'],
            ['nama' => 'Jajat Sudrajat',         'gelar' => 'S.Pd., M.M., Ph.D', 'prodi' => 'OAA'],
            ['nama' => 'Sonny Agustian Mustofa', 'gelar' => 'S.E., A.Kt.',   'prodi' => 'OAA'],
            ['nama' => 'Ir. Suaha Bakhtiar',     'gelar' => 'M.M',           'prodi' => 'OAA'],
            ['nama' => 'Lukmanul Ramdhan',       'gelar' => 'A.Md., AB',     'prodi' => 'OAA'],
            ['nama' => 'Dian Ikha Pramayanti',   'gelar' => 'S.Pt., M.Si',   'prodi' => 'OAA'],
            ['nama' => 'Kemalia Witna Sari',     'gelar' => 'M.Ikom',        'prodi' => 'OAA'],

            // AIS
            ['nama' => 'Diki Purnomo',           'gelar' => 'S.Sos',         'prodi' => 'AIS'],
            ['nama' => 'H. Niantoro Soetrisno',  'gelar' => 'S.Ab., M.M.',   'prodi' => 'AIS'],
            ['nama' => 'Taupiq Azhari Siregar',  'gelar' => 'A.Md',          'prodi' => 'AIS'],
            ['nama' => 'Muchammad Chusnan Aprianto', 'gelar' => 'S.Si., M.Sc', 'prodi' => 'AIS'],
            ['nama' => 'R. Mochamad Daud Rizky F.', 'gelar' => 'S.Kom',      'prodi' => 'AIS'],

            // ASE
            ['nama' => 'Anas Fajar Pratama',     'gelar' => 'S.Kom.',        'prodi' => 'ASE'],
            ['nama' => 'Halim Fathi',             'gelar' => null,            'prodi' => 'ASE'],
            ['nama' => 'Danang Purnomo',          'gelar' => 'S.St., M.Kom.', 'prodi' => 'ASE'],
            ['nama' => 'Imam Fajar',              'gelar' => 'M.Kom.',        'prodi' => 'ASE'],
            ['nama' => 'Widyatama Fajar',         'gelar' => 'S.Kom.',        'prodi' => 'ASE'],
            ['nama' => 'Imam Ma\'ruf Nugroho',    'gelar' => 'M.Kom.',        'prodi' => 'ASE'],
        ];

        $prodis = ProgramStudi::pluck('id', 'kode_prodi')->toArray();

        foreach ($dosenList as $index => $data) {
            $namaSlug = Str::slug($data['nama'], '.');
            $email = $namaSlug . '@lp3i.ac.id';

            // Pastikan email unik
            $emailCount = User::where('email', 'like', $namaSlug . '%@lp3i.ac.id')->count();
            if ($emailCount > 0 && !User::where('email', $email)->exists()) {
                $email = $namaSlug . '.' . ($emailCount + 1) . '@lp3i.ac.id';
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => $data['nama'],
                    'role'     => 'dosen',
                    'password' => Hash::make('password123'),
                ]
            );

            // Generate NIDN dummy (10 digit)
            $nidn = str_pad($index + 1, 10, '0', STR_PAD_LEFT);

            $prodiId = $prodis[$data['prodi']] ?? $prodis['OAA'];

            Dosen::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nidn'              => $nidn,
                    'gelar'             => $data['gelar'],
                    'program_studi_id'  => $prodiId,
                    'jabatan_fungsional'=> 'Tenaga Pengajar',
                ]
            );
        }

        $this->command->info('✅ Dosen seeded: ' . count($dosenList) . ' dosen.');
    }
}
