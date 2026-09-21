<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Import data mahasiswa dari file Excel:
     * D:\application project lp3i\application project lp3i\Daftar Peserta Didik 2026.xlsx
     *
     * Sheet 2023 & 2024: kolom [No, NIPD, Nama, Kelas]
     * Sheet 2025       : kolom [No, NIPD, Nama, Jurusan, Kelas]
     */
    public function run(): void
    {
        $jsonPath = __DIR__ . '/data_mahasiswa.json';
        $prodis = ProgramStudi::pluck('id', 'kode_prodi')->toArray();

        // 1. Prioritaskan data JSON jika tersedia (agar bisa jalan di Codespaces/Server Linux)
        if (file_exists($jsonPath)) {
            $mahasiswaList = json_decode(file_get_contents($jsonPath), true) ?? [];
            $total = 0;

            foreach ($mahasiswaList as $m) {
                $user = User::firstOrCreate(
                    ['email' => $m['email']],
                    [
                        'name'     => $m['name'],
                        'role'     => 'mahasiswa',
                        'phone'    => $m['phone'] ?? null,
                        'password' => Hash::make('password123'),
                    ]
                );

                $prodiId = $prodis[$m['kode_prodi']] ?? ($prodis['OAA'] ?? 1);

                Mahasiswa::firstOrCreate(
                    ['nim' => $m['nim']],
                    [
                        'user_id'          => $user->id,
                        'program_studi_id' => $prodiId,
                        'angkatan'         => $m['angkatan'],
                        'kelas'            => $m['kelas'],
                        'status'           => $m['status'] ?? 'Aktif',
                    ]
                );
                $total++;
            }

            $this->command->info("✅ Mahasiswa seeded: {$total} mahasiswa berhasil dari JSON.");
            return;
        }

        // 2. Fallback: Parse file Excel jika JSON tidak ada
        $excelPath = 'D:\\application project lp3i\\application project lp3i\\Daftar Peserta Didik 2026.xlsx';

        if (!file_exists($excelPath)) {
            $this->command->warn("⚠️  Data mahasiswa JSON atau Excel tidak ditemukan.");
            return;
        }

        foreach ($sheets as $sheetName => $config) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);

            if (!$worksheet) {
                $this->command->warn("⚠️  Sheet '{$sheetName}' tidak ditemukan, dilewati.");
                continue;
            }

            $this->command->info("📋 Memproses sheet: {$sheetName}...");

            $rows = $worksheet->toArray(null, true, true, false);

            foreach ($rows as $rowIndex => $row) {
                // Skip header row (index 0)
                if ($rowIndex === 0) continue;

                $nipd  = trim((string) ($row[$config['nipd_col']] ?? ''));
                $nama  = trim((string) ($row[$config['nama_col']] ?? ''));
                $kelas = trim((string) ($row[$config['kelas_col']] ?? ''));

                // Skip baris kosong atau header berulang
                if (empty($nipd) || !is_numeric($nipd) || empty($nama) || empty($kelas)) continue;
                if (in_array(strtolower($nama), ['peserta didik', 'nama']) ) continue;
                if (in_array(strtolower($kelas), ['kelas', 'jurusan'])) continue;
                if (strlen($nipd) < 10) continue; // Skip formula errors

                // Tentukan kode program studi dari nama kelas
                // Kelas format: "OAA 23-001", "AIS 24-001", "ASE 25-001"
                $kelasPrefix = strtoupper(explode(' ', $kelas)[0] ?? '');

                // Kalau ada kolom prodi terpisah (sheet 2025)
                if ($config['prodi_col'] !== null) {
                    $prodiKode = strtoupper(trim((string) ($row[$config['prodi_col']] ?? '')));
                    if (empty($prodiKode) || !isset($prodis[$prodiKode])) {
                        $prodiKode = $kelasPrefix;
                    }
                } else {
                    $prodiKode = $kelasPrefix;
                }

                $prodiId = $prodis[$prodiKode] ?? null;

                if (!$prodiId) {
                    $this->command->warn("  ⚠️  Skip (prodi tidak dikenal): {$nama} | {$kelas}");
                    $skipped++;
                    continue;
                }

                // Buat email unik dari NIPD
                $email = $nipd . '@lp3i.ac.id';

                // Buat user account
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'     => ucwords(strtolower($nama)),
                        'role'     => 'mahasiswa',
                        'password' => Hash::make('password123'),
                    ]
                );

                // Format kelas untuk field mahasiswa.kelas (misal: OAA-23-001 → "OAA 23-001")
                $kelasFormatted = preg_replace('/\s+/', ' ', $kelas);

                // Buat data mahasiswa
                Mahasiswa::firstOrCreate(
                    ['nim' => $nipd],
                    [
                        'user_id'          => $user->id,
                        'program_studi_id' => $prodiId,
                        'angkatan'         => $config['angkatan'],
                        'kelas'            => $kelasFormatted,
                        'status'           => 'Aktif',
                    ]
                );

                $total++;
            }

            $this->command->info("  ✅ Sheet {$sheetName}: berhasil diproses.");
        }

        $this->command->info("✅ Mahasiswa seeded: {$total} mahasiswa berhasil, {$skipped} dilewati.");
    }
}
