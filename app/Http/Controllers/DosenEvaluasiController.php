<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JawabanKuesioner;
use App\Models\KelasMataKuliah;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenEvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->firstOrFail();

        $periodeId = $request->get('periode_id');
        $periodes = Periode::latest()->get();

        if (!$periodeId && $periodes->isNotEmpty()) {
            $aktif = $periodes->firstWhere('status', 'Aktif') ?? $periodes->first();
            $periodeId = $aktif->id;
        }

        $query = \App\Models\ViewRekapEvaluasiKelas::where('dosen_id', $dosen->id)
            ->with(['kelasMataKuliah.mataKuliah', 'periode']);

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $viewRows = $query->get();

        $rekapPerKelas = [];
        foreach ($viewRows as $row) {
            if (!$row->kelasMataKuliah) {
                continue;
            }

            $jmlResponden = (int) $row->total_responden;
            $rataRata = round($row->avg_total ?? 0, 2);

            $rekapPerKelas[] = [
                'kelas' => $row->kelasMataKuliah,
                'total_responden' => $jmlResponden,
                'pedagogik' => round($row->avg_pedagogik ?? 0, 2),
                'profesional' => round($row->avg_profesional ?? 0, 2),
                'kepribadian' => round($row->avg_kepribadian ?? 0, 2),
                'sosial' => round($row->avg_sosial ?? 0, 2),
                'rata_rata' => $rataRata,
                'predikat' => $jmlResponden > 0 ? $row->predikat : 'Belum Ada Penilaian',
            ];
        }

        return view('dosen.evaluasi.index', compact('dosen', 'periodes', 'periodeId', 'rekapPerKelas'));
    }

    public function detail(KelasMataKuliah $kelas)
    {
        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->firstOrFail();

        // Authorize dosen can only view own class
        if ($kelas->dosen_id !== $dosen->id) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $kelas->load(['mataKuliah', 'periode', 'evaluasi.jawaban.pertanyaan']);

        $evaluasis = $kelas->evaluasi;
        $evaluasiIds = $evaluasis->pluck('id');

        $allJawaban = JawabanKuesioner::whereIn('evaluasi_id', $evaluasiIds)->with('pertanyaan')->get();

        $pertanyaanStats = $allJawaban->groupBy('pertanyaan_id')->map(function ($items) {
            $first = $items->first();
            return [
                'pertanyaan' => $first->pertanyaan,
                'rata_rata' => round($items->avg('skor'), 2),
                'total_jawaban' => $items->count(),
                'distribusi' => [
                    1 => $items->where('skor', 1)->count(),
                    2 => $items->where('skor', 2)->count(),
                    3 => $items->where('skor', 3)->count(),
                    4 => $items->where('skor', 4)->count(),
                    5 => $items->where('skor', 5)->count(),
                ]
            ];
        });

        $feedbacks = $evaluasis->whereNotNull('saran_masukan')->where('saran_masukan', '!=', '');

        return view('dosen.evaluasi.detail', compact('kelas', 'pertanyaanStats', 'feedbacks', 'evaluasis'));
    }
}
