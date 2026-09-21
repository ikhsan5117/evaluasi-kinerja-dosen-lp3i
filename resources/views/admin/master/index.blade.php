@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Kelola Master Data</h1>
        <div class="page-subtitle">Manajemen data dosen, mahasiswa, periode akademik, mata kuliah, dan kelas perkuliahan.</div>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('admin.master.template.download', ['type' => $tab]) }}" class="btn btn-secondary">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Template
        </a>
        <button type="button" class="btn btn-secondary" onclick="openModal('modalImportExcel')" style="color: var(--accent); border-color: var(--accent);">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path></svg>
            Upload Excel
        </button>
        @if($tab === 'dosen')
            <button class="btn btn-primary" onclick="openModal('modalAddDosen')">+ Tambah Dosen</button>
        @elseif($tab === 'mahasiswa')
            <button class="btn btn-primary" onclick="openModal('modalAddMahasiswa')">+ Tambah Mahasiswa</button>
        @elseif($tab === 'periode')
            <button class="btn btn-primary" onclick="openModal('modalAddPeriode')">+ Tambah Periode</button>
        @elseif($tab === 'matkul')
            <button class="btn btn-primary" onclick="openModal('modalAddMatkul')">+ Tambah Mata Kuliah</button>
        @elseif($tab === 'kelas')
            <button class="btn btn-primary" onclick="openModal('modalAddKelas')">+ Tambah Kelas Matkul</button>
        @endif
    </div>
</div>

<!-- TABS NAVIGATION -->
<div class="tabs">
    <a href="{{ route('admin.master.index', ['tab' => 'dosen']) }}" class="tab-item {{ $tab === 'dosen' ? 'active' : '' }}">🧑‍🏫 Dosen</a>
    <a href="{{ route('admin.master.index', ['tab' => 'mahasiswa']) }}" class="tab-item {{ $tab === 'mahasiswa' ? 'active' : '' }}">🎓 Mahasiswa</a>
    <a href="{{ route('admin.master.index', ['tab' => 'periode']) }}" class="tab-item {{ $tab === 'periode' ? 'active' : '' }}">📅 Periode Akademik</a>
    <a href="{{ route('admin.master.index', ['tab' => 'matkul']) }}" class="tab-item {{ $tab === 'matkul' ? 'active' : '' }}">📚 Mata Kuliah</a>
    <a href="{{ route('admin.master.index', ['tab' => 'kelas']) }}" class="tab-item {{ $tab === 'kelas' ? 'active' : '' }}">🏫 Kelas Perkuliahan</a>
</div>

<!-- TAB 1: DOSEN -->
@if($tab === 'dosen')
    {{-- SEARCH BAR DOSEN --}}
    <form method="GET" action="{{ route('admin.master.index') }}" class="filter-bar">
        <input type="hidden" name="tab" value="dosen">
        <div class="filter-row">
            <div class="filter-search">
                <span class="filter-icon">🔍</span>
                <input type="text" name="q_dosen" class="filter-input" placeholder="Cari nama, email, NIDN dosen..." value="{{ request('q_dosen') }}">
            </div>
            <select name="f_dosen_prodi" class="filter-select">
                <option value="">-- Semua Prodi --</option>
                @foreach($prodis as $pr)
                    <option value="{{ $pr->id }}" {{ request('f_dosen_prodi') == $pr->id ? 'selected' : '' }}>{{ $pr->kode_prodi }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if(request('q_dosen') || request('f_dosen_prodi'))
                <a href="{{ route('admin.master.index', ['tab' => 'dosen']) }}" class="btn btn-ghost btn-sm">✕ Reset</a>
            @endif
        </div>
        @if($dosens->total() > 0)
            <div class="filter-info">Menampilkan {{ $dosens->firstItem() }}–{{ $dosens->lastItem() }} dari {{ $dosens->total() }} dosen</div>
        @endif
    </form>
    <div class="panel">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Dosen & Gelar</th>
                        <th>NIDN</th>
                        <th>Program Studi</th>
                        <th>Kontak / Email</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosens as $idx => $d)
                        <tr>
                            <td>{{ $dosens->firstItem() + $idx }}</td>
                            <td>
                                <strong>{{ $d->user->name }}</strong>{{ $d->gelar ? ', ' . $d->gelar : '' }}
                                @if($d->jabatan_fungsional)
                                    <br><span style="font-size: 10px; color: var(--muted);">{{ $d->jabatan_fungsional }}</span>
                                @endif
                            </td>
                            <td><code>{{ $d->nidn }}</code></td>
                            <td><span class="badge badge-blue">{{ $d->programStudi->nama_prodi ?? '-' }}</span></td>
                            <td>
                                {{ $d->user->email }}<br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $d->user->phone ?? '-' }}</span>
                            </td>
                            <td>
                                <button class="btn-icon text-primary" title="Edit" onclick="editDosen({{ json_encode([
                                    'id' => $d->id,
                                    'name' => $d->user->name,
                                    'email' => $d->user->email,
                                    'nidn' => $d->nidn,
                                    'gelar' => $d->gelar,
                                    'prodi_id' => $d->program_studi_id,
                                    'phone' => $d->user->phone
                                ]) }})">✎</button>
                                <form action="{{ route('admin.master.dosen.destroy', $d) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus dosen ini beserta akun penggunanya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger" title="Hapus">🗑</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; color: var(--muted);">Belum ada data dosen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pg-bar">{{ $dosens->links() }}</div>
    </div>

