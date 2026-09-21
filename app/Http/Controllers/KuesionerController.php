<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Models\Periode;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KuesionerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Kuesioner::with(['periode', 'creator'])->withCount('pertanyaan');

        if ($search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
        }

        $kuesioners = $query->latest()->paginate(10);
        $periodes = Periode::latest()->get();

        return view('admin.kuesioner.index', compact('kuesioners', 'periodes', 'search'));
    }

    public function show(Kuesioner $kuesioner)
    {
        $kuesioner->load(['periode', 'pertanyaan' => function ($q) {
            $q->orderBy('nomor_urut');
        }]);

        return view('admin.kuesioner.show', compact('kuesioner'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:100',
            'periode_id' => 'required|exists:periode,id',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $validated['created_by'] = Auth::id();

        if ($validated['status'] === 'Aktif') {
            Kuesioner::where('periode_id', $validated['periode_id'])->update(['status' => 'Nonaktif']);
        }

        $kuesioner = Kuesioner::create($validated);

        return redirect()->route('admin.kuesioner.show', $kuesioner)->with('success', 'Kuesioner berhasil dibuat. Silakan tambahkan butir pertanyaan.');
    }

    public function update(Request $request, Kuesioner $kuesioner)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:100',
            'periode_id' => 'required|exists:periode,id',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        if ($validated['status'] === 'Aktif') {
            Kuesioner::where('periode_id', $validated['periode_id'])
                ->where('id', '!=', $kuesioner->id)
                ->update(['status' => 'Nonaktif']);
        }

        $kuesioner->update($validated);

        return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner berhasil diperbarui.');
    }

    public function destroy(Kuesioner $kuesioner)
    {
        $kuesioner->delete();
        return redirect()->route('admin.kuesioner.index')->with('success', 'Kuesioner berhasil dihapus.');
    }

    public function toggleStatus(Kuesioner $kuesioner)
    {
        $newStatus = $kuesioner->status === 'Aktif' ? 'Nonaktif' : 'Aktif';
        if ($newStatus === 'Aktif') {
            Kuesioner::where('periode_id', $kuesioner->periode_id)->update(['status' => 'Nonaktif']);
        }
        $kuesioner->update(['status' => $newStatus]);

        return back()->with('success', "Status kuesioner berhasil diubah menjadi {$newStatus}.");
    }

    // --- PERTANYAAN CRUD ---
    public function storePertanyaan(Request $request, Kuesioner $kuesioner)
    {
        $validated = $request->validate([
            'teks_pertanyaan' => 'required|string',
            'kategori' => 'required|in:Pedagogik,Metode Pembelajaran,Profesional,Kepribadian,Sosial',
            'nomor_urut' => 'required|integer|min:1',
        ]);

        $kuesioner->pertanyaan()->create($validated);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updatePertanyaan(Request $request, Pertanyaan $pertanyaan)
    {
        $validated = $request->validate([
            'teks_pertanyaan' => 'required|string',
            'kategori' => 'required|in:Pedagogik,Metode Pembelajaran,Profesional,Kepribadian,Sosial',
            'nomor_urut' => 'required|integer|min:1',
        ]);

        $pertanyaan->update($validated);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroyPertanyaan(Pertanyaan $pertanyaan)
    {
        $pertanyaan->delete();
        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Download Excel template for Kuesioner questions.
     */
    public function downloadTemplate(\App\Services\ExcelTemplateService $templateService)
    {
        return $templateService->download('pertanyaan');
    }

    /**
     * Import Kuesioner questions from Excel file.
     */
    public function importPertanyaan(Request $request, Kuesioner $kuesioner)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (empty($rows)) {
                return back()->with('error', 'File Excel kosong.');
            }

            \Illuminate\Support\Facades\DB::beginTransaction();

            $headerIdx = 0;
            for ($r = 0; $r < min(5, count($rows)); $r++) {
                $rowLower = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$r]);
                foreach ($rowLower as $cell) {
                    if (str_contains($cell, 'pertanyaan') || str_contains($cell, 'butir')) {
                        $headerIdx = $r;
                        break 2;
                    }
                }
            }

            $currentMaxOrder = $kuesioner->pertanyaan()->max('nomor_urut') ?? 0;
            $totalImported = 0;

            for ($i = $headerIdx + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $noUrutInput = trim((string)($row[0] ?? ''));
                $teks = trim((string)($row[1] ?? ''));
                $kategori = trim((string)($row[2] ?? 'Pedagogik'));

                // If column 0 is empty but column 1 has text, shift or use incremental
                if (empty($teks) && !empty($noUrutInput) && !is_numeric($noUrutInput)) {
                    $teks = $noUrutInput;
                    $noUrutInput = '';
                }

                if (empty($teks) || str_contains(strtolower($teks), 'teks butir') || str_contains(strtolower($teks), 'contoh')) {
                    continue;
                }

                // Normalize category
                $katValid = match (strtolower($kategori)) {
                    'profesional', 'professional' => 'Profesional',
                    'kepribadian', 'personality' => 'Kepribadian',
                    'sosial', 'social' => 'Sosial',
                    'pedagogik' => 'Pedagogik',
                    default => 'Metode Pembelajaran',
                };

                $nomorUrut = is_numeric($noUrutInput) && (int)$noUrutInput > 0
                    ? (int)$noUrutInput
                    : ++$currentMaxOrder;

                if ($nomorUrut > $currentMaxOrder) {
                    $currentMaxOrder = $nomorUrut;
                }

                $kuesioner->pertanyaan()->create([
                    'teks_pertanyaan' => $teks,
                    'kategori' => $katValid,
                    'nomor_urut' => $nomorUrut,
                ]);

                $totalImported++;
            }

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', "Import Butir Pertanyaan Selesai! Berhasil menambahkan {$totalImported} butir pertanyaan kuesioner.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal memproses file Excel Pertanyaan: ' . $e->getMessage());
        }
    }
}
