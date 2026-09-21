@extends('layouts.app')

@section('title', 'Detail Evaluasi Kinerja Dosen')

@section('content')
<div class="page-header">
    <div>
        <div style="margin-bottom: 6px;">
            <a href="{{ route('mahasiswa.history.index') }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600;">
                ← Kembali ke Riwayat Evaluasi
            </a>
        </div>
        <h1 class="page-title">Detail Pengisian Evaluasi Dosen</h1>
        <div class="page-subtitle">Disimpan pada: {{ $evaluasi->tanggal_pengisian->format('d F Y, H:i') }} WIB</div>
    </div>
</div>

<!-- INFORMASI DOSEN -->
<div class="info-card-box">
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Nama Dosen:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $evaluasi->kelasMataKuliah->dosen->nama_lengkap ?? '-' }}</strong>
    </div>
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Mata Kuliah:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $evaluasi->kelasMataKuliah->mataKuliah->nama_matkul ?? '-' }}</strong>
    </div>
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Kelas:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $evaluasi->kelasMataKuliah->nama_kelas ?? '-' }}</strong>
    </div>
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Periode Akademik:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $evaluasi->kelasMataKuliah->periode->nama_periode ?? '-' }}</strong>
    </div>
</div>

<!-- JAWABAN TABLE -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Nilai yang Anda Berikan</h2>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>Butir Pertanyaan</th>
                    <th width="120">Kompetensi</th>
                    <th width="140">Skor Diberikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasi->jawaban as $idx => $j)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $j->pertanyaan->teks_pertanyaan ?? '-' }}</td>
                        <td>
                            <span class="badge badge-purple">{{ $j->pertanyaan->kategori ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-green" style="font-size: 11px;">
                                ★ {{ $j->skor }} &bull; {{ match($j->skor) { 1=>'Sangat Kurang', 2=>'Kurang', 3=>'Cukup', 4=>'Baik', 5=>'Sangat Baik', default=>'-' } }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($evaluasi->saran_masukan)
        <div style="margin-top: 20px; padding: 14px; background: var(--bg); border-radius: 8px; border-left: 3px solid var(--accent); border-top: 1px solid var(--border); border-right: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <strong style="font-size: 12px; color: var(--navy); display: block; margin-bottom: 4px;">Saran / Masukan Anda:</strong>
            <p style="font-size: 12px; color: var(--ink); font-style: italic;">"{{ $evaluasi->saran_masukan }}"</p>
        </div>
    @endif
</div>
@endsection
