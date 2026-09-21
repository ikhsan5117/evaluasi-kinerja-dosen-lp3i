<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\KelasMataKuliah;
use App\Models\Kuesioner;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->with('programStudi')->firstOrFail();

        $periodeAktif = Periode::where('status', 'Aktif')->first() ?? Periode::latest()->first();

        // Kuesioner aktif
        $kuesionerAktif = Kuesioner::where('status', 'Aktif')
            ->when($periodeAktif, function ($q) use ($periodeAktif) {
                $q->where('periode_id', $periodeAktif->id);
            })
            ->withCount('pertanyaan')
            ->first();

        // Normalisasi kelas mahasiswa agar cocok baik format "AIS 24-001" maupun "AIS-24-001"
        $rawKelas = trim($mahasiswa->kelas ?? '');
        $kelasWithHyphen = str_replace(' ', '-', $rawKelas);
        $kelasWithSpace = str_replace('-', ' ', $rawKelas);
        $possibleClasses = array_values(array_unique(array_filter([$rawKelas, $kelasWithHyphen, $kelasWithSpace])));

        // Filter: hanya tampilkan mata kuliah & dosen untuk kelas milik mahasiswa ini
        $kelasList = KelasMataKuliah::where('periode_id', $periodeAktif->id ?? 0)
            ->whereIn('nama_kelas', $possibleClasses)
            ->with(['mataKuliah', 'dosen.user'])
            ->get();

        // Kelas yang sudah dievaluasi oleh mahasiswa ini
        $evaluasiSelesai = Evaluasi::where('mahasiswa_id', $mahasiswa->id)
            ->where('kuesioner_id', $kuesionerAktif->id ?? 0)
            ->pluck('kelas_mata_kuliah_id')
            ->toArray();

        $totalKelas = $kelasList->count();
        $totalSudah = count($evaluasiSelesai);
        $totalBelum = max(0, $totalKelas - $totalSudah);

        return view('mahasiswa.dashboard', compact(
            'mahasiswa',
            'periodeAktif',
            'kuesionerAktif',
            'kelasList',
            'evaluasiSelesai',
            'totalKelas',
            'totalSudah',
            'totalBelum',
            'namaKelasFilter'
        ));
    }
}
