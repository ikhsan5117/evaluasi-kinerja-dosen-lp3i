<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\KelasMataKuliah;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Periode;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Services\ExcelTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MasterDataImportController extends Controller
{
    protected ExcelTemplateService $templateService;

    public function __construct(ExcelTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Download Excel template for master data.
     */
    public function downloadTemplate(string $type)
    {
        $validTypes = ['mahasiswa', 'dosen', 'matkul', 'kelas', 'periode', 'pertanyaan'];
        if (!in_array($type, $validTypes)) {
            abort(404, 'Tipe template tidak valid.');
        }

        return $this->templateService->download($type);
    }

    /**
     * Import Data Mahasiswa from Excel file.
     * Supports both multi-sheet (e.g. 2023, 2024, 2025) and single-sheet template files.
     */
    public function importMahasiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $prodis = ProgramStudi::all()->keyBy(fn($p) => strtoupper(trim($p->kode_prodi)));

        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheetNames = $spreadsheet->getSheetNames();

            $totalImported = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;

            DB::beginTransaction();

            foreach ($sheetNames as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $rows = $sheet->toArray(null, true, true, false);

                if (empty($rows)) {
                    continue;
                }

                // Detect column indices based on header row or standard positions
                $headerIdx = -1;
                $colMap = [
                    'nim' => -1,
                    'nama' => -1,
                    'kelas' => -1,
                    'prodi' => -1,
                    'angkatan' => -1,
                    'email' => -1,
                    'phone' => -1,
                ];

                // Search first 5 rows for header keyword
                for ($r = 0; $r < min(5, count($rows)); $r++) {
                    $rowLower = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$r]);
                    foreach ($rowLower as $cIdx => $cellVal) {
                        if (in_array($cellVal, ['nipd', 'nim', 'nipd / nim', 'no. induk', 'nomor induk'])) {
                            $colMap['nim'] = $cIdx;
                            $headerIdx = $r;
                        } elseif (in_array($cellVal, ['peserta didik', 'nama', 'nama mahasiswa', 'nama peserta didik', 'nama lengkap', 'nama mahasiswa / peserta didik'])) {
                            $colMap['nama'] = $cIdx;
                            $headerIdx = $r;
                        } elseif (in_array($cellVal, ['kelas', 'rombel', 'nama kelas'])) {
                            $colMap['kelas'] = $cIdx;
                        } elseif (in_array($cellVal, ['prodi', 'program studi', 'jurusan', 'kode prodi'])) {
                            $colMap['prodi'] = $cIdx;
                        } elseif (in_array($cellVal, ['angkatan', 'tahun', 'tahun angkatan'])) {
                            $colMap['angkatan'] = $cIdx;
                        } elseif (in_array($cellVal, ['email', 'surel', 'e-mail'])) {
                            $colMap['email'] = $cIdx;
                        } elseif (in_array($cellVal, ['telepon', 'no hp', 'phone', 'hp', 'no telepon', 'no. telp', 'whatsapp'])) {
                            $colMap['phone'] = $cIdx;
                        }
                    }
                    if ($headerIdx !== -1) {
                        break;
                    }
                }

                // Fallback default mapping if headers not explicitly detected by text
                if ($colMap['nim'] === -1) $colMap['nim'] = 1;
                if ($colMap['nama'] === -1) $colMap['nama'] = 2;
                if ($colMap['kelas'] === -1) $colMap['kelas'] = 3;
                if ($headerIdx === -1) $headerIdx = 0;

                // Process rows starting after header
                for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                    $row = $rows[$i];

                    $nim = trim((string)($row[$colMap['nim']] ?? ''));
                    $nama = trim((string)($row[$colMap['nama']] ?? ''));
                    $kelas = trim((string)($row[$colMap['kelas']] ?? ''));

                    // Skip empty rows or title rows
                    if (empty($nim) || empty($nama) || strtolower($nim) === 'nipd' || strtolower($nim) === 'nim' || strtolower($nama) === 'peserta didik') {
                        continue;
                    }

                    // Format name to title case
                    $nama = ucwords(strtolower($nama));

                    // Determine Angkatan
                    $angkatan = '';
                    if ($colMap['angkatan'] !== -1 && !empty($row[$colMap['angkatan']])) {
                        $angkatan = trim((string)$row[$colMap['angkatan']]);
                    } elseif (is_numeric($sheetName) && strlen($sheetName) === 4) {
                        $angkatan = $sheetName;
                    } elseif (preg_match('/\b(20\d{2})\b/', $kelas, $m)) {
                        $angkatan = $m[1];
                    } elseif (preg_match('/(\d{2})-\d+/', $kelas, $m)) {
                        $angkatan = '20' . $m[1];
                    } else {
                        $angkatan = (string)date('Y');
                    }

                    // Determine Program Studi
                    $prodiCode = '';
                    if ($colMap['prodi'] !== -1 && !empty($row[$colMap['prodi']])) {
                        $prodiCode = strtoupper(trim((string)$row[$colMap['prodi']]));
                    }
                    if (empty($prodiCode) || !$prodis->has($prodiCode)) {
                        // Detect from class name prefix (e.g. "ASE 24-001" -> "ASE")
                        $prefix = strtoupper(explode(' ', $kelas)[0] ?? '');
                        if ($prodis->has($prefix)) {
                            $prodiCode = $prefix;
                        }
                    }

                    $prodiId = $prodis->has($prodiCode) ? $prodis->get($prodiCode)->id : ($prodis->first()?->id ?? null);
                    if (!$prodiId) {
                        $totalSkipped++;
                        continue;
                    }

                    // Determine Email
                    $email = '';
                    if ($colMap['email'] !== -1 && !empty($row[$colMap['email']])) {
                        $email = strtolower(trim((string)$row[$colMap['email']]));
                    }
                    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $email = preg_replace('/[^a-zA-Z0-9]/', '', $nim) . '@mahasiswa.lp3i.ac.id';
                    }

                    $phone = ($colMap['phone'] !== -1 && !empty($row[$colMap['phone']])) ? trim((string)$row[$colMap['phone']]) : null;

                    // Find or create User
                    $existingMahasiswa = Mahasiswa::where('nim', $nim)->first();
                    if ($existingMahasiswa) {
                        // Update existing
                        $user = $existingMahasiswa->user;
                        if ($user) {
                            $user->name = $nama;
                            if ($phone) $user->phone = $phone;
                            $user->save();
                        }
                        $existingMahasiswa->update([
                            'program_studi_id' => $prodiId,
                            'angkatan' => $angkatan,
                            'kelas' => $kelas,
                        ]);
                        $totalUpdated++;
                    } else {
                        // Check if user with same email exists
                        $user = User::where('email', $email)->first();
                        if (!$user) {
                            $user = User::create([
                                'name' => $nama,
                                'email' => $email,
                                'role' => 'mahasiswa',
                                'phone' => $phone,
                                'password' => Hash::make('password123'),
                            ]);
                        } else {
                            $user->name = $nama;
                            $user->role = 'mahasiswa';
                            if ($phone) $user->phone = $phone;
                            $user->save();
                        }

                        Mahasiswa::create([
                            'user_id' => $user->id,
                            'nim' => $nim,
                            'program_studi_id' => $prodiId,
                            'angkatan' => $angkatan,
                            'kelas' => $kelas,
                            'status' => 'Aktif',
                        ]);
                        $totalImported++;
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.master.index', ['tab' => 'mahasiswa'])
                ->with('success', "Import Data Mahasiswa Selesai! Berhasil ditambahkan: {$totalImported} data baru, diperbarui: {$totalUpdated} data" . ($totalSkipped > 0 ? ", dilewati: {$totalSkipped} baris tidak valid." : "."));
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.master.index', ['tab' => 'mahasiswa'])
                ->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Import Data Dosen from Excel.
     */
    public function importDosen(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $prodis = ProgramStudi::all()->keyBy(fn($p) => strtoupper(trim($p->kode_prodi)));

        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            $totalImported = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;

            DB::beginTransaction();

            $headerIdx = 0;
            // Detect header row
            for ($r = 0; $r < min(5, count($rows)); $r++) {
                $rowLower = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$r]);
                if (in_array('nidn', $rowLower) || in_array('nama dosen', $rowLower) || in_array('nama lengkap', $rowLower)) {
                    $headerIdx = $r;
                    break;
                }
            }

            for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $nidn = trim((string)($row[1] ?? ''));
                $nama = trim((string)($row[2] ?? ''));
                $gelar = trim((string)($row[3] ?? ''));
                $kodeProdi = strtoupper(trim((string)($row[4] ?? '')));
                $email = strtolower(trim((string)($row[5] ?? '')));
                $phone = trim((string)($row[6] ?? ''));

                if (empty($nidn) || empty($nama) || strtolower($nidn) === 'nidn') {
                    continue;
                }

                $prodiId = $prodis->has($kodeProdi) ? $prodis->get($kodeProdi)->id : ($prodis->first()?->id ?? null);
                if (!$prodiId) {
                    $totalSkipped++;
                    continue;
                }

                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email = preg_replace('/[^a-zA-Z0-9]/', '', $nidn) . '@dosen.lp3i.ac.id';
                }

                $existingDosen = Dosen::where('nidn', $nidn)->first();
                if ($existingDosen) {
                    $user = $existingDosen->user;
                    if ($user) {
                        $user->name = $nama;
                        if (!empty($phone)) $user->phone = $phone;
                        $user->save();
                    }
                    $existingDosen->update([
                        'gelar' => $gelar ?: null,
                        'program_studi_id' => $prodiId,
                    ]);
                    $totalUpdated++;
                } else {
                    $user = User::where('email', $email)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $nama,
                            'email' => $email,
                            'role' => 'dosen',
                            'phone' => $phone ?: null,
                            'password' => Hash::make('password123'),
                        ]);
                    } else {
                        $user->name = $nama;
                        $user->role = 'dosen';
                        if (!empty($phone)) $user->phone = $phone;
                        $user->save();
                    }

                    Dosen::create([
                        'user_id' => $user->id,
                        'nidn' => $nidn,
                        'gelar' => $gelar ?: null,
                        'program_studi_id' => $prodiId,
                    ]);
                    $totalImported++;
                }
            }

            DB::commit();

            return redirect()->route('admin.master.index', ['tab' => 'dosen'])
                ->with('success', "Import Data Dosen Selesai! Berhasil ditambahkan: {$totalImported} dosen baru, diperbarui: {$totalUpdated} data" . ($totalSkipped > 0 ? ", dilewati: {$totalSkipped} baris tidak valid." : "."));
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.master.index', ['tab' => 'dosen'])
                ->with('error', 'Gagal memproses file Excel Dosen: ' . $e->getMessage());
        }
    }

    /**
     * Import Data Mata Kuliah from Excel.
     */
    public function importMataKuliah(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $prodis = ProgramStudi::all()->keyBy(fn($p) => strtoupper(trim($p->kode_prodi)));

        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            $totalImported = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;

            DB::beginTransaction();

            $headerIdx = 0;
            for ($r = 0; $r < min(5, count($rows)); $r++) {
                $rowLower = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$r]);
                if (in_array('kode matkul', $rowLower) || in_array('nama mata kuliah', $rowLower)) {
                    $headerIdx = $r;
                    break;
                }
            }

            for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $kodeMatkul = strtoupper(trim((string)($row[1] ?? '')));
                $namaMatkul = trim((string)($row[2] ?? ''));
                $sks = (int)($row[3] ?? 3);
                $kodeProdi = strtoupper(trim((string)($row[4] ?? '')));

                if (empty($kodeMatkul) || empty($namaMatkul) || strtolower($kodeMatkul) === 'kode matkul') {
                    continue;
                }

                $prodiId = $prodis->has($kodeProdi) ? $prodis->get($kodeProdi)->id : ($prodis->first()?->id ?? null);
                if (!$prodiId) {
                    $totalSkipped++;
                    continue;
                }

                $matkul = MataKuliah::where('kode_matkul', $kodeMatkul)->first();
                if ($matkul) {
                    $matkul->update([
                        'nama_matkul' => $namaMatkul,
                        'sks' => max(1, min(6, $sks)),
                        'program_studi_id' => $prodiId,
                    ]);
                    $totalUpdated++;
                } else {
                    MataKuliah::create([
                        'kode_matkul' => $kodeMatkul,
                        'nama_matkul' => $namaMatkul,
                        'sks' => max(1, min(6, $sks)),
                        'program_studi_id' => $prodiId,
                    ]);
                    $totalImported++;
                }
            }

            DB::commit();

            return redirect()->route('admin.master.index', ['tab' => 'matkul'])
                ->with('success', "Import Mata Kuliah Selesai! Berhasil ditambahkan: {$totalImported} matkul baru, diperbarui: {$totalUpdated} data.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.master.index', ['tab' => 'matkul'])
                ->with('error', 'Gagal memproses file Excel Mata Kuliah: ' . $e->getMessage());
        }
    }

    /**
     * Import Data Kelas Perkuliahan from Excel.
     */
    public function importKelas(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');

        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            $totalImported = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;

            DB::beginTransaction();

            $headerIdx = 0;
            for ($r = 0; $r < min(5, count($rows)); $r++) {
                $rowLower = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$r]);
                if (in_array('nama kelas', $rowLower) || in_array('kode matkul', $rowLower)) {
                    $headerIdx = $r;
                    break;
                }
            }

            for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $namaKelas = trim((string)($row[1] ?? ''));
                $kodeMatkul = strtoupper(trim((string)($row[2] ?? '')));
                $nidnDosen = trim((string)($row[3] ?? ''));
                $tahunAjaran = trim((string)($row[4] ?? ''));
                $semester = ucfirst(strtolower(trim((string)($row[5] ?? 'Ganjil'))));
                $ruangan = trim((string)($row[6] ?? ''));
                $jadwal = trim((string)($row[7] ?? ''));

                if (empty($namaKelas) || empty($kodeMatkul) || strtolower($namaKelas) === 'nama kelas') {
                    continue;
                }

                $matkul = MataKuliah::where('kode_matkul', $kodeMatkul)->first();
                $dosen = Dosen::where('nidn', $nidnDosen)->first();

                // Find or create Periode
                $periode = null;
                if (!empty($tahunAjaran)) {
                    $periode = Periode::where('tahun_ajaran', $tahunAjaran)
                        ->where('semester', $semester)
                        ->first();
                    if (!$periode) {
                        $periode = Periode::create([
                            'tahun_ajaran' => $tahunAjaran,
                            'semester' => in_array($semester, ['Ganjil', 'Genap']) ? $semester : 'Ganjil',
                            'status' => 'Aktif',
                        ]);
                    }
                } else {
                    $periode = Periode::where('status', 'Aktif')->first() ?: Periode::latest()->first();
                }

                if (!$matkul || !$dosen || !$periode) {
                    $totalSkipped++;
                    continue;
                }

                $kelas = KelasMataKuliah::where('nama_kelas', $namaKelas)
                    ->where('mata_kuliah_id', $matkul->id)
                    ->where('periode_id', $periode->id)
                    ->first();

                if ($kelas) {
                    $kelas->update([
                        'dosen_id' => $dosen->id,
                        'ruangan' => $ruangan ?: null,
                        'jadwal' => $jadwal ?: null,
                    ]);
                    $totalUpdated++;
                } else {
                    KelasMataKuliah::create([
                        'nama_kelas' => $namaKelas,
                        'mata_kuliah_id' => $matkul->id,
                        'dosen_id' => $dosen->id,
                        'periode_id' => $periode->id,
                        'ruangan' => $ruangan ?: null,
                        'jadwal' => $jadwal ?: null,
                    ]);
                    $totalImported++;
                }
            }

            DB::commit();

            return redirect()->route('admin.master.index', ['tab' => 'kelas'])
                ->with('success', "Import Kelas Perkuliahan Selesai! Berhasil ditambahkan: {$totalImported} kelas baru, diperbarui: {$totalUpdated} data" . ($totalSkipped > 0 ? ", dilewati: {$totalSkipped} (Matkul / Dosen tidak cocok)." : "."));
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.master.index', ['tab' => 'kelas'])
                ->with('error', 'Gagal memproses file Excel Kelas: ' . $e->getMessage());
        }
    }

    /**
     * Import Data Periode from Excel.
     */
    public function importPeriode(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');

        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            $totalImported = 0;
            $totalUpdated = 0;

            DB::beginTransaction();

            $headerIdx = 0;
            for ($r = 0; $r < min(5, count($rows)); $r++) {
                $rowLower = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$r]);
                if (in_array('tahun ajaran', $rowLower) || in_array('semester', $rowLower)) {
                    $headerIdx = $r;
                    break;
                }
            }

            for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $tahunAjaran = trim((string)($row[1] ?? ''));
                $semester = ucfirst(strtolower(trim((string)($row[2] ?? 'Ganjil'))));
                $status = ucfirst(strtolower(trim((string)($row[3] ?? 'Nonaktif'))));

                if (empty($tahunAjaran) || strtolower($tahunAjaran) === 'tahun ajaran') {
                    continue;
                }

                if (!in_array($semester, ['Ganjil', 'Genap'])) {
                    $semester = 'Ganjil';
                }
                if (!in_array($status, ['Aktif', 'Nonaktif'])) {
                    $status = 'Nonaktif';
                }

                $periode = Periode::where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->first();

                if ($periode) {
                    $periode->update(['status' => $status]);
                    $totalUpdated++;
                } else {
                    Periode::create([
                        'tahun_ajaran' => $tahunAjaran,
                        'semester' => $semester,
                        'status' => $status,
                    ]);
                    $totalImported++;
                }
            }

            DB::commit();

            return redirect()->route('admin.master.index', ['tab' => 'periode'])
                ->with('success', "Import Periode Selesai! Berhasil ditambahkan: {$totalImported} periode baru, diperbarui: {$totalUpdated} data.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.master.index', ['tab' => 'periode'])
                ->with('error', 'Gagal memproses file Excel Periode: ' . $e->getMessage());
        }
    }
}
