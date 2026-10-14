# 🗳️ E-Voting OSIS SMPN 2 Balung

Sistem E-Voting Modern, Cepat, Real-Time, dan Terverifikasi untuk Pemilihan Ketua OSIS **SMPN 2 Balung** (Tahun Pelajaran 2026/2027).  
Mendukung partisipasi penuh **822 Pemilih Tetap (DPT)** yang terdiri atas **770 Siswa** (24 rombel kelas 7A–9H) dan **52 Dewan Guru / Staf Tata Usaha**.

---

## 🌟 Fitur Utama

### 1. 🗳️ Bilik Suara Digital (Voter Booth)
- **Pencarian Pemilih Cepat**: Pemilih cukup mengetikkan nama di kolom pencarian.
- **Dukungan Siswa & Guru**: Guru dapat langsung memilih tanpa hambatan validasi NISN.
- **Anti-Double Voting**: Sistem secara otomatis mengunci hak suara setelah pemilih mengirimkan suara (1 orang = 1 suara).
- **Integritas Paslon**: Kandidat yang terdaftar sebagai paslon otomatis dinonaktifkan hak suaranya demi asas netralitas (*conflict of interest prevention*).
- **Kontrol Bilik Suara**: Bilik suara dapat dibuka atau ditutup sementara oleh panitia secara instan dari panel admin.

### 2. 📊 Rekapitulasi & Quick Count Real-Time
- **Pembaruan Otomatis**: Grafik perolehan suara dan log pemilih diperbarui secara otomatis setiap 3 detik tanpa perlu refresh manual.
- **Statistik Lengkap**:
  - Total DPT terdaftar (822).
  - Suara masuk & persentase partisipasi.
  - Rincian suara per kelas (7A–9H dan Dewan Guru).
  - Rincian per tingkat (Kelas 7, 8, 9, dan Guru).
- **Unduh Laporan Berita Acara (.xlsx)**: Mengunduh rekap suara resmi multi-sheet untuk arsip panitia.

### 3. 👥 Manajemen DPT (Siswa & Guru) & Pemutakhiran
- **Tab Kategori Terpisah**:
  - `Semua DPT (822)`
  - `Data Siswa (770)`
  - `Dewan Guru (52)`
  - `Status Hak Suara` (Menyaring pemilih yang belum hadir / sudah memilih).
- **Ekspor Excel Multi-Sheet**:
  - Menyediakan sheet khusus **Format Siap Edit & Re-Impor** untuk mempermudah panitia saat pergantian tahun ajaran (menghapus siswa kelas 9 yang lulus, menaikkan tingkat kelas 7 & 8, memasukkan siswa baru kelas 7, serta memperbarui guru pensiun/baru).
- **Impor Excel / CSV Cepat**: Impor massal DPT dengan deteksi kolom otomatis dan validasi duplikasi.

### 4. 🔄 Fitur Reset Suara Aman (Clean Slate)
- Tombol reset khusus di panel admin untuk mengosongkan perolehan suara kembali ke 0 (0%) setelah masa uji coba (*dry-run*).
- **TIDAK MENGHAPUS** data 822 pemilih maupun data kandidat/paslon.
- Dilengkapi konfirmasi keamanan ganda (wajib ketik kata `RESET`).

---

## 🏗️ Arsitektur & Teknologi

Proyek ini menggunakan arsitektur hybrid modern yang siap digunakan baik secara terintegrasi (Fullstack PHP) maupun terpisah (Frontend Vercel + Backend InfinityFree):

