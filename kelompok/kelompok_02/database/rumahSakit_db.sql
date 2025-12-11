-- --------------------------------------------------------
-- 1. PEMBUATAN DAN PENGGUNAAN DATABASE
-- --------------------------------------------------------

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS rumahSakit_db;

-- Gunakan database yang baru dibuat
USE rumahSakit_db;


-- --------------------------------------------------------
-- 2. STRUKTUR TABEL (CREATE TABLE)
-- --------------------------------------------------------

-- 1. Tabel ROLES
CREATE TABLE roles (
    id_role INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE COMMENT 'e.g., Admin, Dokter, Pasien'
);

-- 2. Tabel USERS (untuk Login dan Autentikasi)
CREATE TABLE users (
    id_user INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_role INT(11) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (id_role) REFERENCES roles(id_role)
);

-- 3. Tabel DOCTORS (Data Detail Dokter)
CREATE TABLE doctors (
    id_doctor INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_user INT(11) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    specialization VARCHAR(100),
    phone VARCHAR(15),
    license_no VARCHAR(50) UNIQUE,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- 4. Tabel PATIENTS (Data Detail Pasien)
CREATE TABLE patients (
    id_patient INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_user INT(11) UNIQUE,
    name VARCHAR(150) NOT NULL,
    birth_date DATE,
    address TEXT,
    phone VARCHAR(15),
    med_record_no VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nomor Rekam Medis (penting)',
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- 5. Tabel APPOINTMENTS (Janji Temu/Antrian)
CREATE TABLE appointments (
    id_appointment INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_patient INT(11) NOT NULL,
    id_doctor INT(11) NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    status ENUM('Scheduled', 'In Progress', 'Done', 'Canceled') NOT NULL DEFAULT 'Scheduled',
    queue_number INT(11) COMMENT 'Nomor antrian untuk hari/waktu tersebut',
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient),
    FOREIGN KEY (id_doctor) REFERENCES doctors(id_doctor),
    UNIQUE KEY unique_appointment (id_doctor, date, time)
);

-- 6. Tabel MEDICAL_RECORDS (Dokumentasi Medis)
CREATE TABLE medical_records (
    id_record INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_appointment INT(11) NOT NULL UNIQUE,
    diagnosis TEXT NOT NULL,
    treatment TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_appointment) REFERENCES appointments(id_appointment)
);


-- --------------------------------------------------------
-- 3. DATA AWAL (SEEDING)
-- --------------------------------------------------------

-- Isi data roles
INSERT INTO roles (id_role, role_name) VALUES
(1, 'Admin'),
(2, 'Dokter'),
(3, 'Pasien');

-- Contoh Akun (Gunakan password ter-hash yang aman di implementasi PHP)
-- Untuk testing awal, anggap password '123456' telah di-hash
INSERT INTO users (id_user, id_role, username, password, email) VALUES
(1, 1, 'admin_klinik', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'admin@klinik.com'), -- Pass: 123456
(2, 2, 'dr_sinta', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'sinta@klinik.com'), -- Pass: 123456
(3, 3, 'pasien_uji', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'pasien@mail.com'); -- Pass: 123456

-- Contoh data Dokter
INSERT INTO doctors (id_user, name, specialization) VALUES
(2, 'Dr. Sinta Dewi, Sp.A', 'Anak');

-- Contoh data Pasien
INSERT INTO patients (id_user, name, med_record_no) VALUES
(3, 'Budi Santoso', 'MR-0001');