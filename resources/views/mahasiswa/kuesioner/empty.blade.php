@extends('layouts.app')

@section('title', 'Kuesioner Tidak Tersedia')

@section('content')
<div class="panel" style="text-align: center; padding: 60px 20px;">
    <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
    <h2 style="font-size: 18px; color: var(--navy); margin-bottom: 8px;">Tidak Ada Kuesioner Aktif</h2>
    <p style="color: var(--muted); font-size: 13px; max-width: 420px; margin: 0 auto 20px;">
        Saat ini belum ada instrumen kuesioner evaluasi dosen yang dibuka untuk periode akademik sekarang.
    </p>
    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-navy">Kembali ke Dashboard</a>
</div>
@endsection
