@extends('layouts.app')

@section('title', 'Isi Kuesioner Evaluasi Dosen')

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

        <!-- Step 2 (Active) -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">2</div>
            <span style="font-size: 11px; font-weight: 600; color: var(--navy);">Isi Kuesioner</span>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border); margin: 0 10px 18px;"></div>

        <!-- Step 3 -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--blue-bg); color: var(--muted); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">3</div>
            <span style="font-size: 11px; color: var(--muted);">Selesai</span>
        </div>
    </div>
</div>

<!-- INFORMASI DOSEN & KELAS -->
<div class="info-card-box">
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Nama Dosen:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $kelas->dosen->nama_lengkap ?? '-' }}</strong>
    </div>
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Mata Kuliah:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $kelas->mataKuliah->nama_matkul ?? '-' }}</strong>
    </div>
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Kelas / Ruangan:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $kelas->nama_kelas }} ({{ $kelas->ruangan ?? 'TBA' }})</strong>
    </div>
    <div>
        <span style="display: block; font-size: 11px; color: var(--muted);">Periode Akademik:</span>
        <strong style="font-size: 14px; color: var(--navy);">{{ $kelas->periode->nama_periode ?? '-' }}</strong>
    </div>
</div>

<!-- FORM EVALUASI -->
<form action="{{ route('mahasiswa.kuesioner.store', $kelas) }}" method="POST">
    @csrf

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Daftar Pertanyaan Evaluasi</h2>
                <div style="font-size: 11.5px; color: var(--muted); margin-top: 2px;">
                    Skala: <strong>1</strong> (Sangat Kurang), <strong>2</strong> (Kurang), <strong>3</strong> (Cukup), <strong>4</strong> (Baik), <strong>5</strong> (Sangat Baik)
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($kuesioner->pertanyaan as $idx => $p)
                <div style="padding: 16px 18px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 10px;">
                        <div style="font-size: 13px; font-weight: 600; color: var(--ink);">
                            <span style="color: var(--accent); margin-right: 6px;">{{ $p->nomor_urut }}.</span>
                            {{ $p->teks_pertanyaan }}
                        </div>
                        <span class="badge badge-purple" style="font-size: 9.5px; flex-shrink: 0;">{{ $p->kategori }}</span>
                    </div>

                    <!-- LIKERT OPTIONS (1 - 5) -->
                    <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center; padding-left: 20px;">
                        @for($score = 1; $score <= 5; $score++)
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; cursor: pointer; padding: 6px 10px; background: var(--card-bg); color: var(--ink); border: 1px solid var(--border); border-radius: 6px; transition: all 0.15s;">
                                <input type="radio" name="jawaban[{{ $p->id }}]" value="{{ $score }}" required style="cursor: pointer;">
                                <span>{{ $score }} &bull; {{ match($score) { 1=>'Sangat Kurang', 2=>'Kurang', 3=>'Cukup', 4=>'Baik', 5=>'Sangat Baik' } }}</span>
                            </label>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>

        <!-- SARAN & MASUKAN -->
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border);">
            <label class="form-label" style="font-size: 13px; font-weight: 700; color: var(--navy);">
                Saran & Masukan untuk Dosen (Opsional / Anonim)
            </label>
            <textarea name="saran_masukan" class="form-control" placeholder="Tuliskan kritik dan saran yang membangun untuk perbaikan proses pembelajaran..." style="min-height: 90px;"></textarea>
            <small style="font-size: 11px; color: var(--muted); margin-top: 4px; display: block;">
                🔒 Identitas Anda dijamin aman dan masukan disampaikan secara anonim kepada dosen.
            </small>
        </div>

        <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('mahasiswa.kuesioner.index') }}" class="btn btn-ghost">← Kembali</a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 13px;">
                Kirim Evaluasi Dosen ✓
            </button>
        </div>
    </div>
</form>
@endsection
