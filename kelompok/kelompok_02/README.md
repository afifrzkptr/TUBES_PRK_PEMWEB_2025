# Sistem Informasi Rumah Sakit (Kelompok 02)

Aplikasi berbasis web untuk manajemen operasional rumah sakit yang mencakup modul Pasien, Dokter, dan Admin.

## Anggota Kelompok
* **Afif Rizki Putra** (2315061061)
* **Puan Akeyla Maharani Munaji** (2315061070)
* **Dara Ayu Rahmadilla** (2315061092)
* **Nabila Putri Ayu Ningtyas** (2315061016)

## Deskripsi Project
Project ini adalah Sistem Informasi Rumah Sakit yang dibangun menggunakan PHP Native (tanpa framework) dan MySQL. Aplikasi ini bertujuan untuk mempermudah administrasi rumah sakit dalam mengelola data pasien, dokter, jadwal janji temu (appointments), dan rekam medis (medical records).

### Fitur Utama:
1.  **Modul Admin**:
    *   Dashboard statistik (Total pasien, dokter, antrian).
    *   Manajemen User (CRUD Role: Admin, Dokter, Pasien).
    *   Manajemen Dokter & Pasien.
    *   Manajemen Appointment (Jadwal Janji Temu).
    *   Melihat & Mencetak Riwayat Medis Pasien.
2.  **Modul Dokter**:
    *   Dashboard jadwal praktek hari ini.
    *   Input rekam medis pasien (Diagnosis, Tindakan, Obat).
    *   Melihat riwayat pasien.
3.  **Modul Pasien**:
    *   Booking appointment (Janji Temu).
    *   Melihat riwayat kunjungan.

## Cara Menjalankan Aplikasi

### Prersyarat
*   Web Server (Apache/Nginx) - Direkomendasikan menggunakan **Laragon** atau **XAMPP**.
*   PHP Versi 7.4 atau lebih baru.
*   MySQL Database.

### Instalasi
1.  **Clone / Download Repository**
    Simpan folder `kelompok_02` di dalam direktori root server lokal Anda (misalnya `www` di Laragon atau `htdocs` di XAMPP).
    Path: `D:\LARAGON\laragon\www\kelompok\TUBES_PRK_PEMWEB_2025\kelompok\kelompok_02`

2.  **Import Database**
    *   Buka PHPMyAdmin atau Adminer.
    *   Buat database baru dengan nama: `rumahsakit_db`.
    *   Import file SQL yang terdapat di folder `database/rumahsakit_db.sql`.

3.  **Konfigurasi Database**
    Pastikan konfigurasi database di `config/db.php` sesuai dengan kredensial server lokal Anda:
    ```php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', ''); // Sesuaikan password jika ada
    define('DB_NAME', 'rumahsakit_db');
    ```

4.  **Menjalankan Aplikasi**
    Buka browser dan akses:
    `http://localhost/kelompok/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_02/auth/login.php`

### Akun Demo (Default)
Apabila data dummy sudah di-generate:
*   **Admin**: admin / password (atau cek di tabel `users`)
*   **Dokter**: dokter / password
*   **Pasien**: user / password

*Catatan: Pastikan untuk menjalankan perintah `git pull` terbaru untuk mendapatkan pembaruan terakhir.*