<!-- TAB 2: MAHASISWA -->
@elseif($tab === 'mahasiswa')
    {{-- SEARCH BAR MAHASISWA --}}
    <form method="GET" action="{{ route('admin.master.index') }}" class="filter-bar">
        <input type="hidden" name="tab" value="mahasiswa">
        <div class="filter-row">
            <div class="filter-search">
                <span class="filter-icon">🔍</span>
                <input type="text" name="q_mhs" class="filter-input" placeholder="Cari nama, NIM, kelas..." value="{{ request('q_mhs') }}">
            </div>
            <select name="f_mhs_prodi" class="filter-select">
                <option value="">-- Semua Prodi --</option>
                @foreach($prodis as $pr)
                    <option value="{{ $pr->id }}" {{ request('f_mhs_prodi') == $pr->id ? 'selected' : '' }}>{{ $pr->kode_prodi }}</option>
                @endforeach
            </select>
            <select name="f_mhs_angkatan" class="filter-select">
                <option value="">-- Semua Angkatan --</option>
                @foreach($angkatanList as $ang)
                    <option value="{{ $ang }}" {{ request('f_mhs_angkatan') == $ang ? 'selected' : '' }}>{{ $ang }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if(request('q_mhs') || request('f_mhs_prodi') || request('f_mhs_angkatan'))
                <a href="{{ route('admin.master.index', ['tab' => 'mahasiswa']) }}" class="btn btn-ghost btn-sm">✕ Reset</a>
            @endif
        </div>
        @if($mahasiswas->total() > 0)
            <div class="filter-info">Menampilkan {{ $mahasiswas->firstItem() }}–{{ $mahasiswas->lastItem() }} dari {{ $mahasiswas->total() }} mahasiswa</div>
        @endif
    </form>
    <div class="panel">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Program Studi</th>
                        <th>Angkatan & Kelas</th>
                        <th>Email / Kontak</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $idx => $m)
                        <tr>
                            <td>{{ $mahasiswas->firstItem() + $idx }}</td>
                            <td><strong>{{ $m->user->name }}</strong></td>
                            <td><code>{{ $m->nim }}</code></td>
                            <td><span class="badge badge-purple">{{ $m->programStudi->nama_prodi ?? '-' }}</span></td>
                            <td>{{ $m->angkatan }} &bull; <strong>{{ $m->kelas }}</strong></td>
                            <td>
                                {{ $m->user->email }}<br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $m->user->phone ?? '-' }}</span>
                            </td>
                            <td>
                                <button class="btn-icon text-primary" title="Edit" onclick="editMahasiswa({{ json_encode([
                                    'id' => $m->id,
                                    'name' => $m->user->name,
                                    'email' => $m->user->email,
                                    'nim' => $m->nim,
                                    'prodi_id' => $m->program_studi_id,
                                    'angkatan' => $m->angkatan,
                                    'kelas' => $m->kelas,
                                    'phone' => $m->user->phone
                                ]) }})">✎</button>
                                <form action="{{ route('admin.master.mahasiswa.destroy', $m) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus mahasiswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger" title="Hapus">🗑</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--muted);">Belum ada data mahasiswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pg-bar">{{ $mahasiswas->links() }}</div>
    </div>

