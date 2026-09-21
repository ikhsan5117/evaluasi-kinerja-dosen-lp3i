@extends('layouts.app')

@section('title', 'Butir Pertanyaan: ' . $kuesioner->judul)

@section('content')
<div class="page-header">
    <div>
        <div style="margin-bottom: 6px;">
            <a href="{{ route('admin.kuesioner.index') }}" style="color: var(--accent); text-decoration: none; font-size: 12px; font-weight: 600;">
                ← Kembali ke Daftar Kuesioner
            </a>
        </div>
        <h1 class="page-title">{{ $kuesioner->judul }}</h1>
        <div class="page-subtitle">
            Periode: <strong>{{ $kuesioner->periode->nama_periode ?? '-' }}</strong> &bull;
            Status: <span class="badge {{ $kuesioner->status === 'Aktif' ? 'badge-green' : 'badge-gray' }}">{{ $kuesioner->status }}</span> &bull;
            Total: <strong>{{ $kuesioner->pertanyaan->count() }}</strong> Butir Soal
        </div>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('admin.kuesioner.template.download') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 8px 14px; font-weight: 500; font-size: 13px; border-radius: 6px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Template
        </a>
        <button type="button" class="btn btn-secondary" onclick="openModal('modalImportPertanyaan')" style="display: inline-flex; align-items: center; gap: 6px; background: var(--blue-bg); border: 1px solid var(--border); color: var(--navy); font-weight: 600; padding: 8px 14px; font-size: 13px; border-radius: 6px; cursor: pointer;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path></svg>
            Upload Butir Soal Excel
        </button>
        <button class="btn btn-primary" onclick="openModal('modalAddPertanyaan')">+ Tambah Pertanyaan</button>
    </div>
</div>

@if($kuesioner->deskripsi)
    <div style="background: var(--blue-bg); padding: 14px 18px; border-radius: 8px; font-size: 12px; color: var(--navy); margin-bottom: 20px;">
        <strong>Petunjuk:</strong> {{ $kuesioner->deskripsi }}
    </div>
@endif

<!-- PERTANYAAN LIST -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Daftar Butir Instrumen Penilaian (Skala Likert 1-5)</h2>
    </div>

    @if($kuesioner->pertanyaan->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--muted); font-size: 12px;">
            Belum ada butir pertanyaan pada kuesioner ini. Silakan klik tombol "+ Tambah Pertanyaan".
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Teks Butir Pertanyaan</th>
                        <th width="140">Kompetensi / Aspek</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kuesioner->pertanyaan as $idx => $p)
                        <tr>
                            <td><strong>{{ $p->nomor_urut }}</strong></td>
                            <td style="font-size: 13px;">
                                {{ $p->teks_pertanyaan }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($p->kategori) {
                                        'Pedagogik', 'Metode Pembelajaran' => 'badge-blue',
                                        'Profesional' => 'badge-purple',
                                        'Kepribadian' => 'badge-green',
                                        'Sosial' => 'badge-amber',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $p->kategori }}</span>
                            </td>
                            <td>
                                <button class="btn-icon text-primary" title="Edit" onclick="editPertanyaan({{ json_encode($p) }})">✎</button>
                                <form action="{{ route('admin.pertanyaan.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus butir pertanyaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger" title="Hapus">🗑</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- MODAL ADD PERTANYAAN -->
<div class="modal-overlay" id="modalAddPertanyaan">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Butir Pertanyaan</h3>
            <button class="modal-close" onclick="closeModal('modalAddPertanyaan')">&times;</button>
        </div>
        <form action="{{ route('admin.pertanyaan.store', $kuesioner) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nomor Urut</label>
                    <input type="number" name="nomor_urut" class="form-control" value="{{ ($kuesioner->pertanyaan->max('nomor_urut') ?? 0) + 1 }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Aspek Kompetensi</label>
                    <select name="kategori" class="form-control" required>
                        <option value="Metode Pembelajaran">Metode Pembelajaran (Penyampaian & Praktik)</option>
                        <option value="Profesional">Profesional (Penguasaan Materi & Objektivitas)</option>
                        <option value="Kepribadian">Kepribadian (Keteladanan & Kedisiplinan)</option>
                        <option value="Sosial">Sosial (Komunikasi & Interaksi Kelas)</option>
                        <option value="Pedagogik">Pedagogik</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Teks Pernyataan / Pertanyaan</label>
                    <textarea name="teks_pertanyaan" class="form-control" placeholder="Contoh: Dosen menyampaikan materi perkuliahan dengan jelas dan terstruktur." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddPertanyaan')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pertanyaan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PERTANYAAN -->
