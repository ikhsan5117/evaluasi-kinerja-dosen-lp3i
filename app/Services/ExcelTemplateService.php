<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelTemplateService
{
    /**
     * Download generated template file as streamed response.
     */
    public function download(string $type, ?string $title = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr(ucfirst($type), 0, 31));

        $config = $this->getTemplateConfig($type);

        // Title Block
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT ' . strtoupper($config['title']));
        $sheet->mergeCells('A1:' . $this->getColumnLetter(count($config['headers'])) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('001F3F'));
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Subtitle / Guide
        $sheet->setCellValue('A2', $config['subtitle'] ?? 'Petunjuk: Isi data mulai baris ke-4. Jangan mengubah susunan baris judul kolom (Header).');
        $sheet->mergeCells('A2:' . $this->getColumnLetter(count($config['headers'])) . '2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('555555'));

        // Header Row (Row 3)
        $headerRow = 3;
        foreach ($config['headers'] as $colIdx => $header) {
            $colLetter = $this->getColumnLetter($colIdx + 1);
            $sheet->setCellValue($colLetter . $headerRow, $header);
        }

        // Header Styling (LP3I Navy Brand)
        $lastColLetter = $this->getColumnLetter(count($config['headers']));
        $headerRange = "A{$headerRow}:{$lastColLetter}{$headerRow}";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '002B49'], // LP3I Navy Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // Sample Data Rows
        $currentRow = 4;
        foreach ($config['samples'] as $sampleRow) {
            foreach ($sampleRow as $colIdx => $val) {
                $colLetter = $this->getColumnLetter($colIdx + 1);
                $sheet->setCellValueExplicit($colLetter . $currentRow, (string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $currentRow++;
        }

        $lastDataRow = max(4, $currentRow - 1);
        $dataRange = "A4:{$lastColLetter}{$lastDataRow}";

        // Table Borders
        $tableRange = "A{$headerRow}:{$lastColLetter}{$lastDataRow}";
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Alternating row background for samples
        for ($r = 4; $r <= $lastDataRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:{$lastColLetter}{$r}")->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ]);
            }
            $sheet->getRowDimension($r)->setRowHeight(22);
        }

        // Auto-fit column widths
        foreach (range(1, count($config['headers'])) as $colIdx) {
            $colLetter = $this->getColumnLetter($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Center align specific columns like No, SKS, Semester, Status
        if (!empty($config['center_columns'])) {
            foreach ($config['center_columns'] as $colIdx) {
                $colLetter = $this->getColumnLetter($colIdx);
                $sheet->getStyle("{$colLetter}4:{$colLetter}{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        $filename = 'template_import_' . $type . '_' . date('Ymd_His') . '.xlsx';

        return new StreamedResponse(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Get template specification for each entity.
     */
    protected function getTemplateConfig(string $type): array
    {
        return match ($type) {
            'mahasiswa' => [
                'title' => 'Data Mahasiswa',
                'subtitle' => 'Petunjuk: Isi NIPD/NIM, Nama Peserta Didik, Kelas (contoh: ASE 24-001 / OAA 23-001), Program Studi (kode prodi seperti OAA/AIS/ASE), Angkatan (contoh: 2024). Email & No HP bersifat opsional.',
                'headers' => ['No', 'NIPD / NIM', 'Nama Mahasiswa / Peserta Didik', 'Kelas', 'Kode Prodi', 'Angkatan', 'Email', 'No Telepon'],
                'center_columns' => [1, 4, 5, 6],
                'samples' => [
                    ['1', '2410910040033', 'Muhamad Ikhsan Maulana', 'ASE 24-001', 'ASE', '2024', '2410910040033@lp3i.ac.id', '081234567890'],
                    ['2', '2410910030059', 'Helfira Fayza Kulla Azmina', 'AIS 24-001', 'AIS', '2024', '2410910030059@lp3i.ac.id', '081234567891'],
                    ['3', '2310910070187', 'Amel Jenni Shakila', 'OAA 23-001', 'OAA', '2023', '2310910070187@lp3i.ac.id', '081234567892'],
                ],
            ],
            'dosen' => [
                'title' => 'Data Dosen',
                'subtitle' => 'Petunjuk: NIDN dan Nama Lengkap wajib diisi. Kode Prodi diisi kode program studi (contoh: ASE / OAA / AIS). Gelar, Email, No HP bersifat opsional.',
                'headers' => ['No', 'NIDN', 'Nama Lengkap', 'Gelar', 'Kode Prodi', 'Email', 'No Telepon'],
                'center_columns' => [1, 2, 5],
                'samples' => [
                    ['1', '0412058001', 'Dr. Ahmad Fauzi', 'M.Kom', 'ASE', 'ahmad.fauzi@lp3i.ac.id', '081211112222'],
                    ['2', '0415088502', 'Siti Rahmawati', 'M.Ak', 'OAA', 'siti.rahmawati@lp3i.ac.id', '081233334444'],
                    ['3', '0420019003', 'Budi Santoso', 'M.Kom', 'AIS', 'budi.santoso@lp3i.ac.id', '081255556666'],
                ],
            ],
            'matkul' => [
                'title' => 'Data Mata Kuliah',
                'subtitle' => 'Petunjuk: Kode Matkul harus unik. SKS diisi angka 1-6. Kode Prodi diisi kode program studi (ASE/OAA/AIS).',
                'headers' => ['No', 'Kode Matkul', 'Nama Mata Kuliah', 'SKS', 'Kode Prodi'],
                'center_columns' => [1, 2, 4, 5],
                'samples' => [
                    ['1', 'MK-ASE01', 'Pemrograman Web Framework', '3', 'ASE'],
                    ['2', 'MK-OAA01', 'Otomatisasi Perkantoran Modern', '3', 'OAA'],
                    ['3', 'MK-AIS01', 'Sistem Informasi Akuntansi', '3', 'AIS'],
                ],
            ],
            'kelas' => [
                'title' => 'Data Kelas Perkuliahan',
                'subtitle' => 'Petunjuk: Nama Kelas, Kode Matkul, NIDN Dosen, Tahun Ajaran (contoh: 2024/2025), dan Semester (Ganjil/Genap) wajib sesuai data master.',
                'headers' => ['No', 'Nama Kelas', 'Kode Matkul', 'NIDN Dosen', 'Tahun Ajaran', 'Semester', 'Ruangan', 'Jadwal'],
                'center_columns' => [1, 2, 3, 4, 5, 6],
                'samples' => [
                    ['1', 'ASE 24-001', 'MK-ASE01', '0412058001', '2024/2025', 'Ganjil', 'Lab Komputer 1', 'Senin, 08:00 - 10:30'],
                    ['2', 'OAA 23-001', 'MK-OAA01', '0415088502', '2024/2025', 'Ganjil', 'Ruang 204', 'Selasa, 10:30 - 13:00'],
                    ['3', 'AIS 24-001', 'MK-AIS01', '0420019003', '2024/2025', 'Ganjil', 'Lab Akuntansi', 'Rabu, 13:00 - 15:30'],
                ],
            ],
            'periode' => [
                'title' => 'Data Periode Akademik',
                'subtitle' => 'Petunjuk: Tahun Ajaran (contoh: 2024/2025), Semester (Ganjil/Genap), Status (Aktif/Nonaktif).',
                'headers' => ['No', 'Tahun Ajaran', 'Semester', 'Status'],
                'center_columns' => [1, 2, 3, 4],
                'samples' => [
                    ['1', '2024/2025', 'Ganjil', 'Aktif'],
                    ['2', '2024/2025', 'Genap', 'Nonaktif'],
                    ['3', '2025/2026', 'Ganjil', 'Nonaktif'],
                ],
            ],
            'pertanyaan' => [
                'title' => 'Butir Pertanyaan Kuesioner',
                'subtitle' => 'Petunjuk: Kategori wajib salah satu dari: Metode Pembelajaran, Profesional, Kepribadian, atau Sosial. Skor skala Likert 1-5 akan diterapkan otomatis.',
                'headers' => ['No', 'Teks Butir Pertanyaan', 'Kategori (Metode Pembelajaran / Profesional / Kepribadian / Sosial)'],
                'center_columns' => [1, 3],
                'samples' => [
                    ['1', 'Dosen menyampaikan rencana pembelajaran (RPS) dan kontrak kuliah dengan jelas di awal perkuliahan.', 'Metode Pembelajaran'],
                    ['2', 'Dosen menguasai materi perkuliahan secara mendalam dan mampu menjelaskan konsep dengan baik.', 'Profesional'],
                    ['3', 'Dosen bersikap adil, santun, dan objektif dalam memberikan penilaian kepada mahasiswa.', 'Kepribadian'],
                    ['4', 'Dosen mudah dihubungi dan terbuka untuk berdiskusi terkait kesulitan belajar mahasiswa.', 'Sosial'],
                ],
            ],
            default => [
                'title' => 'Data Master',
                'subtitle' => 'Isi data sesuai kolom header.',
                'headers' => ['No', 'Kolom 1', 'Kolom 2'],
                'center_columns' => [1],
                'samples' => [
                    ['1', 'Contoh 1', 'Contoh 2'],
                ],
            ],
        };
    }

    /**
     * Convert 1-based column index to Excel column letter (1 -> A, 27 -> AA).
     */
    protected function getColumnLetter(int $colIndex): string
    {
        $letter = '';
        while ($colIndex > 0) {
            $mod = ($colIndex - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $colIndex = (int)(($colIndex - $mod) / 26);
        }
        return $letter;
    }
}
