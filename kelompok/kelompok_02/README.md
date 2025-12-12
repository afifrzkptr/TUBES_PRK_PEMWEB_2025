# Sistem Informasi Rumah Sakit (Kelompok 02)

Aplikasi berbasis web untuk manajemen operasional rumah sakit yang mencakup modul Pasien, Dokter, dan Admin.


## Anggota Kelompok

- **Afif Rizki Putra** (2315061061)
- **Puan Akeyla Maharani Munaji** (2315061070)
- **Dara Ayu Rahmadilla** (2315061092)
- **Nabila Putri Ayu Ningtyas** (2315061016)

## Deskripsi Project

Project ini adalah Sistem Informasi Rumah Sakit yang dibangun menggunakan PHP Native (tanpa framework) dan MySQL. Aplikasi ini bertujuan untuk mempermudah administrasi rumah sakit dalam mengelola data pasien, dokter, jadwal janji temu (appointments), dan rekam medis (medical records).

## Fitur Utama

### 1. Modul Admin:
- Dashboard statistik (Total pasien, dokter, antrian).
- Manajemen User (CRUD Role: Admin, Dokter, Pasien).
- Manajemen Dokter & Pasien.
- Manajemen Appointment (Jadwal Janji Temu).
- Melihat & Mencetak Riwayat Medis Pasien.

### 2. Modul Dokter:
- Dashboard jadwal praktek hari ini.
- Input rekam medis pasien (Diagnosis, Tindakan, Obat).
- Melihat riwayat pasien.

### 3. Modul Pasien:
- Booking appointment (Janji Temu).
- Melihat riwayat kunjungan.

## 📁 Struktur Folder

```
kelompok_02/
├── admin/              # Halaman admin
│   ├── appointments.php
│   ├── dashboard.php
│   ├── doctors.php
│   ├── medical_records.php
│   ├── patients.php
│   ├── patient_medical_history.php
│   ├── users.php
│   └── includes/
│       ├── check_admin.php
│       ├── footer.php
│       └── header.php
├── assets/
│   └── css/
│       ├── admin.css
│       ├── login.css
│       └── register.css
├── auth/               # Autentikasi
│   ├── login.php
│   ├── login_success.php
│   ├── logout.php
│   └── register.php
├── config/
│   └── config.lokal.php
├── database/
│   └── rumahSakit_db.sql
└── README.md
```

## 🗄️ Database Schema

### Tables
1. **roles** - Role pengguna (Admin, Dokter, Pasien)
2. **users** - Data pengguna sistem
3. **patients** - Data pasien
4. **doctors** - Data dokter
5. **appointments** - Jadwal appointment/antrian
6. **medical_records** - Rekam medis pasien

### Relationships
- `users` → `roles` (Many-to-One)
- `patients` → `users` (One-to-One)
- `doctors` → `users` (One-to-One)
- `appointments` → `patients` (Many-to-One)
- `appointments` → `doctors` (Many-to-One)
- `medical_records` → `appointments` (One-to-One)

## 🛠️ Teknologi

- **Backend**: PHP 8.x
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Icons**: Lucide Icons
- **Font**: Google Fonts (Inter)

## Cara Menjalankan Aplikasi

### Prersyarat

- Web Server (Apache/Nginx) - Direkomendasikan menggunakan Laragon atau XAMPP.
- PHP Versi 7.4 atau lebih baru.
- MySQL Database.

### Instalasi

1. **Clone / Download Repository**
   
   Simpan folder `kelompok_02` di dalam direktori root server lokal Anda (misalnya `www` di Laragon atau `htdocs` di XAMPP). Path: `D:\LARAGON\Laragon\www\kelompok_TUBES_PRK_PEMWEB_2025\kelompok\kelompok_02`

2. **Import Database**
   
   - Buka PHPMyAdmin atau Adminer.
   - Buat database baru dengan nama: `rumahsakit_db`.
   - Kemudian import file SQL yang terdapat di folder `database/rumahsakit_db.sql`.

3. **Konfigurasi Database**
   
   Periksa konfigurasi database di `config/db.php`. Sesuai dengan kredensial server lokal Anda:
   
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Sesuaikan password jika ada
   define('DB_NAME', 'rumahsakit_db');
   ```

4. **Menjalankan Aplikasi**
   
   Buka browser dan akses: `http://localhost/kelompok_TUBES_PRK_PEMWEB_2025/kelompok/kelompok_02/auth/login.php`

## Akun Demo (Default)

Apabila data dummy sudah di-generate:

- **Admin**: `admin` / password (atau cek di tabel `users`)
- **Dokter**: `dokter` / password
- **Pasien**: `user` / password

*Catatan: Pastikan untuk menjalankan perintah* `git pull` *terbaru untuk mendapatkan pembaruan terakhir.*

## 📁 Struktur Folder

