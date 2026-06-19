<?php
require_once __DIR__ . "/tracker.php"; // Para obtener la IP ya registrada
require_once __DIR__ . "/config/database.php";

$pdo = cms_pdo();
if (!$pdo) {
    http_response_code(503);
    echo "ERROR";
    exit;
}

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

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$profesion, $edad, $otro, $ip]);
    } catch (PDOException $e) {
        // La tabla de accesos puede no existir en entornos locales; no romper la respuesta
        http_response_code(503);
        echo "ERROR";
        exit;
    }

    echo "OK";
    exit;
}
?>
