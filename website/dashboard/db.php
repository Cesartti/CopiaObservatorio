<?php

require_once __DIR__ . '/../config/database.php';

$pdo = cms_pdo();
$db_error = null;

if (!$pdo) {
    $db_error = 'No se pudo conectar a la base de datos del dashboard. Revise credenciales o cree la BD local.';
}
