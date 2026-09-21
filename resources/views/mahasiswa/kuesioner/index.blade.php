@extends('layouts.app')

@section('title', 'Isi Kuesioner Evaluasi Dosen')

@section('content')
<!-- STEPPER INDICATOR -->
<div class="stepper-container">
    <div style="display: flex; align-items: center; max-width: 600px; margin: 0 auto;">
        <!-- Step 1 (Active) -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">1</div>
            <span style="font-size: 11px; font-weight: 600; color: var(--navy);">Pilih Dosen</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border); margin: 0 10px 18px;"></div>

        <!-- Step 2 -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--blue-bg); color: var(--muted); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">2</div>
            <span style="font-size: 11px; color: var(--muted);">Isi Kuesioner</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border); margin: 0 10px 18px;"></div>

        <!-- Step 3 -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--blue-bg); color: var(--muted); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">3</div>
            <span style="font-size: 11px; color: var(--muted);">Selesai</span>
        </div>
    </div>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">Pilih Dosen &amp; Mata Kuliah yang Ingin Dievaluasi</h1>
        <div class="page-subtitle">Berikut daftar dosen &amp; mata kuliah yang mengajar di kelas Anda.</div>
    </div>
    @if(isset($namaKelasFilter) && $namaKelasFilter)
    <span style="font-size: 11px; background: var(--blue-bg); color: var(--navy); padding: 6px 14px; border-radius: 20px; border: 1px solid var(--border); display: flex; align-items: center; gap: 5px; white-space: nowrap;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        Filter Kelas: <b>{{ $namaKelasFilter }}</b>
    </span>
    @endif
</div>

<div class="panel">
    @if($kelasList->isEmpty())
        <div style="text-align: center; padding: 50px 24px;">
            <div style="font-size: 40px; margin-bottom: 14px;">📭</div>
            <div style="font-weight: 700; color: var(--navy); font-size: 15px; margin-bottom: 8px;">
                Belum Ada Jadwal untuk Kelas {{ $namaKelasFilter ?? ($mahasiswa->kelas ?? '') }}
            </div>
            <div style="font-size: 12px; color: var(--muted); line-height: 1.6;">
                Jadwal mata kuliah &amp; dosen untuk kelas Anda belum diinput pada periode ini.<br>
                Silakan hubungi admin jika Anda merasa ada yang keliru.
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Dosen Pengampu</th>
                        <th>Mata Kuliah &amp; SKS</th>
                        <th>Ruangan &amp; Jadwal</th>
                        <th>Status</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelasList as $idx => $k)
                        @php
                            $sudah = in_array($k->id, $evaluasiSudah);
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $k->dosen->nama_lengkap ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">NIDN: {{ $k->dosen->nidn ?? '-' }}</span>
                            </td>
                            <td>
                                <strong>{{ $k->mataKuliah->nama_matkul ?? '-' }}</strong><br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $k->mataKuliah->sks ?? 0 }} SKS</span>
                            </td>
                            <td>
                                {{ $k->ruangan ?? 'Ruang TBA' }}<br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $k->jadwal ?? '-' }}</span>
                            </td>
                            <td>
                                @if($sudah)
                                    <span class="badge badge-green">✓ Sudah Diisi</span>
                                @else
                                    <span class="badge badge-red">! Belum Diisi</span>
                                @endif
                            </td>
                            <td>
                                @if($sudah)
                                    <span class="btn btn-ghost btn-sm" style="color: var(--green); border-color: var(--green);">Selesai ✓</span>
                                @else
                                    <a href="{{ route('mahasiswa.kuesioner.fill', $k) }}" class="btn btn-primary btn-sm">
                                        Mulai Menilai →
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

