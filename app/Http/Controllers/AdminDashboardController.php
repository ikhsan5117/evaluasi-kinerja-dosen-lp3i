<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Evaluasi;
use App\Models\KelasMataKuliah;
use App\Models\Kuesioner;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalDosen = Dosen::count();
        $totalMahasiswa = Mahasiswa::count();
        $totalKuesioner = Kuesioner::count();
        $periodeAktif = Periode::where('status', 'Aktif')->first() ?? Periode::latest()->first();

        // Hitung partisipasi evaluasi periode aktif
        $totalKelasAktif = $periodeAktif ? KelasMataKuliah::where('periode_id', $periodeAktif->id)->count() : 0;
        $totalEvaluasiAktif = $periodeAktif ? Evaluasi::whereHas('kelasMataKuliah', function ($q) use ($periodeAktif) {
            $q->where('periode_id', $periodeAktif->id);
        })->count() : 0;

        // Estimasi expected responden (total mhs * total kelas aktif yang relevan)
        $expectedResponden = max(1, $totalMahasiswa * max(1, $totalKelasAktif));
        $partisipasiPersen = min(100, round(($totalEvaluasiAktif / max(1, $totalMahasiswa * 4)) * 100));

        // Aktivitas terbaru
        $recentEvaluasi = Evaluasi::with(['mahasiswa.user', 'kelasMataKuliah.dosen.user', 'kelasMataKuliah.mataKuliah'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalDosen',
            'totalMahasiswa',
            'totalKuesioner',
            'periodeAktif',
            'partisipasiPersen',
            'totalEvaluasiAktif',
            'recentEvaluasi'
        ));
    }
}
