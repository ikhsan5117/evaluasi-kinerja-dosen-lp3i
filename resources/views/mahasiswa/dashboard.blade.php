@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Selamat Datang, {{ $mahasiswa->user->name }}</h1>
        <div class="page-subtitle">Silakan isi kuesioner evaluasi kinerja dosen dan pantau riwayat pengisian Anda.</div>
    </div>
    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
        <span class="badge badge-blue" style="padding: 6px 12px; font-size: 11px;">
            NIM: {{ $mahasiswa->nim }} &bull; {{ $mahasiswa->kelas }} &bull; {{ $mahasiswa->programStudi->nama_prodi ?? '-' }}
        </span>
        @if($namaKelasFilter)
        <span style="font-size: 11px; background:#EFF6FF; color:#1D4ED8; padding:4px 10px; border-radius:20px; border:1px solid #BFDBFE; display:flex; align-items:center; gap:4px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Menampilkan data untuk kelas: <b>{{ $namaKelasFilter }}</b>
        </span>
        @endif
    </div>
</div>

<!-- STAT CARDS -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-ico ico-blue">📋</div>
        <div>
            <div class="stat-num">{{ $totalKelas }}</div>
            <div class="stat-lbl">Mata Kuliah / Dosen Tersedia</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-green">✓</div>
        <div>
            <div class="stat-num">{{ $totalSudah }}</div>
            <div class="stat-lbl">Sudah Diisi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-red">!</div>
        <div>
            <div class="stat-num">{{ $totalBelum }}</div>
            <div class="stat-lbl">Belum Diisi</div>
        </div>
    </div>
</div>

<!-- PANEL KUESIONER TERBARU -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Daftar Dosen & Mata Kuliah Semester Ini</h2>
        <a href="{{ route('mahasiswa.kuesioner.index') }}" class="btn btn-navy btn-sm">Mulai Isi Kuesioner →</a>
    </div>

    @if($kelasList->isEmpty())
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 36px; margin-bottom: 12px;">📭</div>
            <div style="font-weight: 700; color: var(--navy); margin-bottom: 6px;">
                Belum Ada Jadwal untuk Kelas {{ $namaKelasFilter ?? $mahasiswa->kelas }}
            </div>
            <div style="font-size: 12px; color: var(--muted);">
                Jadwal mata kuliah &amp; dosen untuk kelas Anda belum diinput pada periode ini.<br>
                Silakan hubungi admin jika ini dirasa keliru.
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen Pengampu</th>
                        <th>Ruangan & Jadwal</th>
                        <th>Status Pengisian</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelasList as $idx => $kelas)
                        @php
                            $isDone = in_array($kelas->id, $evaluasiSelesai);
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $kelas->mataKuliah->nama_matkul ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $kelas->mataKuliah->kode_matkul ?? '' }} &bull; {{ $kelas->mataKuliah->sks ?? 0 }} SKS</span>
                            </td>
                            <td>
                                <strong>{{ $kelas->dosen->nama_lengkap ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">NIDN: {{ $kelas->dosen->nidn ?? '-' }}</span>
                            </td>
                            <td>
                                {{ $kelas->ruangan ?? 'Ruang TBA' }}<br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $kelas->jadwal ?? '-' }}</span>
                            </td>
                            <td>
                                @if($isDone)
                                    <span class="badge badge-green">✓ Sudah Diisi</span>
                                @else
                                    <span class="badge badge-red">! Belum Diisi</span>
                                @endif
                            </td>
                            <td>
                                @if($isDone)
                                    <span class="btn btn-ghost btn-sm" style="color: var(--green); border-color: var(--green);">Selesai</span>
                                @else
                                    <a href="{{ route('mahasiswa.kuesioner.fill', $kelas) }}" class="btn btn-primary btn-sm">
                                        Isi Evaluasi
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
