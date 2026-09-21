@extends('layouts.app')

@section('title', 'Kelola Kuesioner')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Kelola Instrumen Kuesioner</h1>
        <div class="page-subtitle">Buat dan atur kuesioner evaluasi dosen serta butir-butir pertanyaan per kompetensi.</div>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('admin.kuesioner.template.download') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 8px 14px; font-weight: 500; font-size: 13px; border-radius: 6px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Template Butir Soal
        </a>
        <button class="btn btn-primary" onclick="openModal('modalAddKuesioner')">+ Buat Kuesioner Baru</button>
    </div>
</div>

<div class="panel">
    <div style="margin-bottom: 16px; display: flex; gap: 10px; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <form method="GET" action="{{ route('admin.kuesioner.index') }}" style="display: flex; gap: 8px; flex: 1; max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Cari judul kuesioner..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-navy">Cari</button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>Judul Kuesioner</th>
                    <th>Kategori</th>
                    <th>Periode Akademik</th>
                    <th>Jumlah Butir Pertanyaan</th>
                    <th>Status</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kuesioners as $idx => $k)
                    <tr>
                        <td>{{ $kuesioners->firstItem() + $idx }}</td>
                        <td>
                            <strong>{{ $k->judul }}</strong>
                            @if($k->deskripsi)
                                <br><span style="font-size: 10.5px; color: var(--muted);">{{ Str::limit($k->deskripsi, 60) }}</span>
                            @endif
                        </td>
                        <td><span class="badge badge-blue">{{ $k->kategori }}</span></td>
                        <td>{{ $k->periode->nama_periode ?? '-' }}</td>
                        <td>
                            <strong>{{ $k->pertanyaan_count }}</strong> Butir
                        </td>
                        <td>
                            <form action="{{ route('admin.kuesioner.toggle', $k) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge {{ $k->status === 'Aktif' ? 'badge-green' : 'badge-gray' }}" style="border:none; cursor:pointer;" title="Klik untuk ubah status">
                                    {{ $k->status }} 🔄
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.kuesioner.show', $k) }}" class="btn btn-navy btn-sm" title="Kelola Butir Pertanyaan">
                                📝 Butir Soal
                            </a>
                            <form action="{{ route('admin.kuesioner.destroy', $k) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kuesioner ini beserta seluruh butir pertanyaannya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon text-danger" title="Hapus">🗑</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--muted); padding: 30px;">
                            Belum ada instrumen kuesioner. Silakan buat kuesioner baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">{{ $kuesioners->links() }}</div>
</div>

<!-- MODAL ADD KUESIONER -->
<div class="modal-overlay" id="modalAddKuesioner">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Buat Instrumen Kuesioner</h3>
            <button class="modal-close" onclick="closeModal('modalAddKuesioner')">&times;</button>
        </div>
        <form action="{{ route('admin.kuesioner.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul Kuesioner</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Evaluasi Kinerja Dosen Semester Ganjil 2025/2026" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" value="Evaluasi Kinerja Dosen" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Periode Akademik</label>
                    <select name="periode_id" class="form-control" required>
                        @foreach($periodes as $pr)
                            <option value="{{ $pr->id }}" {{ $pr->status === 'Aktif' ? 'selected' : '' }}>
                                {{ $pr->nama_periode }} {{ $pr->status === 'Aktif' ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi / Petunjuk Pengisian</label>
                    <textarea name="deskripsi" class="form-control" placeholder="Tuliskan petunjuk pengisian bagi mahasiswa..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddKuesioner')">Batal</button>
                <button type="submit" class="btn btn-primary">Lanjut Tambah Butir Soal →</button>
            </div>
        </form>
    </div>
</div>
@endsection