<!-- TAB 3: PERIODE -->
@elseif($tab === 'periode')
    <div class="panel">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodes as $idx => $p)
                        <tr>
                            <td>{{ $periodes->firstItem() + $idx }}</td>
                            <td><strong>{{ $p->tahun_ajaran }}</strong></td>
                            <td>{{ $p->semester }}</td>
                            <td>
                                <span class="badge {{ $p->status === 'Aktif' ? 'badge-green' : 'badge-gray' }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn-icon text-primary" title="Edit" onclick="editPeriode({{ json_encode($p) }})">✎</button>
                                <form action="{{ route('admin.master.periode.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus periode ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger" title="Hapus">🗑</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; color: var(--muted);">Belum ada data periode.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pg-bar">{{ $periodes->links() }}</div>
    </div>

<!-- TAB 4: MATA KULIAH -->
@elseif($tab === 'matkul')
    {{-- SEARCH BAR MATKUL --}}
    <form method="GET" action="{{ route('admin.master.index') }}" class="filter-bar">
        <input type="hidden" name="tab" value="matkul">
        <div class="filter-row">
            <div class="filter-search">
                <span class="filter-icon">🔍</span>
                <input type="text" name="q_matkul" class="filter-input" placeholder="Cari nama atau kode mata kuliah..." value="{{ request('q_matkul') }}">
            </div>
            <select name="f_matkul_prodi" class="filter-select">
                <option value="">-- Semua Prodi --</option>
                @foreach($prodis as $pr)
                    <option value="{{ $pr->id }}" {{ request('f_matkul_prodi') == $pr->id ? 'selected' : '' }}>{{ $pr->kode_prodi }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if(request('q_matkul') || request('f_matkul_prodi'))
                <a href="{{ route('admin.master.index', ['tab' => 'matkul']) }}" class="btn btn-ghost btn-sm">✕ Reset</a>
            @endif
        </div>
        @if($matkuls->total() > 0)
            <div class="filter-info">Menampilkan {{ $matkuls->firstItem() }}–{{ $matkuls->lastItem() }} dari {{ $matkuls->total() }} mata kuliah</div>
        @endif
    </form>
    <div class="panel">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Bobot SKS</th>
                        <th>Program Studi</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matkuls as $idx => $mk)
                        <tr>
                            <td>{{ $matkuls->firstItem() + $idx }}</td>
                            <td><code>{{ $mk->kode_matkul }}</code></td>
                            <td><strong>{{ $mk->nama_matkul }}</strong></td>
                            <td>{{ $mk->sks }} SKS</td>
                            <td><span class="badge badge-blue">{{ $mk->programStudi->nama_prodi ?? '-' }}</span></td>
                            <td>
                                <button class="btn-icon text-primary" title="Edit" onclick="editMatkul({{ json_encode($mk) }})">✎</button>
                                <form action="{{ route('admin.master.matkul.destroy', $mk) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger" title="Hapus">🗑</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; color: var(--muted);">Belum ada data mata kuliah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pg-bar">{{ $matkuls->links() }}</div>
    </div>

