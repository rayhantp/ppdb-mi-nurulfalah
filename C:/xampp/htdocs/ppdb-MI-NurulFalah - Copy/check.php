<?php
include 'config/database.php';
$stmt = $pdo->query('DESCRIBE berkas');
print_r(array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field'));
