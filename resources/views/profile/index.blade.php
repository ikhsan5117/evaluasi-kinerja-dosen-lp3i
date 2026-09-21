@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Profil Pengguna</h1>
        <div class="page-subtitle">Informasi akun dan pengaturan kata sandi Anda</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
    <!-- Detail Profil -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Informasi Akun</h2>
            <span class="badge badge-blue">{{ strtoupper($user->role) }}</span>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                <small style="font-size: 10.5px; color: var(--muted);">Email terdaftar di sistem LP3I dan tidak dapat diubah sendiri.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
            </div>

            @if($user->isDosen() && $user->dosen)
                <div class="form-group">
                    <label class="form-label">NIDN</label>
                    <input type="text" class="form-control" value="{{ $user->dosen->nidn }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <input type="text" class="form-control" value="{{ $user->dosen->programStudi->nama_prodi ?? '-' }}" disabled>
                </div>
            @elseif($user->isMahasiswa() && $user->mahasiswa)
                <div class="form-group">
                    <label class="form-label">NIM</label>
                    <input type="text" class="form-control" value="{{ $user->mahasiswa->nim }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi / Kelas</label>
                    <input type="text" class="form-control" value="{{ ($user->mahasiswa->programStudi->nama_prodi ?? '-') . ' / ' . $user->mahasiswa->kelas }}" disabled>
                </div>
            @endif

            <hr style="border: none; border-top: 1px solid var(--border); margin: 20px 0;">

            <h3 style="font-size: 13px; font-weight: 700; color: var(--navy); margin-bottom: 12px;">Ubah Password (Opsional)</h3>

            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini jika ingin mengubah">
            </div>

            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru">
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <!-- Ringkasan Info LP3I -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Pedoman Penggunaan</h2>
        </div>
        <div style="font-size: 12px; line-height: 1.6; color: var(--ink);">
            <p style="margin-bottom: 12px;"><strong>Kebijakan Evaluasi Dosen:</strong></p>
            <ul style="padding-left: 18px; margin-bottom: 16px; color: var(--muted);">
                <li>Evaluasi bersifat objektif, konstruktif, dan anonim bagi mahasiswa.</li>
                <li>Setiap masukan diperhitungkan untuk peningkatan mutu pembelajaran.</li>
                <li>Gunakan kredensial resmi LP3I dan jaga kerahasiaan kata sandi Anda.</li>
            </ul>

            <div style="padding: 12px; background: var(--blue-bg); border-radius: 8px; color: var(--navy); border: 1px solid var(--border);">
                <strong>💡 Bantuan Teknis:</strong><br>
                Jika terdapat kendala data akun atau jadwal perkuliahan, hubungi Bagian Akademik LP3I.
            </div>
        </div>
    </div>
</div>
@endsection
