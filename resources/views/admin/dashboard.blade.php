@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Admin / Akademik</h1>
        <div class="page-subtitle">Kelola data master, instrumen kuesioner, dan pantau rekapitulasi evaluasi dosen LP3I.</div>
    </div>
    <div>
        <span class="badge badge-blue" style="padding: 6px 12px; font-size: 11px;">
            📅 Periode: {{ $periodeAktif ? $periodeAktif->nama_periode : 'Belum Ada Periode Aktif' }}
        </span>
    </div>
</div>

<!-- STAT CARDS -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-ico ico-blue">🧑‍🏫</div>
        <div>
            <div class="stat-num">{{ $totalDosen }}</div>
            <div class="stat-lbl">Total Dosen</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-purple">🎓</div>
        <div>
            <div class="stat-num">{{ $totalMahasiswa }}</div>
            <div class="stat-lbl">Total Mahasiswa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-green">📋</div>
        <div>
            <div class="stat-num">{{ $totalKuesioner }}</div>
            <div class="stat-lbl">Instrumen Kuesioner</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-amber">📝</div>
        <div>
            <div class="stat-num">{{ $totalEvaluasiAktif }}</div>
            <div class="stat-lbl">Evaluasi Terisi (Periode Ini)</div>
        </div>
    </div>
</div>

