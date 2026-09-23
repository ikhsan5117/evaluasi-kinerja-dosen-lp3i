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
        $totalDosen = Dosen::where('status', 'Aktif')->orWhereNull('status')->count();
        $totalMahasiswa = Mahasiswa::where('status', 'Aktif')->orWhereNull('status')->count();
        $totalKuesioner = Kuesioner::count();
        $periodeAktif = Periode::where('status', 'Aktif')->first() ?? Periode::latest()->first();

        // Hitung partisipasi evaluasi periode aktif berdasarkan jumlah mahasiswa
        $totalKelasAktif = $periodeAktif ? KelasMataKuliah::where('periode_id', $periodeAktif->id)->count() : 0;
        $totalEvaluasiAktif = $periodeAktif ? Evaluasi::whereHas('kelasMataKuliah', function ($q) use ($periodeAktif) {
            $q->where('periode_id', $periodeAktif->id);
        })->count() : 0;

        // Mahasiswa unik yang sudah mengisi setidaknya 1 evaluasi di periode ini
        $mahasiswaMengisi = $periodeAktif ? Evaluasi::whereHas('kelasMataKuliah', function ($q) use ($periodeAktif) {
            $q->where('periode_id', $periodeAktif->id);
        })->distinct('mahasiswa_id')->count('mahasiswa_id') : 0;

        $partisipasiPersen = $totalMahasiswa > 0 ? min(100, round(($mahasiswaMengisi / $totalMahasiswa) * 100)) : 0;

        // Aktivitas terbaru
        $recentEvaluasi = Evaluasi::with(['mahasiswa.user', 'kelasMataKuliah.dosen.user', 'kelasMataKuliah.mataKuliah'])
            ->latest()
            ->take(50)
            ->get();

        return view('admin.dashboard', compact(
            'totalDosen',
            'totalMahasiswa',
            'totalKuesioner',
            'periodeAktif',
            'partisipasiPersen',
            'mahasiswaMengisi',
            'totalEvaluasiAktif',
            'recentEvaluasi'
        ));
    }
}
