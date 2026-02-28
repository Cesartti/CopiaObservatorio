<?php
require_once "../tracker.php"; // Para obtener la IP ya registrada

$DB_HOST = "127.0.0.1";
$DB_NAME = "observatorio_boyaca";
$DB_USER = "observa_user";
$DB_PASS = "Observa2025*";

$pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
               $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $profesion = $_POST["profesion"] ?? null;
    $edad      = $_POST["edad"] ?? null;
    $otro      = $_POST["otro"] ?? null;
    $ip        = $_SERVER["REMOTE_ADDR"];

    // Actualiza el último registro de esa IP
    $sql = "UPDATE accesos_observatorio 
            SET profesion=?, edad=?, otro=?
            WHERE ip=? 
            ORDER BY id DESC LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$profesion, $edad, $otro, $ip]);

    echo "OK";
    exit;
}
?>
