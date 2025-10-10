<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kita panggil kedua file konfigurasi Anda
require_once("config/config_site.php");
require_once("core/config_core.php");

echo "<h3>Pengecekan Variabel dan Path</h3>";
echo "<pre>"; // Menggunakan tag <pre> agar lebih mudah dibaca

echo "Nilai dari \$_SERVER['DOCUMENT_ROOT']: ";
var_dump($_SERVER['DOCUMENT_ROOT']);

echo "\nNilai dari CORE_PATH: ";
var_dump(CORE_PATH);

echo "\nNilai dari CLASS_PATH: ";
var_dump(CLASS_PATH);

echo "</pre>";
