@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Evaluasi')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Laporan & Rekapitulasi Evaluasi Dosen</h1>
        <div class="page-subtitle">Analisis hasil penilaian kinerja dosen LP3I berdasarkan 4 aspek kompetensi.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.laporan.export_excel', request()->all()) }}" class="btn btn-navy">
            📥 Export Excel (.xlsx)
        </a>
        <a href="{{ route('admin.laporan.export_pdf', request()->all()) }}" class="btn btn-danger" target="_blank">
            📄 Export PDF
        </a>
    </div>
</div>

<!-- FILTERS -->
<div class="panel" style="padding: 14px 18px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.laporan.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 180px;">
            <label class="form-label" style="font-size: 11px;">Periode Akademik</label>
            <select name="periode_id" class="form-control">
                <option value="">-- Semua Periode --</option>
                @foreach($periodes as $p)
                    <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_periode }} {{ $p->status === 'Aktif' ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label class="form-label" style="font-size: 11px;">Program Studi</label>
            <select name="prodi_id" class="form-control">
                <option value="">-- Semua Program Studi --</option>
                @foreach($prodis as $pr)
                    <option value="{{ $pr->id }}" {{ $prodiId == $pr->id ? 'selected' : '' }}>
                        {{ $pr->nama_prodi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label class="form-label" style="font-size: 11px;">Dosen</label>
            <select name="dosen_id" class="form-control">
                <option value="">-- Semua Dosen --</option>
                @foreach($dosens as $d)
                    <option value="{{ $d->id }}" {{ $dosenId == $d->id ? 'selected' : '' }}>
                        {{ $d->nama_lengkap }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<!-- STAT ROW -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-ico ico-blue">👥</div>
        <div>
            <div class="stat-num">{{ $totalRespondenGlobal }}</div>
            <div class="stat-lbl">Total Respon Mahasiswa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-green">★</div>
        <div>
            <div class="stat-num">{{ number_format($rataRataGlobal, 2) }} <span style="font-size: 12px; color: var(--muted);">/ 5.0</span></div>
            <div class="stat-lbl">Rata-rata Skor Institusi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-purple">🧑‍🏫</div>
        <div>
            <div class="stat-num">{{ $totalDosenDievaluasi }}</div>
            <div class="stat-lbl">Dosen Telah Dievaluasi</div>
        </div>
    </div>
</div>

<!-- DETAILED RECAP TABLE -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Tabel Rekapitulasi Evaluasi per Dosen & Mata Kuliah</h2>
    </div>

    @if(empty($rekapData))
        <div style="text-align: center; padding: 40px; color: var(--muted); font-size: 12px;">
            Tidak ditemukan data evaluasi sesuai filter yang dipilih.
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Dosen & NIDN</th>
                        <th>Mata Kuliah & Kelas</th>
                        <th>Responden</th>
                        <th>Pedagogik</th>
                        <th>Profesional</th>
                        <th>Kepribadian</th>
                        <th>Sosial</th>
                        <th>Rata-rata Total</th>
                        <th>Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $idx => $r)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $r['dosen']->nama_lengkap }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">NIDN: {{ $r['dosen']->nidn }} &bull; {{ $r['dosen']->programStudi->nama_prodi ?? '-' }}</span>
                            </td>
                            <td>
                                <strong>{{ $r['kelas']->mataKuliah->nama_matkul ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">Kelas: {{ $r['kelas']->nama_kelas }}</span>
                            </td>
                            <td>
                                <strong>{{ $r['total_responden'] }}</strong> Mhs
                            </td>
                            <td>{{ number_format($r['pedagogik'], 2) }}</td>
                            <td>{{ number_format($r['profesional'], 2) }}</td>
                            <td>{{ number_format($r['kepribadian'], 2) }}</td>
                            <td>{{ number_format($r['sosial'], 2) }}</td>
                            <td>
                                <strong style="font-size: 13px; color: var(--navy);">
                                    ★ {{ number_format($r['rata_rata'], 2) }}
                                </strong>
                            </td>
                            <td>
                                @php
                                    $badge = match($r['predikat']) {
                                        'Sangat Baik' => 'badge-green',
                                        'Baik' => 'badge-blue',
                                        'Cukup' => 'badge-amber',
                                        'Kurang' => 'badge-red',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $r['predikat'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