<!-- TAB 5: KELAS PERKULIAHAN -->
@elseif($tab === 'kelas')
    {{-- SEARCH BAR KELAS --}}
    <form method="GET" action="{{ route('admin.master.index') }}" class="filter-bar">
        <input type="hidden" name="tab" value="kelas">
        <div class="filter-row">
            <div class="filter-search">
                <span class="filter-icon">🔍</span>
                <input type="text" name="q_kelas" class="filter-input" placeholder="Cari nama kelas, mata kuliah, dosen..." value="{{ request('q_kelas') }}">
            </div>
            <select name="f_kelas_periode" class="filter-select">
                <option value="">-- Semua Periode --</option>
                @foreach($periodeAll as $pr)
                    <option value="{{ $pr->id }}" {{ request('f_kelas_periode') == $pr->id ? 'selected' : '' }}>
                        {{ $pr->tahun_ajaran }} {{ $pr->semester }} {{ $pr->status === 'Aktif' ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if(request('q_kelas') || request('f_kelas_periode'))
                <a href="{{ route('admin.master.index', ['tab' => 'kelas']) }}" class="btn btn-ghost btn-sm">✕ Reset</a>
            @endif
        </div>
        @if($kelasList->total() > 0)
            <div class="filter-info">Menampilkan {{ $kelasList->firstItem() }}–{{ $kelasList->lastItem() }} dari {{ $kelasList->total() }} kelas</div>
        @endif
    </form>
    <div class="panel">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Kelas</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen Pengampu</th>
                        <th>Periode</th>
                        <th>Ruangan & Jadwal</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelasList as $idx => $k)
                        <tr>
                            <td>{{ $kelasList->firstItem() + $idx }}</td>
                            <td><strong>{{ $k->nama_kelas }}</strong></td>
                            <td>{{ $k->mataKuliah->nama_matkul ?? '-' }} ({{ $k->mataKuliah->sks ?? 0 }} SKS)</td>
                            <td>{{ $k->dosen->nama_lengkap ?? '-' }}</td>
                            <td><span class="badge badge-purple">{{ $k->periode->nama_periode ?? '-' }}</span></td>
                            <td>
                                {{ $k->ruangan ?? 'Ruang TBA' }}<br>
                                <span style="font-size: 10px; color: var(--muted);">{{ $k->jadwal ?? '-' }}</span>
                            </td>
                            <td>
                                <button class="btn-icon text-primary" title="Edit" onclick="editKelas({{ json_encode($k) }})">✎</button>
                                <form action="{{ route('admin.master.kelas.destroy', $k) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kelas perkuliahan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger" title="Hapus">🗑</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--muted);">Belum ada data kelas perkuliahan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pg-bar">{{ $kelasList->links() }}</div>
    </div>
@endif

