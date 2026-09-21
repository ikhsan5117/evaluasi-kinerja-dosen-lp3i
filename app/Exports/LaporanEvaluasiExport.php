<?php

namespace App\Exports;

use App\Models\Dosen;
use App\Models\Evaluasi;
use App\Models\JawabanKuesioner;
use App\Models\Periode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanEvaluasiExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $periodeId;
    protected $dosenId;

    public function __construct($periodeId = null, $dosenId = null)
    {
        $this->periodeId = $periodeId;
        $this->dosenId = $dosenId;
    }

    public function collection()
    {
        $query = \App\Models\ViewRekapEvaluasiKelas::with([
            'dosen.user',
            'dosen.programStudi',
            'kelasMataKuliah.mataKuliah',
            'periode'
        ]);

        if ($this->dosenId) {
            $query->where('dosen_id', $this->dosenId);
        }

        if ($this->periodeId) {
            $query->where('periode_id', $this->periodeId);
        }

        $rows = $query->get();
        $data = collect();
        $no = 1;

        foreach ($rows as $row) {
            if (!$row->dosen || !$row->kelasMataKuliah) {
                continue;
            }

            $totalResponden = (int) $row->total_responden;
            $pedagogik = round($row->avg_pedagogik ?? 0, 2);
            $profesional = round($row->avg_profesional ?? 0, 2);
            $kepribadian = round($row->avg_kepribadian ?? 0, 2);
            $sosial = round($row->avg_sosial ?? 0, 2);
            $rataRata = round($row->avg_total ?? 0, 2);

            $data->push([
                'no' => $no++,
                'dosen' => $row->dosen->nama_lengkap,
                'nidn' => $row->dosen->nidn,
                'prodi' => $row->dosen->programStudi->nama_prodi ?? '-',
                'mata_kuliah' => $row->kelasMataKuliah->mataKuliah->nama_matkul ?? '-',
                'kelas' => $row->kelasMataKuliah->nama_kelas,
                'total_responden' => $totalResponden,
                'pedagogik' => number_format($pedagogik, 2),
                'profesional' => number_format($profesional, 2),
                'kepribadian' => number_format($kepribadian, 2),
                'sosial' => number_format($sosial, 2),
                'rata_rata' => number_format($rataRata, 2),
                'predikat' => $totalResponden > 0 ? $row->predikat : 'Belum Ada Penilaian',
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Dosen',
            'NIDN',
            'Program Studi',
            'Mata Kuliah',
            'Kelas',
            'Total Responden',
            'Metode Pembelajaran (1-5)',
            'Profesional (1-5)',
            'Kepribadian (1-5)',
            'Sosial (1-5)',
            'Rata-rata Total',
            'Kategori / Predikat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ];
    }
}
