<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\KelasMataKuliah;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Periode;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $allowedTabs = ['dosen', 'mahasiswa', 'periode', 'matkul', 'kelas'];
        $tab = in_array($request->get('tab'), $allowedTabs) ? $request->get('tab') : 'dosen';

        // === DOSEN: search by nama, nidn, email, prodi, status ===
        $dosenQ = $request->get('q_dosen');
        $dosenProdi = $request->get('f_dosen_prodi');
        $dosenStatus = $request->get('f_dosen_status');
        $dosenQuery = Dosen::with(['user', 'programStudi'])
            ->when($dosenQ, fn($q) => $q->whereHas('user', fn($u) =>
                $u->where('name', 'like', "%{$dosenQ}%")
                  ->orWhere('email', 'like', "%{$dosenQ}%")
            )->orWhere('nidn', 'like', "%{$dosenQ}%"))
            ->when($dosenProdi, fn($q) => $q->where('program_studi_id', $dosenProdi))
            ->when($dosenStatus, fn($q) => $q->where('status', $dosenStatus))
            ->latest();
        $dosens = $dosenQuery->paginate(10, ['*'], 'dosen_page')->appends($request->except('dosen_page'));

        // === MAHASISWA: search by nama, nim, kelas, angkatan, prodi, status ===
        $mhsQ = $request->get('q_mhs');
        $mhsProdi = $request->get('f_mhs_prodi');
        $mhsAngkatan = $request->get('f_mhs_angkatan');
        $mhsStatus = $request->get('f_mhs_status');
        $mhsQuery = Mahasiswa::with(['user', 'programStudi'])
            ->when($mhsQ, fn($q) => $q->whereHas('user', fn($u) =>
                $u->where('name', 'like', "%{$mhsQ}%")
            )->orWhere('nim', 'like', "%{$mhsQ}%")
              ->orWhere('kelas', 'like', "%{$mhsQ}%"))
            ->when($mhsProdi, fn($q) => $q->where('program_studi_id', $mhsProdi))
            ->when($mhsAngkatan, fn($q) => $q->where('angkatan', $mhsAngkatan))
            ->when($mhsStatus, fn($q) => $q->where('status', $mhsStatus))
            ->latest();
        $mahasiswas = $mhsQuery->paginate(15, ['*'], 'mhs_page')->appends($request->except('mhs_page'));

        // === PERIODE ===
        $periodes = Periode::latest()->paginate(10, ['*'], 'periode_page')->appends($request->except('periode_page'));

        // === PRODI ===
        $prodis = ProgramStudi::withCount(['dosen', 'mahasiswa', 'mataKuliah'])->get();

        // === MATKUL: search by nama, kode, prodi, status ===
        $matkulQ = $request->get('q_matkul');
        $matkulProdi = $request->get('f_matkul_prodi');
        $matkulStatus = $request->get('f_matkul_status');
        $matkulQuery = MataKuliah::with('programStudi')
            ->when($matkulQ, fn($q) => $q->where('nama_matkul', 'like', "%{$matkulQ}%")
                ->orWhere('kode_matkul', 'like', "%{$matkulQ}%"))
            ->when($matkulProdi, fn($q) => $q->where('program_studi_id', $matkulProdi))
            ->when($matkulStatus, fn($q) => $q->where('status', $matkulStatus))
            ->latest();
        $matkuls = $matkulQuery->paginate(10, ['*'], 'matkul_page')->appends($request->except('matkul_page'));

        // === KELAS: search by nama kelas, dosen, matkul, periode ===
        $kelasQ = $request->get('q_kelas');
        $kelasPeriode = $request->get('f_kelas_periode');
        $kelasQuery = KelasMataKuliah::with(['mataKuliah', 'dosen.user', 'periode'])
            ->when($kelasQ, fn($q) => $q->where('nama_kelas', 'like', "%{$kelasQ}%")
                ->orWhereHas('mataKuliah', fn($mk) => $mk->where('nama_matkul', 'like', "%{$kelasQ}%"))
                ->orWhereHas('dosen.user', fn($d) => $d->where('name', 'like', "%{$kelasQ}%")))
            ->when($kelasPeriode, fn($q) => $q->where('periode_id', $kelasPeriode))
            ->latest();
        $kelasList = $kelasQuery->paginate(10, ['*'], 'kelas_page')->appends($request->except('kelas_page'));

        // Angkatan unik untuk filter mahasiswa
        $angkatanList = Mahasiswa::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');
        $periodeAll   = Periode::orderBy('tahun_ajaran', 'desc')->get();
        $matkulAll    = MataKuliah::orderBy('nama_matkul')->get();
        $dosenAll     = Dosen::with('user')->get();

        return view('admin.master.index', compact(
            'tab', 'dosens', 'mahasiswas', 'periodes', 'prodis',
            'matkuls', 'kelasList', 'angkatanList', 'periodeAll',
            'matkulAll', 'dosenAll'
        ));
    }

    // --- DOSEN CRUD ---
    public function storeDosen(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nidn' => 'required|string|unique:dosen,nidn|max:20',
            'gelar' => 'nullable|string|max:50',
            'program_studi_id' => 'required|exists:program_studi,id',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:Aktif,Nonaktif',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'dosen',
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password'] ?? 'password'),
            ]);

            Dosen::create([
                'user_id' => $user->id,
                'nidn' => $validated['nidn'],
                'gelar' => $validated['gelar'] ?? null,
                'program_studi_id' => $validated['program_studi_id'],
                'status' => $validated['status'] ?? 'Aktif',
            ]);
        });

        return redirect()->route('admin.master.index', ['tab' => 'dosen'])->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function updateDosen(Request $request, Dosen $dosen)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->user_id,
            'nidn' => 'required|string|max:20|unique:dosen,nidn,' . $dosen->id,
            'gelar' => 'nullable|string|max:50',
            'program_studi_id' => 'required|exists:program_studi,id',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        DB::transaction(function () use ($dosen, $validated) {
            $dosen->user->name = $validated['name'];
            $dosen->user->email = $validated['email'];
            $dosen->user->phone = $validated['phone'] ?? null;
            if (!empty($validated['password'])) {
                $dosen->user->password = Hash::make($validated['password']);
            }
            $dosen->user->save();

            $dosen->update([
                'nidn' => $validated['nidn'],
                'gelar' => $validated['gelar'] ?? null,
                'program_studi_id' => $validated['program_studi_id'],
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('admin.master.index', ['tab' => 'dosen'])->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroyDosen(Dosen $dosen)
    {
        $dosen->user->delete(); // On cascade will delete dosen record
        return redirect()->route('admin.master.index', ['tab' => 'dosen'])->with('success', 'Data dosen berhasil dihapus.');
    }

    // --- MAHASISWA CRUD ---
    public function storeMahasiswa(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|unique:mahasiswa,nim|max:30',
            'program_studi_id' => 'required|exists:program_studi,id',
            'angkatan' => 'required|string|max:10',
            'kelas' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:Aktif,Nonaktif,Cuti,Lulus',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'mahasiswa',
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password'] ?? 'password'),
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $validated['nim'],
                'program_studi_id' => $validated['program_studi_id'],
                'angkatan' => $validated['angkatan'],
                'kelas' => $validated['kelas'],
                'status' => $validated['status'] ?? 'Aktif',
            ]);
        });

        return redirect()->route('admin.master.index', ['tab' => 'mahasiswa'])->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function updateMahasiswa(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,
            'nim' => 'required|string|max:30|unique:mahasiswa,nim,' . $mahasiswa->id,
            'program_studi_id' => 'required|exists:program_studi,id',
            'angkatan' => 'required|string|max:10',
            'kelas' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:Aktif,Nonaktif,Cuti,Lulus',
        ]);

        DB::transaction(function () use ($mahasiswa, $validated) {
            $mahasiswa->user->name = $validated['name'];
            $mahasiswa->user->email = $validated['email'];
            $mahasiswa->user->phone = $validated['phone'] ?? null;
            if (!empty($validated['password'])) {
                $mahasiswa->user->password = Hash::make($validated['password']);
            }
            $mahasiswa->user->save();

            $mahasiswa->update([
                'nim' => $validated['nim'],
                'program_studi_id' => $validated['program_studi_id'],
                'angkatan' => $validated['angkatan'],
                'kelas' => $validated['kelas'],
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('admin.master.index', ['tab' => 'mahasiswa'])->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroyMahasiswa(Mahasiswa $mahasiswa)
    {
        $mahasiswa->user->delete();
        return redirect()->route('admin.master.index', ['tab' => 'mahasiswa'])->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    // --- PERIODE CRUD ---
    public function storePeriode(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        if ($validated['status'] === 'Aktif') {
            Periode::where('status', 'Aktif')->update(['status' => 'Nonaktif']);
        }

        Periode::create($validated);

        return redirect()->route('admin.master.index', ['tab' => 'periode'])->with('success', 'Periode akademik berhasil ditambahkan.');
    }

    public function updatePeriode(Request $request, Periode $periode)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        if ($validated['status'] === 'Aktif') {
            Periode::where('id', '!=', $periode->id)->where('status', 'Aktif')->update(['status' => 'Nonaktif']);
        }

        $periode->update($validated);

        return redirect()->route('admin.master.index', ['tab' => 'periode'])->with('success', 'Periode akademik berhasil diperbarui.');
    }

    public function destroyPeriode(Periode $periode)
    {
        $periode->delete();
        return redirect()->route('admin.master.index', ['tab' => 'periode'])->with('success', 'Periode akademik berhasil dihapus.');
    }

    // --- PRODI & MATKUL & KELAS CRUD ---
    public function storeMataKuliah(Request $request)
    {
        $validated = $request->validate([
            'kode_matkul' => 'required|string|unique:mata_kuliah,kode_matkul|max:20',
            'nama_matkul' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'program_studi_id' => 'required|exists:program_studi,id',
            'status' => 'nullable|in:Aktif,Nonaktif',
        ]);

        MataKuliah::create($validated);

        return redirect()->route('admin.master.index', ['tab' => 'matkul'])->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function updateMataKuliah(Request $request, MataKuliah $matkul)
    {
        $validated = $request->validate([
            'kode_matkul' => 'required|string|max:20|unique:mata_kuliah,kode_matkul,' . $matkul->id,
            'nama_matkul' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'program_studi_id' => 'required|exists:program_studi,id',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $matkul->update($validated);

        return redirect()->route('admin.master.index', ['tab' => 'matkul'])->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroyMataKuliah(MataKuliah $matkul)
    {
        $matkul->delete();
        return redirect()->route('admin.master.index', ['tab' => 'matkul'])->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function storeKelas(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'required|exists:dosen,id',
            'periode_id' => 'required|exists:periode,id',
            'nama_kelas' => 'required|string|max:50',
            'ruangan' => 'nullable|string|max:50',
            'jadwal' => 'nullable|string|max:100',
        ]);

        KelasMataKuliah::create($validated);

        return redirect()->route('admin.master.index', ['tab' => 'kelas'])->with('success', 'Kelas perkuliahan berhasil ditambahkan.');
    }

    public function updateKelas(Request $request, KelasMataKuliah $kela)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'required|exists:dosen,id',
            'periode_id' => 'required|exists:periode,id',
            'nama_kelas' => 'required|string|max:50',
            'ruangan' => 'nullable|string|max:50',
            'jadwal' => 'nullable|string|max:100',
        ]);

        $kela->update($validated);

        return redirect()->route('admin.master.index', ['tab' => 'kelas'])->with('success', 'Kelas perkuliahan berhasil diperbarui.');
    }

    public function destroyKelas(KelasMataKuliah $kela)
    {
        $kela->delete();
        return redirect()->route('admin.master.index', ['tab' => 'kelas'])->with('success', 'Kelas perkuliahan berhasil dihapus.');
    }
}
