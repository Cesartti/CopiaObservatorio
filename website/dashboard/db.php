<?php
// dashboard/db.php
$DB_HOST = "127.0.0.1";
$DB_NAME = "observatorio_boyaca";
$DB_USER = "observa_user";
$DB_PASS = "Observa2025*";

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión a la base de datos");
}
