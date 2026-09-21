<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Evaluasi;
use App\Models\JawabanKuesioner;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->with('programStudi')->firstOrFail();

        $periodeAktif = Periode::where('status', 'Aktif')->first() ?? Periode::latest()->first();

        // Ambil kelas yang diampu dosen pada periode aktif
        $kelasList = $dosen->kelasMataKuliah()
            ->when($periodeAktif, function ($q) use ($periodeAktif) {
                $q->where('periode_id', $periodeAktif->id);
            })
            ->with(['mataKuliah', 'evaluasi.jawaban.pertanyaan'])
            ->get();

        $evaluasiIds = $kelasList->flatMap(fn($k) => $k->evaluasi->pluck('id'));
        $totalEvaluasi = $evaluasiIds->count();

        // Hitung nilai per aspek
        $allJawaban = JawabanKuesioner::whereIn('evaluasi_id', $evaluasiIds)->with('pertanyaan')->get();

        $pedagogik = $allJawaban->where('pertanyaan.kategori', 'Pedagogik')->avg('skor') ?? 0;
        $profesional = $allJawaban->where('pertanyaan.kategori', 'Profesional')->avg('skor') ?? 0;
        $kepribadian = $allJawaban->where('pertanyaan.kategori', 'Kepribadian')->avg('skor') ?? 0;
        $sosial = $allJawaban->where('pertanyaan.kategori', 'Sosial')->avg('skor') ?? 0;
        $rataRata = $allJawaban->avg('skor') ?? 0;

        // Feedback / saran terbaru dari mahasiswa (anonim)
        $feedbacks = Evaluasi::whereIn('id', $evaluasiIds)
            ->whereNotNull('saran_masukan')
            ->where('saran_masukan', '!=', '')
            ->with(['kelasMataKuliah.mataKuliah'])
            ->latest()
            ->take(6)
            ->get();

        return view('dosen.dashboard', compact(
            'dosen',
            'periodeAktif',
            'kelasList',
            'totalEvaluasi',
            'pedagogik',
            'profesional',
            'kepribadian',
            'sosial',
            'rataRata',
            'feedbacks'
        ));
    }
}
