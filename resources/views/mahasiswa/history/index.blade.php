@extends('layouts.app')

@section('title', 'Riwayat Pengisian Evaluasi')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Riwayat Pengisian Kuesioner</h1>
        <div class="page-subtitle">Daftar evaluasi kinerja dosen yang telah Anda selesaikan.</div>
    </div>
</div>

<!-- FILTER PERIODE -->
<div class="panel" style="padding: 12px 18px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('mahasiswa.history.index') }}" style="display: flex; gap: 10px; align-items: center;">
        <label class="form-label" style="margin: 0; font-size: 11.5px;">Filter Periode:</label>
        <select name="periode_id" class="form-control" style="max-width: 250px;" onchange="this.form.submit()">
            <option value="">-- Semua Periode --</option>
            @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                    {{ $p->nama_periode }} {{ $p->status === 'Aktif' ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<div class="panel">
    @if($histories->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--muted); font-size: 12px;">
            Belum ada riwayat evaluasi yang Anda isi.
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Tanggal Pengisian</th>
                        <th>Dosen Pengampu</th>
                        <th>Mata Kuliah & Kelas</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $idx => $h)
                        <tr>
                            <td>{{ $histories->firstItem() + $idx }}</td>
                            <td>{{ $h->tanggal_pengisian ? $h->tanggal_pengisian->format('d M Y') : '-' }}</td>
                            <td>
                                <strong>{{ $h->kelasMataKuliah->dosen->nama_lengkap ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">NIDN: {{ $h->kelasMataKuliah->dosen->nidn ?? '-' }}</span>
                            </td>
                            <td>
                                <strong>{{ $h->kelasMataKuliah->mataKuliah->nama_matkul ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">Kelas: {{ $h->kelasMataKuliah->nama_kelas ?? '-' }}</span>
                            </td>
                            <td><span class="badge badge-purple">{{ $h->kelasMataKuliah->periode->nama_periode ?? '-' }}</span></td>
                            <td><span class="badge badge-green">✓ Sudah Diisi</span></td>
                            <td>
                                <a href="{{ route('mahasiswa.history.show', $h) }}" class="btn btn-ghost btn-sm">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-container">{{ $histories->links() }}</div>
    @endif
</div>
@endsection
