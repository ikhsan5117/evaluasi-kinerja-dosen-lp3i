# 🚀 Panduan Menjalankan & Mengakses Aplikasi Evaluasi Dosen LP3I di GitHub Codespaces

Panduan ini dibuat agar siapa saja (teman, dosen, mahasiswa) dapat menjalankan atau mengakses aplikasi web **Evaluasi Kinerja Dosen LP3I** secara online tanpa perlu install PHP / Composer / database di laptop lokal.

---

## 📌 1. Cara Menjalankan Server di Cloud (Untuk Pemilik / Pengelola Repo)

Jika kamu ingin menyalakan server di cloud agar aplikasi bisa diakses online:

1. Buka repository GitHub: 👉 **[https://github.com/ikhsan5117/evaluasi-kinerja-dosen-lp3i](https://github.com/ikhsan5117/evaluasi-kinerja-dosen-lp3i)**
2. Klik tombol hijau **`<> Code`** di atas daftar file.
3. Pilih tab **`Codespaces`** → klik **`Create codespace on main`** (atau klik nama Codespace yang sudah ada jika pernah dibuat).
4. Tunggu beberapa detik hingga tampilan editor VS Code di browser terbuka.
5. Di bagian bawah (panel **Terminal**), jalankan perintah satu baris berikut lalu tekan **Enter**:

```bash
cp .env.example .env && cp database/starter.sqlite database/database.sqlite && composer install --ignore-platform-reqs && php artisan key:generate && php artisan optimize:clear && php artisan serve --port=8000 --host=0.0.0.0
```

6. **Buat Akses Publik (Penting agar bisa dibuka teman tanpa login GitHub)**:
   - Klik tab **`Pelabuhan`** / **`Ports`** (di samping tab *Terminal*).
   - Pada baris **Port 8000**, cari kolom **Visibilitas** (*Visibility*).
   - Klik kanan pada tulisan **`Private`** → ubah menjadi **`Public`** (Publik).
7. Salin link di kolom **Alamat yang Diteruskan** *(Forwarded Address)* contoh: `https://[nama-server]-8000.app.github.dev`.
8. Bagikan link tersebut ke teman atau dosen kamu!

---

## ⏱️ Apakah Server Harus Dinyalakan Terus?

- **Apakah harus buka laptop terus?** 
  Server ini berjalan di komputer cloud milik Microsoft/GitHub, bukan di RAM laptop kamu.
- **Sistem Auto-Sleep:** 
  GitHub Codespaces memiliki fitur hemat kuota. Jika tab Codespaces kamu ditutup / tidak ada aktivitas selama ~30 menit, server akan otomatis *tidur* (*sleep*).
- **Cara menyalakannya lagi kapan saja:** 
  Cukup buka **[github.com/codespaces](https://github.com/codespaces)**, klik nama Codespace kamu, lalu di terminal jalankan lagi perintah:
  ```bash
  php artisan serve --port=8000 --host=0.0.0.0
  ```
- **Kuota Gratis:** 
  Setiap akun GitHub gratis mendapatkan **60 jam penggunaan Codespaces gratis setiap bulannya**.

---

## 🔑 2. Akun Uji Coba untuk Login

Gunakan kredensial berikut untuk menguji 3 peran (Role) yang ada:

### 👑 1. Administrator
- **URL**: `[Link-Web]/login`
- **Email**: `admin@lp3i.ac.id`
- **Password**: `password123` *(atau `admin123#`)*
- **Fitur**: Kelola Master Data (Dosen, Mahasiswa, Matkul, Kelas, Periode), Kelola Kuesioner & Pertanyaan, Export Laporan Excel & Cetak Rekap PDF.

---

### 👨‍🏫 2. Dosen
- **Email**: `halim.fathi@lp3i.ac.id` *(atau pilih email dosen lain yang terdaftar)*
- **Password**: `password123`
- **Fitur**: Dashboard grafik skor 4 kompetensi (Pedagogik, Profesional, Kepribadian, Sosial), rekap nilai per kelas, dan membaca masukan anonim dari mahasiswa.

---

### 🎓 3. Mahasiswa
- **Email**: `2403001@lp3i.ac.id` *(atau gunakan NIPD mahasiswa lain)*
- **Password**: `password123`
- **Fitur**: Mengisi kuesioner evaluasi dosen per mata kuliah yang diampu di semester aktif, riwayat pengisian evaluasi.

---

## 🎨 Fitur Utama Aplikasi
1. **Mode Terang & Gelap (Light / Dark Mode)** dengan transisi mulus dan penyimpanan preferensi otomatis.
2. **Tabel Responsif** dengan scroll horizontal halus untuk tampilan mobile & tablet tanpa teks berhimpitan.
3. **Rekapitulasi Otomatis** nilai rata-rata dan predikat kinerja dosen (Sangat Baik, Baik, Cukup, Kurang).
4. **Export Data & Cetak Laporan** format Excel & PDF resmi.
