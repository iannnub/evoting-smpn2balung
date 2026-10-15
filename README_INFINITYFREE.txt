PANDUAN DEPLOY E-VOTING OSIS SMPN 2 BALUNG KE INFINITYFREE
================================================================

1. DATABASE MYSQL DI INFINITYFREE:
   - Buka Control Panel (cPanel) InfinityFree Anda.
   - Buka menu "MySQL Databases".
   - Buat database baru (misalnya: if0_43172432_evoting).
   - Buka "phpMyAdmin" untuk database tersebut.
   - Klik tab "Import", pilih file "database.sql" yang ada di dalam zip ini, lalu klik "Go/Kirim".
   - Semua tabel, periode aktif, dan data 822 DPT (770 Siswa + 52 Dewan Guru) akan langsung terpasang.

2. KONFIGURASI DATABASE (env.php):
   - Buka file "env.php" di file manager InfinityFree (atau sebelum upload).
   - Pastikan DB_HOST, DB_NAME, DB_USER, dan DB_PASS sesuai dengan akun MySQL InfinityFree Anda:
     Contoh:
     'DB_HOST' => 'sql203.infinityfree.com',
     'DB_NAME' => 'if0_43172432_evoting',
     'DB_USER' => 'if0_43172432',
     'DB_PASS' => 'Password_Akun_InfinityFree_Anda',

3. UPLOAD FILE:
   - Ekstrak seluruh isi file zip ini langsung ke dalam folder: /htdocs/
   - Pastikan struktur di /htdocs/ adalah:
     /htdocs/
       ├── app/
       ├── config/
       ├── helpers/
       ├── public/
       ├── storage/
       ├── .htaccess
       ├── env.php
       └── database.sql

4. SELESAI & AKSES:
   - Buka browser dan akses website Anda:
     https://evoting-smpn2balung.infinityfreeapp.com/
   - Bilik suara, hasil live auto-refresh, dan detail kandidat sudah aktif 100% tanpa kendala CORS.
   - Panel Admin:
     https://evoting-smpn2balung.infinityfreeapp.com/admin/login