<!-- MODAL ADD DOSEN -->
<div class="modal-overlay" id="modalAddDosen">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Dosen Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddDosen')">&times;</button>
        </div>
        <form action="{{ route('admin.master.dosen.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Dosen (Tanpa Gelar)</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Dr. Ahmad Fauzi" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Gelar Akademik</label>
                    <input type="text" name="gelar" class="form-control" placeholder="Contoh: M.Kom, M.T">
                </div>
                <div class="form-group">
                    <label class="form-label">NIDN</label>
                    <input type="text" name="nidn" class="form-control" placeholder="10 Digit NIDN" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" class="form-control" required>
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach($prodis as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama_prodi }} ({{ $pr->jenjang }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Akun</label>
                    <input type="email" name="email" class="form-control" placeholder="dosen@lp3i.ac.id" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Akun (Default: password)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan untuk password default">
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddDosen')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Dosen</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT DOSEN -->
<div class="modal-overlay" id="modalEditDosen">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Data Dosen</h3>
            <button class="modal-close" onclick="closeModal('modalEditDosen')">&times;</button>
        </div>
        <form id="formEditDosen" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Dosen</label>
                    <input type="text" name="name" id="editDosenName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Gelar Akademik</label>
                    <input type="text" name="gelar" id="editDosenGelar" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">NIDN</label>
                    <input type="text" name="nidn" id="editDosenNidn" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" id="editDosenProdi" class="form-control" required>
                        @foreach($prodis as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Akun</label>
                    <input type="email" name="email" id="editDosenEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" id="editDosenPhone" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditDosen')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD MAHASISWA -->
<div class="modal-overlay" id="modalAddMahasiswa">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Mahasiswa Baru</h3>
            <button class="modal-close" onclick="closeModal('modalAddMahasiswa')">&times;</button>
        </div>
        <form action="{{ route('admin.master.mahasiswa.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama mahasiswa" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-control" placeholder="Nomor Induk Mahasiswa" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" class="form-control" required>
                        <option value="">-- Pilih Prodi --</option>
                        @foreach($prodis as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Angkatan (Tahun Masuk)</label>
                    <input type="text" name="angkatan" class="form-control" placeholder="Contoh: 2024" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" class="form-control" placeholder="Contoh: MI-2A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Akun</label>
                    <input type="email" name="email" class="form-control" placeholder="mhs@lp3i.ac.id" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password (Default: password)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan untuk default">
                </div>
                <div class="form-group">
                    <label class="form-label">No. HP / WA</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddMahasiswa')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Mahasiswa</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT MAHASISWA -->
<div class="modal-overlay" id="modalEditMahasiswa">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Data Mahasiswa</h3>
            <button class="modal-close" onclick="closeModal('modalEditMahasiswa')">&times;</button>
        </div>
        <form id="formEditMahasiswa" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="editMhsName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" id="editMhsNim" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" id="editMhsProdi" class="form-control" required>
                        @foreach($prodis as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Angkatan</label>
                    <input type="text" name="angkatan" id="editMhsAngkatan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" id="editMhsKelas" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Akun</label>
                    <input type="email" name="email" id="editMhsEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                </div>
                <div class="form-group">
                    <label class="form-label">No. HP</label>
                    <input type="text" name="phone" id="editMhsPhone" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditMahasiswa')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD PERIODE -->
<div class="modal-overlay" id="modalAddPeriode">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Periode Akademik</h3>
            <button class="modal-close" onclick="closeModal('modalAddPeriode')">&times;</button>
        </div>
        <form action="{{ route('admin.master.periode.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2025/2026" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-control" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
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
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddPeriode')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Periode</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD MATKUL -->
<div class="modal-overlay" id="modalAddMatkul">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Mata Kuliah</h3>
            <button class="modal-close" onclick="closeModal('modalAddMatkul')">&times;</button>
        </div>
        <form action="{{ route('admin.master.matkul.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Mata Kuliah</label>
                    <input type="text" name="kode_matkul" class="form-control" placeholder="Contoh: MK01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Mata Kuliah</label>
                    <input type="text" name="nama_matkul" class="form-control" placeholder="Contoh: Pemrograman Web" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Bobot SKS</label>
                    <input type="number" name="sks" class="form-control" min="1" max="6" value="3" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" class="form-control" required>
                        <option value="">-- Pilih Prodi --</option>
                        @foreach($prodis as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddMatkul')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Mata Kuliah</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD KELAS -->
<div class="modal-overlay" id="modalAddKelas">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Kelas Perkuliahan</h3>
            <button class="modal-close" onclick="closeModal('modalAddKelas')">&times;</button>
        </div>
        <form action="{{ route('admin.master.kelas.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: MI-2A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Kuliah</label>
                    <select name="mata_kuliah_id" class="form-control" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($matkulAll as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->nama_matkul }} ({{ $mk->kode_matkul }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Dosen Pengampu</label>
                    <select name="dosen_id" class="form-control" required>
                        <option value="">-- Pilih Dosen --</option>
                        @foreach($dosenAll as $ds)
                            <option value="{{ $ds->id }}">{{ $ds->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Periode Akademik</label>
                    <select name="periode_id" class="form-control" required>
                        @foreach($periodeAll as $pr)
                            <option value="{{ $pr->id }}" {{ $pr->status === 'Aktif' ? 'selected' : '' }}>
                                {{ $pr->tahun_ajaran }} {{ $pr->semester }} {{ $pr->status === 'Aktif' ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" class="form-control" placeholder="Contoh: Lab Komputer 1">
                </div>
                <div class="form-group">
                    <label class="form-label">Jadwal Perkuliahan</label>
                    <input type="text" name="jadwal" class="form-control" placeholder="Contoh: Senin, 08:00 - 10:30">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddKelas')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PERIODE -->
<div class="modal-overlay" id="modalEditPeriode">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Periode Akademik</h3>
            <button class="modal-close" onclick="closeModal('modalEditPeriode')">&times;</button>
        </div>
        <form id="formEditPeriode" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="editPeriodeTahun" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Semester</label>
                    <select name="semester" id="editPeriodeSemester" class="form-control" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editPeriodeStatus" class="form-control" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditPeriode')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT MATKUL -->
<div class="modal-overlay" id="modalEditMatkul">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Mata Kuliah</h3>
            <button class="modal-close" onclick="closeModal('modalEditMatkul')">&times;</button>
        </div>
        <form id="formEditMatkul" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Mata Kuliah</label>
                    <input type="text" name="kode_matkul" id="editMatkulKode" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Mata Kuliah</label>
                    <input type="text" name="nama_matkul" id="editMatkulNama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Bobot SKS</label>
                    <input type="number" name="sks" id="editMatkulSks" class="form-control" min="1" max="6" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi_id" id="editMatkulProdi" class="form-control" required>
                        @foreach($prodis as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditMatkul')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KELAS -->
<div class="modal-overlay" id="modalEditKelas">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Kelas Perkuliahan</h3>
            <button class="modal-close" onclick="closeModal('modalEditKelas')">&times;</button>
        </div>
        <form id="formEditKelas" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kelas</label>
                    <input type="text" name="nama_kelas" id="editKelasNama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Kuliah</label>
                    <select name="mata_kuliah_id" id="editKelasMatkul" class="form-control" required>
                        @foreach($matkulAll as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->nama_matkul }} ({{ $mk->kode_matkul }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Dosen Pengampu</label>
                    <select name="dosen_id" id="editKelasDosen" class="form-control" required>
                        @foreach($dosenAll as $ds)
                            <option value="{{ $ds->id }}">{{ $ds->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Periode Akademik</label>
                    <select name="periode_id" id="editKelasPeriode" class="form-control" required>
                        @foreach($periodeAll as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->tahun_ajaran }} {{ $pr->semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" id="editKelasRuangan" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Jadwal Perkuliahan</label>
                    <input type="text" name="jadwal" id="editKelasJadwal" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditKelas')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal-overlay" id="modalImportExcel">
    <div class="modal-box" style="max-width: 540px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #002B49 0%, #00426E 100%); color: #fff;">
            <div>
                <h3 class="modal-title" style="color: #fff; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Upload Data Excel: {{ ucfirst($tab) }}
                </h3>
                <div style="font-size: 11.5px; color: #BAE6FD; margin-top: 2px;">Impor data secara massal menggunakan file spreadsheet (.xlsx, .xls, .csv)</div>
            </div>
            <button class="modal-close" style="color: #fff; font-size: 20px;" onclick="closeModal('modalImportExcel')">&times;</button>
        </div>
        <form action="{{ route('admin.master.import.' . $tab) }}" method="POST" enctype="multipart/form-data" id="formImportExcel">
            @csrf
            <div class="modal-body" style="padding: 20px;">
                
                {{-- Quick Guide per Tab --}}
                <div style="background: var(--blue-bg); border: 1px solid var(--border); border-radius: 8px; padding: 12px 14px; margin-bottom: 16px; font-size: 12px; color: var(--navy);">
                    <div style="font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                        💡 Petunjuk Format Kolom:
                    </div>
                    @if($tab === 'mahasiswa')
                        <div style="line-height: 1.5; color: var(--ink);">
                            • Format kolom: <code>No</code>, <code>NIPD / NIM</code>, <code>Peserta Didik / Nama Mahasiswa</code>, <code>Kelas</code> (misal: <i>ASE 24-001</i>), <code>Kode Prodi</code> (opsional), <code>Angkatan</code> (opsional).<br>
                            • Mendukung file dengan banyak sheet (misal sheet <code>2023</code>, <code>2024</code>, <code>2025</code>) maupun template standar 1 sheet.
                        </div>
                    @elseif($tab === 'dosen')
                        <div style="line-height: 1.5; color: var(--ink);">
                            • Format kolom: <code>No</code>, <code>NIDN</code>, <code>Nama Lengkap</code>, <code>Gelar</code>, <code>Kode Prodi</code> (misal: <i>ASE/OAA/AIS</i>), <code>Email</code>, <code>No Telepon</code>.
                        </div>
                    @elseif($tab === 'matkul')
                        <div style="line-height: 1.5; color: var(--ink);">
                            • Format kolom: <code>No</code>, <code>Kode Matkul</code>, <code>Nama Mata Kuliah</code>, <code>SKS</code>, <code>Kode Prodi</code>.
                        </div>
                    @elseif($tab === 'kelas')
                        <div style="line-height: 1.5; color: var(--ink);">
                            • Format kolom: <code>No</code>, <code>Nama Kelas</code>, <code>Kode Matkul</code>, <code>NIDN Dosen</code>, <code>Tahun Ajaran</code>, <code>Semester</code>, <code>Ruangan</code>, <code>Jadwal</code>.
                        </div>
                    @elseif($tab === 'periode')
                        <div style="line-height: 1.5; color: var(--ink);">
                            • Format kolom: <code>No</code>, <code>Tahun Ajaran</code> (misal: <i>2024/2025</i>), <code>Semester</code> (<i>Ganjil/Genap</i>), <code>Status</code> (<i>Aktif/Nonaktif</i>).
                        </div>
                    @endif
                </div>

                {{-- Download Template Prompt --}}
                <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg); border: 1px dashed var(--border); border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;">
                    <span style="font-size: 12px; color: var(--muted);">Belum memiliki format file?</span>
                    <a href="{{ route('admin.master.template.download', ['type' => $tab]) }}" style="font-size: 12px; font-weight: 600; color: var(--accent); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        📥 Download Template Resmi
                    </a>
                </div>

                {{-- Dropzone / File Picker --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 6px;">Pilih File Excel / CSV <span style="color: var(--red);">*</span></label>
                    <div id="dropzoneBox" style="border: 2px dashed var(--border); border-radius: 10px; padding: 24px 16px; text-align: center; background: var(--bg); cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('excelFileInput').click()">
                        <div style="font-size: 32px; margin-bottom: 8px;">📊</div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--ink);" id="dropzoneFileName">Klik untuk memilih file atau seret file ke sini</div>
                        <div style="font-size: 11px; color: var(--muted); margin-top: 4px;">Mendukung format .xlsx, .xls, .csv (Maksimal 10 MB)</div>
                        <input type="file" name="file" id="excelFileInput" accept=".xlsx,.xls,.csv" required style="display: none;" onchange="handleFileSelect(this)">
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="background: var(--bg); border-top: 1px solid var(--border); padding: 12px 20px; display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalImportExcel')">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitImport">
                    <span id="btnSubmitText">Mulai Impor Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Filter / Search Bar */
    .filter-bar {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 14px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        transition: background-color 0.2s, border-color 0.2s;
    }
    .filter-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .filter-search {
        flex: 1;
        min-width: 200px;
        position: relative;
        display: flex;
        align-items: center;
    }
    .filter-icon {
        position: absolute;
        left: 10px;
        font-size: 13px;
        pointer-events: none;
    }
    .filter-input {
        width: 100%;
        padding: 7px 12px 7px 32px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 12.5px;
        outline: none;
        font-family: inherit;
        background: var(--input-bg);
        color: var(--ink);
        transition: all 0.2s;
    }
    .filter-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
    .filter-select {
        padding: 7px 10px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 12px;
        background: var(--input-bg);
        color: var(--ink);
        font-family: inherit;
        outline: none;
        cursor: pointer;
    }
    .filter-select:focus { border-color: var(--accent); }
    .filter-info {
        margin-top: 8px;
        font-size: 11px;
        color: var(--muted);
    }

    /* Pagination Override - fix giant arrows */
    .pg-bar {
        margin-top: 14px;
        display: flex;
        justify-content: flex-end;
    }
    .pg-bar nav {
        display: flex;
        align-items: center;
    }
    .pg-bar nav > div {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .pg-bar span, .pg-bar a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-decoration: none;
        background: var(--card-bg);
        transition: all 0.15s;
        font-family: inherit;
    }
    .pg-bar a:hover {
        background: var(--blue-bg);
        color: var(--accent);
        border-color: var(--accent);
    }
    .pg-bar span[aria-current="page"] {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }
    .pg-bar span.cursor-default {
        background: #F9FAFB;
        color: #C0C8D4;
    }
    /* Hide the "Showing X to Y of Z results" default text */
    .pg-bar p { display: none; }
    /* Fix SVG arrow icons size */
    .pg-bar svg {
        width: 14px;
        height: 14px;
    }
</style>

<script>
    function editDosen(data) {
        document.getElementById('formEditDosen').action = "/admin/master/dosen/" + data.id;
        document.getElementById('editDosenName').value = data.name;
        document.getElementById('editDosenEmail').value = data.email;
        document.getElementById('editDosenNidn').value = data.nidn;
        document.getElementById('editDosenGelar').value = data.gelar || '';
        document.getElementById('editDosenProdi').value = data.prodi_id;
        document.getElementById('editDosenPhone').value = data.phone || '';
        openModal('modalEditDosen');
    }

    function editMahasiswa(data) {
        document.getElementById('formEditMahasiswa').action = "/admin/master/mahasiswa/" + data.id;
        document.getElementById('editMhsName').value = data.name;
        document.getElementById('editMhsEmail').value = data.email;
        document.getElementById('editMhsNim').value = data.nim;
        document.getElementById('editMhsProdi').value = data.prodi_id;
        document.getElementById('editMhsAngkatan').value = data.angkatan;
        document.getElementById('editMhsKelas').value = data.kelas;
        document.getElementById('editMhsPhone').value = data.phone || '';
        openModal('modalEditMahasiswa');
    }

    function editPeriode(data) {
        document.getElementById('formEditPeriode').action = "/admin/master/periode/" + data.id;
        document.getElementById('editPeriodeTahun').value = data.tahun_ajaran;
        document.getElementById('editPeriodeSemester').value = data.semester;
        document.getElementById('editPeriodeStatus').value = data.status;
        openModal('modalEditPeriode');
    }

    function editMatkul(data) {
        document.getElementById('formEditMatkul').action = "/admin/master/matkul/" + data.id;
        document.getElementById('editMatkulKode').value = data.kode_matkul;
        document.getElementById('editMatkulNama').value = data.nama_matkul;
        document.getElementById('editMatkulSks').value = data.sks;
        document.getElementById('editMatkulProdi').value = data.program_studi_id;
        openModal('modalEditMatkul');
    }

    function editKelas(data) {
        document.getElementById('formEditKelas').action = "/admin/master/kelas/" + data.id;
        document.getElementById('editKelasNama').value = data.nama_kelas;
        document.getElementById('editKelasMatkul').value = data.mata_kuliah_id;
        document.getElementById('editKelasDosen').value = data.dosen_id;
        document.getElementById('editKelasPeriode').value = data.periode_id;
        document.getElementById('editKelasRuangan').value = data.ruangan || '';
        document.getElementById('editKelasJadwal').value = data.jadwal || '';
        openModal('modalEditKelas');
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const dropzone = document.getElementById('dropzoneBox');
            const fileNameText = document.getElementById('dropzoneFileName');
            fileNameText.innerHTML = `📄 <strong>${file.name}</strong> (${(file.size / 1024).toFixed(1)} KB)`;
            dropzone.style.borderColor = '#0284C7';
            dropzone.style.background = '#F0F9FF';
        }
    }

    // Drag and drop event listeners
    const dropBox = document.getElementById('dropzoneBox');
    if (dropBox) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropBox.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropBox.style.borderColor = '#0284C7';
                dropBox.style.background = '#F0F9FF';
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropBox.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (eventName === 'dragleave') {
                    dropBox.style.borderColor = '#94A3B8';
                    dropBox.style.background = '#F8FAFC';
                }
            }, false);
        });
        dropBox.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files[0]) {
                const fileInput = document.getElementById('excelFileInput');
                fileInput.files = files;
                handleFileSelect(fileInput);
            }
        }, false);
    }
</script>
@endsection
