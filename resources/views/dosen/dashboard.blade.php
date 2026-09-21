@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Selamat Datang, {{ $dosen->nama_lengkap }}</h1>
        <div class="page-subtitle">Berikut ringkasan hasil evaluasi kinerja perkuliahan Anda pada periode {{ $periodeAktif ? $periodeAktif->nama_periode : 'aktif' }}.</div>
    </div>
    <div>
        <span class="badge badge-purple" style="padding: 6px 12px; font-size: 11px;">
            NIDN: {{ $dosen->nidn }} &bull; {{ $dosen->programStudi->nama_prodi ?? '-' }}
        </span>
    </div>
</div>

<!-- STAT CARDS -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-ico ico-blue">📄</div>
        <div>
            <div class="stat-num">{{ $totalEvaluasi }}</div>
            <div class="stat-lbl">Total Responden Mahasiswa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-green">★</div>
        <div>
            <div class="stat-num">{{ number_format($rataRata, 2) }} <span style="font-size: 12px; color: var(--muted);">/ 5.0</span></div>
            <div class="stat-lbl">Rata-rata Skor Keseluruhan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-purple">🏫</div>
        <div>
            <div class="stat-num">{{ $kelasList->count() }}</div>
            <div class="stat-lbl">Kelas Diampu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ico ico-amber">🏆</div>
        <div>
            @php
                $predikat = 'Belum Ada Penilaian';
                if ($totalEvaluasi > 0) {
                    if ($rataRata >= 4.5) $predikat = 'Sangat Baik';
                    elseif ($rataRata >= 3.75) $predikat = 'Baik';
                    elseif ($rataRata >= 3.0) $predikat = 'Cukup';
                    else $predikat = 'Kurang';
                }
            @endphp
            <div class="stat-num" style="font-size: 16px;">{{ $predikat }}</div>
            <div class="stat-lbl">Predikat Kinerja</div>
        </div>
    </div>
</div>

