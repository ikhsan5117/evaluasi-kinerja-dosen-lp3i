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
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Mahasiswa</th>
                            <th>Dosen & Mata Kuliah</th>
                            <th>Skor Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvaluasi as $eval)
                            <tr>
                                <td style="color: var(--muted); font-size: 11px;">
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
                                <td>
                                    <span class="badge badge-green" style="font-size: 11px;">
                                        ★ {{ $eval->rata_rata }} / 5.0
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                <text x="21" y="24" text-anchor="middle" font-size="7.5" font-weight="800" fill="#1E3A5F">
                    {{ $partisipasiPersen }}%
                </text>
            </svg>

            <div style="font-size: 11.5px; margin-top: 14px; text-align: left; display: flex; flex-direction: column; gap: 6px; padding: 0 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span><span style="color: #2F80ED; font-size: 14px;">●</span> Terisi:</span>
                    <strong>{{ $totalEvaluasiAktif }} Evaluasi</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span><span style="color: #E4EEFD; font-size: 14px;">●</span> Target Respon:</span>
                    <strong>{{ max(1, $totalMahasiswa * 4) }} Evaluasi</strong>
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
