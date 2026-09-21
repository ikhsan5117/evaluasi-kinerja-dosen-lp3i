<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        $periodeId = $request->get('periode_id');
        $periodes = Periode::latest()->get();

        $query = Evaluasi::where('mahasiswa_id', $mahasiswa->id)
            ->with(['kelasMataKuliah.mataKuliah', 'kelasMataKuliah.dosen.user', 'kelasMataKuliah.periode', 'kuesioner', 'jawaban']);

        if ($periodeId) {
            $query->whereHas('kelasMataKuliah', fn($q) => $q->where('periode_id', $periodeId));
        }

        $histories = $query->latest('tanggal_pengisian')->paginate(10);

        return view('mahasiswa.history.index', compact('histories', 'periodes', 'periodeId'));
    }

    public function show(Evaluasi $evaluasi)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        // Security check
        if ($evaluasi->mahasiswa_id !== $mahasiswa->id) {
            abort(403, 'Anda tidak diizinkan melihat evaluasi ini.');
        }

        $evaluasi->load(['kelasMataKuliah.mataKuliah', 'kelasMataKuliah.dosen.user', 'kelasMataKuliah.periode', 'kuesioner', 'jawaban.pertanyaan']);

        return view('mahasiswa.history.show', compact('evaluasi'));
    }
}
