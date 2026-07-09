<?php
// Konfigurasi Database (Otomatis deteksi lokal/XAMPP & Vercel/Aiven)
$host     = getenv('DB_HOST') ?: 'localhost';
$port     = getenv('DB_PORT') ?: '3306';
$db_name  = getenv('DB_NAME') ?: 'ppdb_nurulfalah';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Logika SSL untuk koneksi Aiven MySQL di Vercel
$ssl_ca = getenv('DB_SSL_CA');
if ($ssl_ca) {
    // Jika DB_SSL_CA bernilai 'true' atau '1', cari file ca.pem di folder config
    $ca_cert_path = ($ssl_ca === 'true' || $ssl_ca === '1') ? __DIR__ . '/ca.pem' : $ssl_ca;
    
    if (file_exists($ca_cert_path)) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $ca_cert_path;
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>