```
kelompok_02/
├── admin/                      # Halaman admin
│   ├── appointments.php        # Manajemen appointment
│   ├── dashboard.php          # Dashboard admin
│   ├── doctors.php            # Manajemen dokter
│   ├── medical_records.php    # Daftar rekam medis
│   ├── patient_medical_history.php  # Detail riwayat pasien
│   ├── patients.php           # Manajemen pasien
│   ├── users.php              # Manajemen user
│   └── includes/              # File include admin
│       ├── check_admin.php    # Middleware admin
│       ├── footer.php         # Footer template
│       └── header.php         # Header template
├── assets/                    # Asset statis
│   └── css/
│       ├── admin.css          # Style admin panel
│       ├── login.css          # Style halaman login
│       └── register.css       # Style halaman register
├── auth/                      # Autentikasi
│   ├── login.php             # Halaman login
│   ├── login_success.php     # Halaman sukses login
│   ├── logout.php            # Proses logout
│   └── register.php          # Halaman registrasi
├── config/                    # Konfigurasi
│   └── config.lokal.php      # Konfigurasi database
├── database/                  # Database
│   └── rumahSakit_db.sql     # Schema & data SQL
└── README.md                  # Dokumentasi
```

## Teknologi yang Digunakan

- **Backend**: PHP Native (tanpa framework)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Icons**: Lucide Icons
- **Font**: Google Fonts (Inter)

## Fitur Keamanan

- Password hashing dengan `password_hash()`
- Prepared statements untuk mencegah SQL injection
- Session-based authentication
- Role-based access control (RBAC)
- XSS protection dengan `htmlspecialchars()`

## Struktur Database

### Tables
1. **roles** - Role pengguna (Admin, Dokter, Pasien)
2. **users** - Data pengguna sistem
3. **patients** - Data pasien
4. **doctors** - Data dokter
5. **appointments** - Jadwal appointment/antrian
6. **medical_records** - Rekam medis pasien

##Screenshot Tampilan Aplikasi
1. Login
   <img width="1918" height="1029" alt="image" src="https://github.com/user-attachments/assets/4d298281-9c29-4521-a50f-0a760158fa61" />
2. Register
   <img width="1917" height="958" alt="image" src="https://github.com/user-attachments/assets/f63869fc-b2d6-4c9b-bc13-8aff52eac644" />
3. Lupa Password
   <img width="1907" height="946" alt="image" src="https://github.com/user-attachments/assets/90b1f70d-1ac1-470b-8a1c-b455ff7b0765" />
   <img width="1916" height="946" alt="image" src="https://github.com/user-attachments/assets/9b1096ed-160d-40eb-968a-8c7757048da6" />
   <img width="1910" height="950" alt="image" src="https://github.com/user-attachments/assets/8b1613ad-4c98-4157-8408-7bffafd7cea8" />
4. Admin
   <img width="1900" height="945" alt="image" src="https://github.com/user-attachments/assets/9b4d4a50-f3cd-478f-b157-8bab8614a01c" />
   <img width="1612" height="690" alt="image" src="https://github.com/user-attachments/assets/3852f1be-c312-4388-bbff-40b61465843e" />
   <img width="1911" height="952" alt="Screenshot 2025-12-12 214640" src="https://github.com/user-attachments/assets/c566adab-4807-4700-9e17-d510b37e66c6" />
   <img width="1918" height="952" alt="image" src="https://github.com/user-attachments/assets/b2f6d96e-7f65-477d-a7b7-262ade79b1fc" />
   <img width="1916" height="949" alt="image" src="https://github.com/user-attachments/assets/f54bf14c-723f-4824-bc8b-32e4aadb2979" />
   <img width="1899" height="952" alt="image" src="https://github.com/user-attachments/assets/13013f3e-e629-4899-af9b-046e17e30fcf" />
   <img width="1914" height="954" alt="image" src="https://github.com/user-attachments/assets/fae3a0b0-c9fb-4c58-b558-9defda7e5773" />
5. Dokter
   <img width="1919" height="864" alt="image" src="https://github.com/user-attachments/assets/133eb446-36f5-42e2-87d6-ecadcf355599" />
   <img width="1915" height="965" alt="image" src="https://github.com/user-attachments/assets/e8a416a5-c9d1-4a22-a5f2-ea28a6e594b3" />
   <img width="1917" height="963" alt="image" src="https://github.com/user-attachments/assets/99c96fd6-8e47-4b74-9acf-39b2543650c7" />
6. Pasien
   <img width="1898" height="965" alt="image" src="https://github.com/user-attachments/assets/6b58505b-b24f-4573-86bd-2da04b60dee2" />
   <img width="1919" height="956" alt="image" src="https://github.com/user-attachments/assets/f2688d88-2768-43d7-b5b3-1635e847a6d5" />
   <img width="1899" height="952" alt="image" src="https://github.com/user-attachments/assets/3cf94be6-0946-4c2f-8ec9-e21fbf9c6382" />


## Kontak

Untuk pertanyaan atau masalah, silakan hubungi anggota kelompok melalui repository ini.

---

**Last Updated**: December 11, 2025 | **Kelompok 02** - Pemrograman Web 2025
