<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\JawabanKuesioner;
use App\Models\KelasMataKuliah;
use App\Models\Kuesioner;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaKuesionerController extends Controller
{
    // Step 1: Daftar dosen & mata kuliah yang harus dievaluasi
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();
        $periodeAktif = Periode::where('status', 'Aktif')->first();

        $kuesionerAktif = Kuesioner::where('status', 'Aktif')
            ->when($periodeAktif, function ($q) use ($periodeAktif) {
                $q->where('periode_id', $periodeAktif->id);
            })
            ->first();

        if (!$kuesionerAktif) {
            return view('mahasiswa.kuesioner.empty');
        }

        // Normalisasi kelas agar cocok baik format "AIS 24-001" maupun "AIS-24-001"
        $rawKelas = trim($mahasiswa->kelas ?? '');
        $kelasWithHyphen = str_replace(' ', '-', $rawKelas);
        $kelasWithSpace = str_replace('-', ' ', $rawKelas);
        $possibleClasses = array_values(array_unique(array_filter([$rawKelas, $kelasWithHyphen, $kelasWithSpace])));

        // Hanya mata kuliah & dosen pada kelas milik mahasiswa ini
        $kelasList = KelasMataKuliah::where('periode_id', $periodeAktif->id ?? 0)
            ->whereIn('nama_kelas', $possibleClasses)
            ->with(['mataKuliah', 'dosen.user'])
            ->get();
        $namaKelasFilter = $rawKelas;

        $evaluasiSudah = Evaluasi::where('mahasiswa_id', $mahasiswa->id)
            ->where('kuesioner_id', $kuesionerAktif->id)
            ->pluck('kelas_mata_kuliah_id')
            ->toArray();

        return view('mahasiswa.kuesioner.index', compact('kuesionerAktif', 'kelasList', 'evaluasiSudah', 'mahasiswa', 'namaKelasFilter'));
    }

    // Step 2: Form pengisian kuesioner untuk kelas tertentu
    public function fill(KelasMataKuliah $kelas)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        $kuesioner = Kuesioner::where('status', 'Aktif')
            ->where('periode_id', $kelas->periode_id)
            ->with(['pertanyaan' => fn($q) => $q->orderBy('nomor_urut')])
            ->firstOrFail();

        // Cek apakah sudah pernah mengisi
        $existing = Evaluasi::where('mahasiswa_id', $mahasiswa->id)
            ->where('kelas_mata_kuliah_id', $kelas->id)
            ->where('kuesioner_id', $kuesioner->id)
            ->first();

        if ($existing) {
            return redirect()->route('mahasiswa.history.show', $existing)
                ->with('warning', 'Anda sudah pernah mengisi kuesioner evaluasi untuk dosen dan kelas ini.');
        }

        $kelas->load(['mataKuliah', 'dosen.user', 'periode']);

        return view('mahasiswa.kuesioner.fill', compact('kelas', 'kuesioner'));
    }

    // Step 3: Simpan jawaban & tampilkan konfirmasi selesai
    public function store(Request $request, KelasMataKuliah $kelas)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        $kuesioner = Kuesioner::where('status', 'Aktif')
            ->where('periode_id', $kelas->periode_id)
            ->with('pertanyaan')
            ->firstOrFail();

        // Validasi: pastikan semua butir pertanyaan dijawab
        $rules = [
            'saran_masukan' => 'nullable|string|max:1000',
            'jawaban' => 'required|array|min:' . $kuesioner->pertanyaan->count(),
            'jawaban.*' => 'required|integer|min:1|max:5',
        ];

        $messages = [
            'jawaban.*.required' => 'Seluruh butir pertanyaan wajib diberi nilai.',
            'jawaban.min' => 'Seluruh pertanyaan harus diisi lengkap sebelum disimpan.',
        ];

        $validated = $request->validate($rules, $messages);

        DB::transaction(function () use ($mahasiswa, $kelas, $kuesioner, $validated) {
            $evaluasi = Evaluasi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'kelas_mata_kuliah_id' => $kelas->id,
                'kuesioner_id' => $kuesioner->id,
                'tanggal_pengisian' => now(),
                'saran_masukan' => $validated['saran_masukan'] ?? null,
                'status' => 'Selesai',
            ]);

            foreach ($validated['jawaban'] as $pertanyaanId => $skor) {
                JawabanKuesioner::create([
                    'evaluasi_id' => $evaluasi->id,
                    'pertanyaan_id' => $pertanyaanId,
                    'skor' => $skor,
                ]);
            }
        });

        return redirect()->route('mahasiswa.kuesioner.success', $kelas)
            ->with('success', 'Evaluasi dosen berhasil dikirim. Terima kasih atas partisipasi Anda!');
    }

    public function success(KelasMataKuliah $kelas)
    {
        $kelas->load(['mataKuliah', 'dosen.user', 'periode']);
        return view('mahasiswa.kuesioner.success', compact('kelas'));
    }
}
