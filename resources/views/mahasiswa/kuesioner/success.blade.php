@extends('layouts.app')

@section('title', 'Evaluasi Berhasil Dikirim')

@section('content')
<!-- STEPPER INDICATOR -->
<div class="stepper-container">
    <div style="display: flex; align-items: center; max-width: 600px; margin: 0 auto;">
        <!-- Step 1 (Done) -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--green); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">✓</div>
            <span style="font-size: 11px; font-weight: 600; color: var(--green);">Pilih Dosen</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--green); margin: 0 10px 18px;"></div>

        <!-- Step 2 (Done) -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--green); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">✓</div>
            <span style="font-size: 11px; font-weight: 600; color: var(--green);">Isi Kuesioner</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--green); margin: 0 10px 18px;"></div>

        <!-- Step 3 (Active) -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">3</div>
            <span style="font-size: 11px; font-weight: 600; color: var(--navy);">Selesai</span>
        </div>
    </div>
</div>

<div class="panel" style="text-align: center; padding: 50px 20px;">
    <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--green-bg); color: var(--green); display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 16px;">
        ✓
    </div>

    <h2 style="font-size: 20px; color: var(--navy); margin-bottom: 8px;">Terima Kasih, Evaluasi Anda Telah Tersimpan!</h2>
    <p style="color: var(--muted); font-size: 13px; max-width: 500px; margin: 0 auto 24px; line-height: 1.5;">
        Penilaian Anda terhadap dosen <strong>{{ $kelas->dosen->nama_lengkap ?? '-' }}</strong> pada mata kuliah <strong>{{ $kelas->mataKuliah->nama_matkul ?? '-' }}</strong> berhasil dicatat ke dalam sistem penjaminan mutu LP3I.
    </p>

    <div style="display: flex; gap: 12px; justify-content: center;">
        <a href="{{ route('mahasiswa.kuesioner.index') }}" class="btn btn-primary" style="padding: 10px 20px;">
            Isi Kuesioner Dosen Lain →
        </a>
        <a href="{{ route('mahasiswa.history.index') }}" class="btn btn-ghost" style="padding: 10px 20px;">
            Lihat Riwayat Pengisian
        </a>
    </div>
</div>
@endsection
