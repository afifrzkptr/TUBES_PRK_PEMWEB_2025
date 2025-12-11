<?php
/**
 * Konfigurasi Database Lokal
 * File ini digunakan untuk koneksi database lokal development
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rumahsakit_db');
define('DB_PORT', '3306');

/**
 * Fungsi untuk membuat koneksi database
 * @return mysqli object koneksi database
 */
function connect_db()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");

    return $conn;
}

/**
 * Fungsi untuk menutup koneksi database
 * @param mysqli $conn - object koneksi yang akan ditutup
 */
function close_db($conn)
{
    if ($conn) {
        $conn->close();
    }
}


// End of file
