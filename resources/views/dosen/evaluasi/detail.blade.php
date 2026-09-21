@extends('layouts.app')

@section('title', 'Detail Evaluasi: ' . ($kelas->mataKuliah->nama_matkul ?? 'Kelas'))

@section('content')
<div class="page-header">
    <div>
        <div style="margin-bottom: 6px;">
            <a href="{{ route('dosen.evaluasi.index') }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600;">
                ← Kembali ke Daftar Kelas
            </a>
        </div>
        <h1 class="page-title">{{ $kelas->mataKuliah->nama_matkul ?? 'Mata Kuliah' }} &mdash; Kelas {{ $kelas->nama_kelas }}</h1>
        <div class="page-subtitle">
            Periode: <strong>{{ $kelas->periode->nama_periode ?? '-' }}</strong> &bull;
            Total Responden: <strong>{{ $evaluasis->count() }}</strong> Mahasiswa
        </div>
    </div>
</div>

<!-- QUESTION BY QUESTION BREAKDOWN -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Rincian Penilaian per Butir Pertanyaan (Skala 1 - 5)</h2>
    </div>

    @if($pertanyaanStats->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--muted); font-size: 12px;">
            Belum ada data penilaian yang masuk untuk kelas ini.
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Butir Pertanyaan</th>
                        <th width="120">Kompetensi</th>
                        <th width="100">Rata-rata</th>
                        <th width="200">Distribusi Skor (1 - 5)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pertanyaanStats as $stat)
                        <tr>
                            <td>{{ $stat['pertanyaan']->nomor_urut }}</td>
                            <td style="font-size: 12.5px;">
                                {{ $stat['pertanyaan']->teks_pertanyaan }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($stat['pertanyaan']->kategori) {
                                        'Pedagogik' => 'badge-blue',
                                        'Profesional' => 'badge-purple',
                                        'Kepribadian' => 'badge-green',
                                        'Sosial' => 'badge-amber',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $stat['pertanyaan']->kategori }}</span>
                            </td>
                            <td>
                                <strong style="font-size: 13px; color: var(--navy);">
                                    ★ {{ number_format($stat['rata_rata'], 2) }}
                                </strong>
                            </td>
                            <td>
                                <div style="display: flex; gap: 4px; font-size: 10px; color: var(--muted);">
                                    <span>[1: {{ $stat['distribusi'][1] }}]</span>
                                    <span>[2: {{ $stat['distribusi'][2] }}]</span>
                                    <span>[3: {{ $stat['distribusi'][3] }}]</span>
                                    <span style="color: var(--accent); font-weight: 600;">[4: {{ $stat['distribusi'][4] }}]</span>
                                    <span style="color: var(--green); font-weight: 700;">[5: {{ $stat['distribusi'][5] }}]</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- ALL FEEDBACK -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Semua Masukan & Saran Mahasiswa (Anonim)</h2>
    </div>

    @if($feedbacks->isEmpty())
        <div style="text-align: center; padding: 30px; color: var(--muted); font-size: 12px;">
            Belum ada saran atau masukan tertulis dari mahasiswa pada kelas ini.
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($feedbacks as $fb)
                <div style="padding: 14px; background: var(--bg); border-radius: 8px; border-left: 3px solid var(--accent); font-size: 12px;">
                    <p style="font-style: italic; color: var(--ink); margin-bottom: 6px;">"{{ $fb->saran_masukan }}"</p>
                    <span style="font-size: 10.5px; color: var(--muted);">Tanggal: {{ $fb->tanggal_pengisian->format('d F Y') }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
