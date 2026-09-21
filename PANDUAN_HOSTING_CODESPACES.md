# 🌐 Panduan Lengkap: Hosting & Menjalankan Projek Web di GitHub Codespaces (100% Gratis & Tanpa Kartu Kredit)

Panduan ini bersifat **universal (umum)** untuk siapa saja yang ingin membuat projek webnya (Laravel, PHP, NodeJS, React, dll) bisa diakses secara online di internet dari HP/laptop orang lain tanpa perlu install aplikasi apapun di laptop lokal dan tanpa kartu kredit.

---

## 💡 Apa itu GitHub Codespaces?
GitHub Codespaces adalah komputer/server virtual di awan (*cloud*) yang disediakan resmi oleh GitHub/Microsoft. 
- ✅ **100% Gratis** (Dapat jatah 60 jam penggunaan gratis setiap bulan per akun GitHub).
- ✅ **Tidak butuh kartu kredit / debit sama sekali**.
- ✅ Diberikan domain HTTPS publik resmi (`.app.github.dev`) yang aman dan cepat.

---

## 🛠️ Langkah-Langkah Menjalankan Projek:

### Langkah 1: Pastikan Kode Projek Sudah Masuk ke GitHub
1. Buat repository baru di akun GitHub kamu ([github.com/new](https://github.com/new)).
2. Upload / push semua file kodingan projek kamu ke repository tersebut.

---

### Langkah 2: Buka Server Cloud (Codespaces)
1. Buka halaman repository projek kamu di GitHub.
2. Klik tombol hijau **`<> Code`** di atas daftar file.
3. Pilih tab **`Codespaces`** → lalu klik tombol **`Create codespace on main`**.
4. Tunggu sekitar 10–30 detik hingga tampilan editor VS Code di browser kamu selesai dimuat.

---

### Langkah 3: Menjalankan Server di Terminal

Buka panel **Terminal** di bagian bawah Codespaces:

#### A. Jika Projek Kamu Menggunakan **Laravel (PHP)**:
Ketik perintah ini di terminal lalu tekan **Enter**:
```bash
# 1. Siapkan file environment & database (jika pakai SQLite)
cp .env.example .env
touch database/database.sqlite

# 2. Install paket dependensi
composer install --ignore-platform-reqs

# 3. Generate App Key & Migrasi Database
php artisan key:generate
php artisan migrate --seed

# 4. Nyalakan Server Web
php artisan serve --port=8000 --host=0.0.0.0
```

#### B. Jika Projek Kamu Menggunakan **NodeJS / Express / React / Vue**:
```bash
npm install
npm run dev -- --host 0.0.0.0
```

#### C. Jika Projek Kamu Menggunakan **HTML / PHP Native Biasa**:
```bash
php -S 0.0.0.0:8000
```

---

### Langkah 4: Buat Link Jadi "PUBLIC" (Paling Penting! 🚨)
Secara default, link web di Codespaces terkunci (*Private*). Agar teman, dosen, atau penguji bisa langsung membuka web kamu **tanpa harus login akun GitHub**:

1. Di panel tab bawah (di sebelah tab *Terminal*), klik tab **`Ports`** (atau **`Pelabuhan`**).
2. Cari baris port yang sedang berjalan (contoh: port **`8000`** atau **`3000`** / **`5173`**).
3. Pada kolom **Visibility** (*Visibilitas*):
   - **Klik kanan** pada tulisan **`Private`** → ubah menjadi **`Public`** (Publik).
4. Pada kolom **Forwarded Address** (*Alamat Diteruskan*):
   - Klik ikon **salin / copy 📋** atau klik ikon **bola dunia 🌐**.
   - Contoh link: `https://[nama-server]-8000.app.github.dev`
5. Bagikan link tersebut ke siapa saja! Sekarang web kamu sudah live dan bisa dibuka dari HP / koneksi internet mana pun! 🎉

---

## ⏱️ Manajemen Server & Kuota Hemat

### 1. Kapan Server Berhenti? (Auto-Sleep)
- Jika tab browser Codespaces ditutup atau tidak ada aktivitas selama **~30 menit**, GitHub akan otomatis mengistirahatkan server (*sleep*) untuk menghemat kuota jam gratis kamu.
- Semua data dan kodingan kamu **tetap aman 100%** (tidak hilang).

### 2. Cara Menyalakan Server Kembali Kapan Saja
Saat ingin presentasi atau demo lagi:
1. Buka **[github.com/codespaces](https://github.com/codespaces)**.
2. Klik nama Codespace kamu.
3. Di terminal, cukup jalankan kembali perintah start:
   ```bash
   php artisan serve --port=8000 --host=0.0.0.0
   ```

### 3. Cara Mematikan Server Manual (Langsung Hemat Kuota)
Setelah selesai presentasi:
1. Buka **[github.com/codespaces](https://github.com/codespaces)**.
2. Klik ikon **`...` (titik tiga)** di samping nama Codespace kamu → pilih **`Stop Codespace`**.

---

## 📊 Cara Cek Sisa Kuota Gratis
1. Buka: **[github.com/settings/billing/summary](https://github.com/settings/billing/summary)**
2. Pilih menu **Usage** di sebelah kiri untuk melihat berapa jam yang sudah terpakai dari 60 jam gratis bulanan kamu.
3. Kuota 60 jam akan otomatis di-reset penuh kembali setiap awal bulan.
