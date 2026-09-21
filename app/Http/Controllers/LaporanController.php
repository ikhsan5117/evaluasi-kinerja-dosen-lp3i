<?php

namespace App\Http\Controllers;

use App\Exports\LaporanEvaluasiExport;
use App\Models\Dosen;
use App\Models\Periode;
use App\Models\ProgramStudi;
use App\Models\ViewRekapEvaluasiKelas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $periodeId = $request->get('periode_id');
        $dosenId = $request->get('dosen_id');
        $prodiId = $request->get('prodi_id');

        $periodes = Periode::latest()->get();
        $dosens = Dosen::with('user')->get();
        $prodis = ProgramStudi::all();

        // Periode default
        if (!$periodeId && $periodes->isNotEmpty()) {
            $aktif = $periodes->firstWhere('status', 'Aktif') ?? $periodes->first();
            $periodeId = $aktif->id;
        }

        // Query rekapitulasi data menggunakan Database View ultra-cepat
        $query = ViewRekapEvaluasiKelas::with([
            'dosen.user',
            'dosen.programStudi',
            'kelasMataKuliah.mataKuliah',
            'periode'
        ]);

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($dosenId) {
            $query->where('dosen_id', $dosenId);
        }

        if ($prodiId) {
            $query->whereHas('dosen', fn($d) => $d->where('program_studi_id', $prodiId));
        }

        $viewRows = $query->get();

        $rekapData = [];
        $totalRespondenGlobal = 0;
        $totalSkorGlobal = 0;
        $countGlobal = 0;

        foreach ($viewRows as $row) {
            if (!$row->dosen || !$row->kelasMataKuliah) {
                continue;
            }

            $jmlResponden = (int) $row->total_responden;
            $totalRespondenGlobal += $jmlResponden;

            $rataRata = round($row->avg_total ?? 0, 2);
            if ($rataRata > 0) {
                $totalSkorGlobal += $rataRata;
                $countGlobal++;
            }

            $rekapData[] = [
                'dosen' => $row->dosen,
                'kelas' => $row->kelasMataKuliah,
                'total_responden' => $jmlResponden,
                'pedagogik' => round($row->avg_pedagogik ?? 0, 2),
                'profesional' => round($row->avg_profesional ?? 0, 2),
                'kepribadian' => round($row->avg_kepribadian ?? 0, 2),
                'sosial' => round($row->avg_sosial ?? 0, 2),
                'rata_rata' => $rataRata,
                'predikat' => $jmlResponden > 0 ? $row->predikat : 'Belum Ada Data',
            ];
        }

        $rataRataGlobal = $countGlobal > 0 ? round($totalSkorGlobal / $countGlobal, 2) : 0;
        $totalDosenDievaluasi = count(array_filter($rekapData, fn($r) => $r['total_responden'] > 0));

        return view('admin.laporan.index', compact(
            'periodes',
            'dosens',
            'prodis',
            'periodeId',
            'dosenId',
            'prodiId',
            'rekapData',
            'totalRespondenGlobal',
            'rataRataGlobal',
            'totalDosenDievaluasi'
        ));
    }

    public function exportExcel(Request $request)
    {
        $periodeId = $request->get('periode_id');
        $dosenId = $request->get('dosen_id');

        $periode = $periodeId ? Periode::find($periodeId) : null;
        $namaPeriode = $periode ? str_replace(['/', ' '], '_', $periode->tahun_ajaran . '_' . $periode->semester) : 'Semua_Periode';
        $filename = 'Laporan_Evaluasi_Dosen_LP3I_' . $namaPeriode . '.xlsx';

        return Excel::download(new LaporanEvaluasiExport($periodeId, $dosenId), $filename);
    }

    public function exportPdf(Request $request)
    {
        $periodeId = $request->get('periode_id');
        $dosenId = $request->get('dosen_id');
        $prodiId = $request->get('prodi_id');

        $periode = $periodeId ? Periode::find($periodeId) : Periode::where('status', 'Aktif')->first();

        $query = ViewRekapEvaluasiKelas::with([
            'dosen.user',
            'dosen.programStudi',
            'kelasMataKuliah.mataKuliah',
            'periode'
        ]);

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($dosenId) {
            $query->where('dosen_id', $dosenId);
        }

        if ($prodiId) {
            $query->whereHas('dosen', fn($d) => $d->where('program_studi_id', $prodiId));
        }

        $viewRows = $query->get();

        $rekapData = [];
        foreach ($viewRows as $row) {
            if (!$row->dosen || !$row->kelasMataKuliah) {
                continue;
            }

            $jmlResponden = (int) $row->total_responden;
            $rataRata = round($row->avg_total ?? 0, 2);

            $rekapData[] = [
                'dosen' => $row->dosen,
                'kelas' => $row->kelasMataKuliah,
                'total_responden' => $jmlResponden,
                'pedagogik' => round($row->avg_pedagogik ?? 0, 2),
                'profesional' => round($row->avg_profesional ?? 0, 2),
                'kepribadian' => round($row->avg_kepribadian ?? 0, 2),
                'sosial' => round($row->avg_sosial ?? 0, 2),
                'rata_rata' => $rataRata,
                'predikat' => $jmlResponden > 0 ? $row->predikat : 'Belum Ada Data',
            ];
        }

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('rekapData', 'periode'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Evaluasi_Dosen_LP3I.pdf');
    }
}