<!-- 2 COLUMN PANELS -->
<div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px; align-items: start;">
    <!-- Aktivitas Evaluasi Masuk Terbaru -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Aktivitas Evaluasi Terbaru</h2>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-ghost btn-sm">Lihat Semua Laporan →</a>
        </div>

        @if($recentEvaluasi->isEmpty())
            <div style="text-align: center; padding: 30px; color: var(--muted); font-size: 12px;">
                Belum ada data evaluasi yang masuk pada periode ini.
            </div>
        @else
            {{-- Tabel dengan scroll horizontal + vertikal, dan pagination 6 baris --}}
            <div id="evalTableWrapper" style="overflow-x: auto; overflow-y: auto; max-height: 310px; border-radius: 10px; border: 1px solid var(--border);">
                <table id="evalTable" style="min-width: 560px; border-collapse: collapse; width: 100%;">
                    <thead style="position: sticky; top: 0; z-index: 2; background: var(--card-bg);">
                        <tr>
                            <th style="white-space: nowrap;">Waktu</th>
                            <th style="white-space: nowrap;">Mahasiswa</th>
                            <th style="white-space: nowrap;">Dosen & Mata Kuliah</th>
                            <th style="white-space: nowrap;">Skor Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody id="evalTbody">
                        @foreach($recentEvaluasi as $eval)
                            <tr class="eval-row" style="display: none;">
                                <td style="color: var(--muted); font-size: 11px; white-space: nowrap;">
                                    {{ $eval->created_at->diffForHumans() }}
                                </td>
                                <td>
                                    <strong>{{ $eval->mahasiswa->user->name ?? 'Mahasiswa' }}</strong><br>
                                    <span style="font-size: 10px; color: var(--muted);">{{ $eval->mahasiswa->nim ?? '-' }} ({{ $eval->mahasiswa->kelas ?? '-' }})</span>
                                </td>
                                <td>
                                    <strong>{{ $eval->kelasMataKuliah->dosen->nama_lengkap ?? '-' }}</strong><br>
                                    <span style="font-size: 10.5px; color: var(--muted);">{{ $eval->kelasMataKuliah->mataKuliah->nama_matkul ?? '-' }}</span>
                                </td>
                                <td style="white-space: nowrap;">
                                    <span class="badge badge-green" style="font-size: 11px;">
                                        ★ {{ $eval->rata_rata }} / 5.0
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Controls --}}
            <div id="evalPagination" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 4px 2px; flex-wrap: wrap; gap: 6px;">
                <div style="font-size: 11px; color: var(--muted);" id="evalPageInfo"></div>
                <div style="display: flex; gap: 6px; align-items: center;">
                    <button id="evalPrevBtn" onclick="evalChangePage(-1)"
                        style="padding: 4px 12px; border-radius: 7px; border: 1px solid var(--border);
                               background: var(--card-bg); color: var(--ink); cursor: pointer;
                               font-size: 12px; font-weight: 600; transition: all 0.15s;"
                        onmouseover="this.style.borderColor='#2F80ED'" onmouseout="this.style.borderColor='var(--border)'">
                        ← Prev
                    </button>

                    <div id="evalPageDots" style="display: flex; gap: 4px;"></div>

                    <button id="evalNextBtn" onclick="evalChangePage(1)"
                        style="padding: 4px 12px; border-radius: 7px; border: 1px solid var(--border);
                               background: var(--card-bg); color: var(--ink); cursor: pointer;
                               font-size: 12px; font-weight: 600; transition: all 0.15s;"
                        onmouseover="this.style.borderColor='#2F80ED'" onmouseout="this.style.borderColor='var(--border)'">
                        Next →
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Grafik Partisipasi Mahasiswa -->
    <div class="panel" style="text-align: center;">
        <div class="panel-header">
            <h2 class="panel-title">Tingkat Partisipasi Evaluasi</h2>
        </div>

        <div style="padding: 10px 0;">
            <svg width="150" height="150" viewBox="0 0 42 42" style="margin: 0 auto; display: block;">
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="#E4EEFD" stroke-width="6"/>
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="#2F80ED" stroke-width="6"
                        stroke-dasharray="{{ $partisipasiPersen }} {{ 100 - $partisipasiPersen }}"
                        stroke-dashoffset="25"
                        stroke-linecap="round"
                        transform="rotate(-90 21 21)"/>
                <text x="21" y="24" text-anchor="middle" font-size="7.5" font-weight="800" fill="var(--ink)">
                    {{ $partisipasiPersen }}%
                </text>
            </svg>

            <div style="font-size: 11.5px; margin-top: 14px; text-align: left; display: flex; flex-direction: column; gap: 6px; padding: 0 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span><span style="color: #2F80ED; font-size: 14px;">●</span> Mahasiswa Mengisi:</span>
                    <strong>{{ $mahasiswaMengisi ?? $totalEvaluasiAktif }} Mahasiswa</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span><span style="color: #E4EEFD; font-size: 14px;">●</span> Target Respon:</span>
                    <strong>{{ $totalMahasiswa }} Mahasiswa</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 10.5px; color: var(--muted); border-top: 1px dashed var(--border); padding-top: 4px; margin-top: 2px;">
                    <span>Total Evaluasi Masuk:</span>
                    <strong>{{ $totalEvaluasiAktif }} Evaluasi</strong>
                </div>
            </div>

            <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: center;">
                <a href="{{ route('admin.master.index') }}" class="btn btn-ghost btn-sm">🗂 Kelola Data</a>
                <a href="{{ route('admin.laporan.export_excel') }}" class="btn btn-navy btn-sm">📥 Export Rekap</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const ROWS_PER_PAGE = 6;
    let currentPage = 1;

    const rows = Array.from(document.querySelectorAll('#evalTbody .eval-row'));
    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / ROWS_PER_PAGE);

    const pageInfo  = document.getElementById('evalPageInfo');
    const prevBtn   = document.getElementById('evalPrevBtn');
    const nextBtn   = document.getElementById('evalNextBtn');
    const dotsWrap  = document.getElementById('evalPageDots');
    const pagination = document.getElementById('evalPagination');

    // Sembunyikan pagination jika data <= 6 baris
    if (totalPages <= 1) {
        rows.forEach(r => r.style.display = '');
        if (pagination) pagination.style.display = 'none';
        return;
    }

    function renderPage(page) {
        currentPage = Math.max(1, Math.min(page, totalPages));
        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end   = start + ROWS_PER_PAGE;

        rows.forEach((row, idx) => {
            row.style.display = (idx >= start && idx < end) ? '' : 'none';
        });

        // Info teks: "Menampilkan 1–6 dari 50 data"
        const from = start + 1;
        const to   = Math.min(end, totalRows);
        if (pageInfo) pageInfo.textContent = `Menampilkan ${from}–${to} dari ${totalRows} data`;

        // Tombol disable
        if (prevBtn) prevBtn.disabled = currentPage === 1;
        if (nextBtn) nextBtn.disabled = currentPage === totalPages;

        // Dots navigasi halaman
        if (dotsWrap) {
            dotsWrap.innerHTML = '';
            // Tampilkan max 5 nomor halaman di sekitar halaman aktif
            let startDot = Math.max(1, currentPage - 2);
            let endDot   = Math.min(totalPages, startDot + 4);
            startDot = Math.max(1, endDot - 4);

            if (startDot > 1) {
                dotsWrap.appendChild(makeDot(1));
                if (startDot > 2) dotsWrap.appendChild(makeEllipsis());
            }
            for (let p = startDot; p <= endDot; p++) dotsWrap.appendChild(makeDot(p));
            if (endDot < totalPages) {
                if (endDot < totalPages - 1) dotsWrap.appendChild(makeEllipsis());
                dotsWrap.appendChild(makeDot(totalPages));
            }
        }

        // Scroll tabel ke atas saat ganti halaman
        const wrapper = document.getElementById('evalTableWrapper');
        if (wrapper) wrapper.scrollTop = 0;
    }

    function makeDot(p) {
        const btn = document.createElement('button');
        btn.textContent = p;
        const isActive = p === currentPage;
        btn.style.cssText = `
            width: 26px; height: 26px; border-radius: 6px;
            border: 1px solid ${isActive ? '#2F80ED' : 'var(--border)'};
            background: ${isActive ? '#2F80ED' : 'var(--card-bg)'};
            color: ${isActive ? '#fff' : 'var(--ink)'};
            font-size: 11px; font-weight: 600;
            cursor: ${isActive ? 'default' : 'pointer'};
            transition: all 0.15s;
        `;
        if (!isActive) {
            btn.addEventListener('click', () => renderPage(p));
            btn.onmouseover = () => { btn.style.borderColor = '#2F80ED'; };
            btn.onmouseout  = () => { btn.style.borderColor = 'var(--border)'; };
        }
        return btn;
    }

    function makeEllipsis() {
        const span = document.createElement('span');
        span.textContent = '...';
        span.style.cssText = 'font-size: 11px; color: var(--muted); align-self: center; padding: 0 2px;';
        return span;
    }

    window.evalChangePage = function (delta) { renderPage(currentPage + delta); };

    // Render halaman pertama
    renderPage(1);
})();
</script>
@endpush