- **Frontend (Web App Modern)**:
  - [Next.js 14](https://nextjs.org/) (App Router)
  - [React 18](https://react.dev/) + [TypeScript](https://www.typescriptlang.org/)
  - [Tailwind CSS](https://tailwindcss.com/)
  - [Lucide Icons](https://lucide.dev/)
- **Backend (API & Database)**:
  - PHP 8.1+ Native MVC (ringan, cepat, tanpa overhead framework berat)
  - MySQL / MariaDB (PDO Prepared Statements, Foreign Keys, Strict Checking)
  - Spreadsheet Engine native (baca & tulis file `.xlsx` dan `.csv` tanpa library eksternal)

---

## 📁 Struktur Direktori

```text
evoting-smpn2balung/
├── app/                  # Controller, Model, Middleware (Backend PHP MVC)
│   ├── Controllers/      # Vote, Result, Home, Admin Controllers
│   ├── Core/             # Router, Database, Controller, Model Engine
│   ├── Middleware/       # CORS, Auth, CSRF
│   ├── Models/           # Student, Candidate, Period, Vote
│   └── Views/            # Native PHP Views
├── config/               # Konfigurasi database, app, dan routes
├── database/             # Schema & dump database siap impor
│   ├── osis_voting.sql   # SQL dump bersih (822 DPT, 2 paslon, 0 suara)
│   └── schema.sql        # Skema tabel & view database
├── frontend/             # Frontend Next.js 14 Web App
│   ├── app/              # Next.js App Router (vote, hasil, admin, dll.)
│   ├── components/       # Komponen UI
│   ├── lib/              # API Client & helper utilities
│   └── public/           # Aset statis & logo sekolah
├── helpers/              # Helper global, env parser, spreadsheet handler
├── public/               # Document root PHP (index.php, uploads, asset)
├── storage/              # Folder penyimpanan log & cache
└── README.md             # Dokumentasi proyek
```

---

## 🚀 Panduan Menjalankan di Komputer Lokal (Localhost / XAMPP)

### Prasyarat:
1. XAMPP (Apache + MySQL dengan PHP 8.1 atau lebih baru).
2. Node.js 18+ & npm.

### Langkah 1: Setup Database
1. Buka XAMPP Control Panel dan jalankan **Apache** serta **MySQL**.
2. Buka phpMyAdmin di browser (`http://localhost/phpmyadmin`).
3. Buat database baru bernama `osis_voting`.
4. Import file: `database/osis_voting.sql`.

### Langkah 2: Setup Backend PHP
1. Letakkan folder ini di `C:\xampp\htdocs\osis`.
2. Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=osis_voting
   DB_USER=root
   DB_PASS=
   ```
3. Uji akses backend: `http://localhost/osis/api/periods/active`.

### Langkah 3: Setup Frontend Next.js
1. Buka terminal di folder `frontend`:
   ```bash
   cd frontend
   npm install
   ```
2. Buat file `.env.local` di folder `frontend/`:
   ```env
   NEXT_PUBLIC_API_URL=http://localhost/osis/api
   NEXT_PUBLIC_BASE_URL=http://localhost/osis
   ```
3. Jalankan server development:
   ```bash
   npm run dev
   ```
4. Buka browser:
   - **Bilik Suara Publik**: [http://localhost:3000/vote](http://localhost:3000/vote)
   - **Hasil & Rekap Real-Time**: [http://localhost:3000/hasil](http://localhost:3000/hasil)
   - **Panel Admin**: [http://localhost:3000/admin/login](http://localhost:3000/admin/login)

---

## 🌐 Panduan Deployment ke InfinityFree (Backend) & Vercel (Frontend)

### Bagian A: Hosting Backend & Database di InfinityFree

1. **Buat Akun & Domain di InfinityFree**:
   - Daftarkan akun gratis di [InfinityFree](https://www.infinityfree.com/).
   - Buat hosting account baru (misal domain: `smpn2balung-evote.infinityfreeapp.com`).
2. **Setup Database MySQL**:
   - Buka menu **MySQL Databases** di Control Panel InfinityFree.
   - Buat database baru (misal: `epiz_xxxxxx_osis`).
   - Catat: `MySQL Host`, `MySQL Database Name`, `MySQL Username`, dan `MySQL Password`.
   - Buka **phpMyAdmin** di InfinityFree, pilih database tersebut, lalu **Import** file: `database/osis_voting.sql`.
3. **Upload File Backend**:
   - Buka File Manager atau gunakan FTP (FileZilla).
   - Masuk ke folder `htdocs/`.
   - Upload seluruh file dan folder root proyek (kecuali folder `frontend/` dan `node_modules/`):
     - `app/`, `config/`, `helpers/`, `public/`, `storage/`, `.htaccess`, `.env`.
   - Konfigurasi file `.env` di hosting:
     ```env
     APP_ENV=production
     DB_HOST=sqlxxx.infinityfree.com
     DB_PORT=3306
     DB_NAME=epiz_xxxxxx_osis
     DB_USER=epiz_xxxxxx
     DB_PASS=password_anda
     ```

### Bagian B: Hosting Frontend di Vercel

1. **Hubungkan Repositori GitHub**:
   - Masuk ke dashboard [Vercel](https://vercel.com/) dan klik **Add New Project**.
   - Pilih repositori GitHub: `iannnub/evoting-smpn2balung`.
2. **Pengaturan Proyek**:
   - **Framework Preset**: Next.js.
   - **Root Directory**: Klik *Edit* dan pilih folder `frontend`.
3. **Environment Variables**:
   - Tambahkan variabel lingkungan berikut:
     - `NEXT_PUBLIC_API_URL` = `https://smpn2balung-evote.infinityfreeapp.com/api`
     - `NEXT_PUBLIC_BASE_URL` = `https://smpn2balung-evote.infinityfreeapp.com`
     - `NEXT_PUBLIC_SITE_URL` = `https://nama-domain-anda.vercel.app` *(opsional, untuk domain kustom share medsos)*
4. **Deploy**:
   - Klik **Deploy**. Vercel akan otomatis melakukan proses build dan aplikasi siap diakses secara global!

---

## 📱 Optimasi SEO & Social Media Share (Open Graph)

Aplikasi telah dilengkapi fitur SEO & Social Share Preview bawaan sesuai standar Google & media sosial:

1. **Kartu Preview Media Sosial (WhatsApp, Facebook, Telegram, Twitter, LinkedIn)**:
   - Gambar Open Graph resmi berukuran standar **1200 x 630 px** (`/og-image.png`) dengan logo resmi SMPN 2 Balung, judul tebal, prinsip pemilihan, dan tombol ajakan memilih.
   - Judul & deskripsi informatif otomatis muncul saat tautan dibagikan ke grup chat atau status media sosial.
2. **Technical SEO Lengkap**:
   - **Sitemap Dinamis**: Terbit di `/sitemap.xml` untuk pengindeksan halaman Google.
   - **Robots.txt**: Terbit di `/robots.txt` dengan proteksi area `/admin/`.
   - **PWA Manifest**: File `/manifest.json` dan ikon aplikasi (`icon-192x192.png`, `icon-512x512.png`, `apple-touch-icon.png`).
   - **Schema.org JSON-LD**: Metadata terstruktur `WebApplication` & `EducationalOrganization`.
   - **Halaman Pengalihan (Redirect)**: Tersedia `/redirect.html` bagi panitia yang ingin membagikan link pengalihan instan lengkap dengan kartu Open Graph.

---

## 🔐 Akun Default Administrator

| Keterangan | Nilai |
| :--- | :--- |
| **URL Login Admin** | `/admin/login` |
| **Email Administrator** | `admin@smpn2balung.sch.id` |
| **Kata Sandi Default** | `admin123` |

> *Catatan Keamanan: Setelah pertama kali login di server produksi, Anda disarankan untuk mengubah kata sandi administrator melalui menu **Pengaturan Akun**.*

---

## 📜 Lisensi & Hak Cipta
Dikembangkan untuk Panitia Pemilihan Ketua OSIS **SMPN 2 Balung**  
Kabupaten Jember, Jawa Timur.  
Lisensi: [MIT License](LICENSE).