<div class="modal-overlay" id="modalEditPertanyaan">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Butir Pertanyaan</h3>
            <button class="modal-close" onclick="closeModal('modalEditPertanyaan')">&times;</button>
        </div>
        <form id="formEditPertanyaan" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nomor Urut</label>
                    <input type="number" name="nomor_urut" id="editNomorUrut" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Aspek Kompetensi</label>
                    <select name="kategori" id="editKategori" class="form-control" required>
                        <option value="Metode Pembelajaran">Metode Pembelajaran</option>
                        <option value="Profesional">Profesional</option>
                        <option value="Kepribadian">Kepribadian</option>
                        <option value="Sosial">Sosial</option>
                        <option value="Pedagogik">Pedagogik</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Teks Pernyataan / Pertanyaan</label>
                    <textarea name="teks_pertanyaan" id="editTeksPertanyaan" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditPertanyaan')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL IMPORT PERTANYAAN -->
<div class="modal-overlay" id="modalImportPertanyaan">
    <div class="modal-box" style="max-width: 540px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #002B49 0%, #00426E 100%); color: #fff;">
            <div>
                <h3 class="modal-title" style="color: #fff; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Upload Butir Pertanyaan (.xlsx)
                </h3>
                <div style="font-size: 11.5px; color: #BAE6FD; margin-top: 2px;">Impor instrumen penilaian ke kuesioner "{{ $kuesioner->judul }}"</div>
            </div>
            <button class="modal-close" style="color: #fff; font-size: 20px;" onclick="closeModal('modalImportPertanyaan')">&times;</button>
        </div>
        <form action="{{ route('admin.kuesioner.import.pertanyaan', $kuesioner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="padding: 20px;">
                
                {{-- Quick Guide --}}
                <div style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px; font-size: 12px; color: #0369A1;">
                    <div style="font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                        💡 Petunjuk Format Kolom:
                    </div>
                    <div style="line-height: 1.5; color: #0C4A6E;">
                        • Format Kolom: <code>No / Nomor Urut</code>, <code>Teks Butir Pertanyaan</code>, <code>Kategori Aspek</code>.<br>
                        • Pilihan Kategori: <code>Pedagogik</code>, <code>Profesional</code>, <code>Kepribadian</code>, atau <code>Sosial</code>.<br>
                        • Setiap butir pertanyaan akan dinilai menggunakan skala Likert 1–5 secara otomatis.
                    </div>
                </div>

                {{-- Download Template Prompt --}}
                <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg); border: 1px dashed var(--border); border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;">
                    <span style="font-size: 12px; color: var(--muted);">Gunakan template resmi untuk format yang presisi:</span>
                    <a href="{{ route('admin.kuesioner.template.download') }}" style="font-size: 12px; font-weight: 600; color: var(--navy); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        📥 Download Template
                    </a>
                </div>

                {{-- Dropzone / File Picker --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 6px;">Pilih File Excel / CSV <span style="color: red;">*</span></label>
                    <div id="dropzonePertanyaan" style="border: 2px dashed var(--border); border-radius: 10px; padding: 24px 16px; text-align: center; background: var(--bg); cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('filePertanyaanInput').click()">
                        <div style="font-size: 32px; margin-bottom: 8px;">📑</div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--ink);" id="dropzonePertanyaanText">Klik untuk memilih file atau seret ke sini</div>
                        <div style="font-size: 11px; color: var(--muted); margin-top: 4px;">Mendukung format .xlsx, .xls, .csv (Maksimal 10 MB)</div>
                        <input type="file" name="file" id="filePertanyaanInput" accept=".xlsx,.xls,.csv" required style="display: none;" onchange="handleFilePertanyaan(this)">
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="background: var(--bg); border-top: 1px solid var(--border); padding: 12px 20px; display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalImportPertanyaan')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    Mulai Impor Butir Soal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editPertanyaan(p) {
        document.getElementById('formEditPertanyaan').action = "/admin/pertanyaan/" + p.id;
        document.getElementById('editNomorUrut').value = p.nomor_urut;
        document.getElementById('editKategori').value = p.kategori;
        document.getElementById('editTeksPertanyaan').value = p.teks_pertanyaan;
        openModal('modalEditPertanyaan');
    }

    function handleFilePertanyaan(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const dropzone = document.getElementById('dropzonePertanyaan');
            const fileNameText = document.getElementById('dropzonePertanyaanText');
            fileNameText.innerHTML = `📄 <strong>${file.name}</strong> (${(file.size / 1024).toFixed(1)} KB)`;
            dropzone.style.borderColor = 'var(--navy)';
            dropzone.style.background = 'var(--blue-bg)';
        }
    }

    const dropPertanyaan = document.getElementById('dropzonePertanyaan');
    if (dropPertanyaan) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropPertanyaan.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropPertanyaan.style.borderColor = 'var(--navy)';
                dropPertanyaan.style.background = 'var(--blue-bg)';
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropPertanyaan.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (eventName === 'dragleave') {
                    dropPertanyaan.style.borderColor = 'var(--border)';
                    dropPertanyaan.style.background = 'var(--bg)';
                }
            }, false);
        });
        dropPertanyaan.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files[0]) {
                const fileInput = document.getElementById('filePertanyaanInput');
                fileInput.files = files;
                handleFilePertanyaan(fileInput);
            }
        }, false);
    }
</script>
@endsection