<!-- 2 COLUMN: ASPECT BAR GRAPH & KELAS DIAMPU -->
<div style="display: grid; grid-template-columns: 1.1fr 1fr; gap: 20px; align-items: stretch; margin-bottom: 22px;">
    <!-- Grafik 4 Kompetensi Dosen -->
    <div class="panel" style="margin-bottom: 0; display: flex; flex-direction: column;">
        <div class="panel-header">
            <h2 class="panel-title">Penilaian Berdasarkan 4 Aspek Kompetensi</h2>
        </div>

        <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 160px; padding: 20px 10px 10px; border-bottom: 1px solid var(--border);">
            <!-- Pedagogik -->
            <div style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                <div style="font-size: 11px; font-weight: 700; margin-bottom: 6px; color: var(--navy);">
                    {{ number_format($pedagogik, 2) }}
                </div>
                <div style="width: 36px; height: {{ max(10, ($pedagogik / 5) * 110) }}px; background: #2F80ED; border-radius: 6px 6px 0 0; transition: height 0.5s;"></div>
                <div style="font-size: 10.5px; color: var(--muted); margin-top: 8px; font-weight: 600;">Pedagogik</div>
            </div>

            <!-- Profesional -->
            <div style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                <div style="font-size: 11px; font-weight: 700; margin-bottom: 6px; color: var(--navy);">
                    {{ number_format($profesional, 2) }}
                </div>
                <div style="width: 36px; height: {{ max(10, ($profesional / 5) * 110) }}px; background: #7C4FE0; border-radius: 6px 6px 0 0; transition: height 0.5s;"></div>
                <div style="font-size: 10.5px; color: var(--muted); margin-top: 8px; font-weight: 600;">Profesional</div>
            </div>

            <!-- Kepribadian -->
            <div style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                <div style="font-size: 11px; font-weight: 700; margin-bottom: 6px; color: var(--navy);">
                    {{ number_format($kepribadian, 2) }}
                </div>
                <div style="width: 36px; height: {{ max(10, ($kepribadian / 5) * 110) }}px; background: #1E9E62; border-radius: 6px 6px 0 0; transition: height 0.5s;"></div>
                <div style="font-size: 10.5px; color: var(--muted); margin-top: 8px; font-weight: 600;">Kepribadian</div>
            </div>

            <!-- Sosial -->
            <div style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                <div style="font-size: 11px; font-weight: 700; margin-bottom: 6px; color: var(--navy);">
                    {{ number_format($sosial, 2) }}
                </div>
                <div style="width: 36px; height: {{ max(10, ($sosial / 5) * 110) }}px; background: #D97706; border-radius: 6px 6px 0 0; transition: height 0.5s;"></div>
                <div style="font-size: 10.5px; color: var(--muted); margin-top: 8px; font-weight: 600;">Sosial</div>
            </div>
        </div>

        <div style="margin-top: auto; padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 11px; color: var(--muted);">Skala Penilaian: 1.00 s/d 5.00</span>
            <a href="{{ route('dosen.evaluasi.index') }}" class="btn btn-navy btn-sm">Lihat Detail per Kelas →</a>
        </div>
    </div>

    <!-- Kelas yang Diampu (Menggantikan posisi Saran & Masukan) -->
    <div class="panel" style="margin-bottom: 0; display: flex; flex-direction: column;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title">Kelas yang Diampu</h2>
            <a href="{{ route('dosen.evaluasi.index') }}" style="font-size: 11.5px; color: var(--accent); text-decoration: none; font-weight: 600;">Lihat Semua →</a>
        </div>

        @if($kelasList->isEmpty())
            <div style="text-align: center; padding: 40px 20px; color: var(--muted); font-size: 12px; margin: auto;">
                Belum ada kelas yang diampu pada periode ini.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 10px; max-height: 250px; overflow-y: auto; padding-right: 2px;">
                @foreach($kelasList as $k)
                    @php
                        $respCount = $k->evaluasi->count();
                        $jawabanKelas = $k->evaluasi->flatMap->jawaban;
                        $avgSkor = $jawabanKelas->avg('skor') ?? 0;
                    @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; gap: 10px;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                <span class="badge badge-purple" style="font-size: 10.5px;">{{ $k->nama_kelas }}</span>
                                <span style="font-size: 10.5px; color: var(--muted);">{{ $k->mataKuliah->kode_matkul ?? '' }} &bull; {{ $k->mataKuliah->sks ?? 0 }} SKS</span>
                            </div>
                            <div style="font-size: 12.5px; font-weight: 700; color: var(--navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $k->mataKuliah->nama_matkul ?? '-' }}">
                                {{ $k->mataKuliah->nama_matkul ?? '-' }}
                            </div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                👥 {{ $respCount }} Responden Mahasiswa
                            </div>
                        </div>
                        <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0;">
                            <span style="font-size: 12.5px; font-weight: 700; color: var(--navy);">
                                ★ {{ number_format($avgSkor, 2) }}
                            </span>
                            <a href="{{ route('dosen.evaluasi.detail', $k->id) }}" class="btn btn-navy btn-sm" style="padding: 3px 8px; font-size: 10.5px;">
                                Detail →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Saran & Masukan Mahasiswa (Anonim) (Dipindahkan ke Bawah) -->
<div class="panel">
    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="panel-title">Saran & Masukan Mahasiswa (Anonim)</h2>
        <span style="font-size: 11.5px; color: var(--muted);">Feedback kuesioner mahasiswa terbaru</span>
    </div>

    @if($feedbacks->isEmpty())
        <div style="text-align: center; padding: 30px; color: var(--muted); font-size: 12px;">
            Belum ada saran atau masukan tertulis dari mahasiswa.
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 12px;">
            @foreach($feedbacks as $fb)
                <div style="padding: 14px; background: var(--bg); border-radius: 8px; border-left: 3px solid var(--accent); border-top: 1px solid var(--border); border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); font-size: 12px; display: flex; flex-direction: column; justify-content: space-between; gap: 8px;">
                    <p style="font-style: italic; color: var(--ink); line-height: 1.5;">"{{ $fb->saran_masukan }}"</p>
                    <div style="font-size: 10.5px; color: var(--muted); display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed var(--border); padding-top: 8px; margin-top: 4px;">
                        <span><strong>Kelas:</strong> {{ $fb->kelasMataKuliah->nama_kelas ?? '-' }} ({{ $fb->kelasMataKuliah->mataKuliah->nama_matkul ?? '-' }})</span>
                        <span>{{ $fb->tanggal_pengisian ? $fb->tanggal_pengisian->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
