@extends('layouts.app')

@section('title', 'Hasil Evaluasi Kinerja')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Hasil Evaluasi Kinerja Perkuliahan</h1>
        <div class="page-subtitle">Rekap penilaian mahasiswa untuk setiap kelas mata kuliah yang Anda ampu.</div>
    </div>
</div>

<!-- FILTER PERIODE -->
<div class="panel" style="padding: 12px 18px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('dosen.evaluasi.index') }}" style="display: flex; gap: 10px; align-items: center;">
        <label class="form-label" style="margin: 0; font-size: 11.5px;">Pilih Periode Akademik:</label>
        <select name="periode_id" class="form-control" style="max-width: 250px;" onchange="this.form.submit()">
            @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                    {{ $p->nama_periode }} {{ $p->status === 'Aktif' ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<!-- LIST KELAS EVALUASI -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Daftar Kelas Mata Kuliah</h2>
    </div>

    @if(empty($rekapPerKelas))
        <div style="text-align: center; padding: 40px; color: var(--muted); font-size: 12px;">
            Anda tidak mengampu kelas pada periode ini.
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Responden</th>
                        <th>Pedagogik</th>
                        <th>Profesional</th>
                        <th>Kepribadian</th>
                        <th>Sosial</th>
                        <th>Rata-rata</th>
                        <th>Predikat</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPerKelas as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $item['kelas']->mataKuliah->nama_matkul ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $item['kelas']->mataKuliah->kode_matkul ?? '' }} &bull; {{ $item['kelas']->mataKuliah->sks ?? 0 }} SKS</span>
                            </td>
                            <td><span class="badge badge-purple">{{ $item['kelas']->nama_kelas }}</span></td>
                            <td><strong>{{ $item['total_responden'] }}</strong> Mahasiswa</td>
                            <td>{{ number_format($item['pedagogik'], 2) }}</td>
                            <td>{{ number_format($item['profesional'], 2) }}</td>
                            <td>{{ number_format($item['kepribadian'], 2) }}</td>
                            <td>{{ number_format($item['sosial'], 2) }}</td>
                            <td>
                                <strong style="color: var(--navy); font-size: 13px;">
                                    ★ {{ number_format($item['rata_rata'], 2) }}
                                </strong>
                            </td>
                            <td>
                                @php
                                    $badge = match($item['predikat']) {
                                        'Sangat Baik' => 'badge-green',
                                        'Baik' => 'badge-blue',
                                        'Cukup' => 'badge-amber',
                                        'Kurang' => 'badge-red',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $item['predikat'] }}</span>
                            </td>
                            <td>
                                <a href="{{ route('dosen.evaluasi.detail', $item['kelas']) }}" class="btn btn-navy btn-sm">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
