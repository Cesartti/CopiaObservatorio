<?php

/**
 * Restablece la contraseña de un usuario del CMS desde la consola del servidor.
 *
 * Se usa cuando nadie puede entrar al panel y por tanto no se puede usar el
 * botón de restablecer de CMS → Usuarios. La tabla `users` solo guarda el hash
 * de la contraseña, que es irreversible: no hay forma de «recuperar» la
 * anterior, únicamente de poner una nueva.
 *
 * La contraseña se pide por teclado y con el eco apagado, así que no queda en
 * el historial del shell ni en la lista de procesos. Tampoco se escribe en
 * ningún registro.
 *
 * Solo corre por línea de comandos, nunca por web.
 *
 * Uso, en el servidor y dentro del docroot:
 *     php scripts/restablecer_clave_cms.php            (lista los usuarios)
 *     php scripts/restablecer_clave_cms.php correo@dominio
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Este script solo se ejecuta por consola.\n");
}

$raiz = dirname(__DIR__);
$config = $raiz . '/website/config/database.php';
if (!is_file($config)) {
    // En el servidor el docroot es website/, así que el script puede quedar al lado.
    $config = $raiz . '/config/database.php';
}
if (!is_file($config)) {
    exit("No encuentro config/database.php. Ejecute el script desde la raíz del sitio.\n");
}
require_once $config;

$pdo = cms_pdo();
if (!$pdo) {
    exit('Sin conexión a la base de datos: ' . (cms_last_db_error() ?? 'motivo desconocido')
        . "\n");
}

/** Lee del teclado sin mostrar lo que se escribe. */
function leer_oculto(string $mensaje): string
{
    fwrite(STDOUT, $mensaje);
    $silencio = false;
    if (DIRECTORY_SEPARATOR === '/' && @shell_exec('command -v stty')) {
        shell_exec('stty -echo');
        $silencio = true;
    }
    $valor = rtrim((string) fgets(STDIN), "\r\n");
    if ($silencio) {
        shell_exec('stty echo');
        fwrite(STDOUT, "\n");
    } else {
        fwrite(STDOUT, "\n[aviso] No se pudo ocultar el texto en pantalla.\n");
    }

    return $valor;
}

$usuarios = $pdo->query(
    'SELECT u.id, u.full_name, u.email, u.is_active, r.code AS rol
       FROM users u INNER JOIN roles r ON r.id = u.role_id
      ORDER BY (r.code = "admin_general") DESC, u.id'
)->fetchAll(PDO::FETCH_ASSOC);

if (!$usuarios) {
    exit("No hay usuarios en la tabla `users`.\n");
}

$correo = $argv[1] ?? null;
if ($correo === null) {
    echo "Usuarios del CMS\n";
    echo str_repeat('-', 78), "\n";
    foreach ($usuarios as $u) {
        printf("  %-34s %-20s %s\n", $u['email'], $u['rol'],
            $u['is_active'] ? 'activo' : 'INACTIVO');
    }
    echo str_repeat('-', 78), "\n";
    echo "\nPara restablecer una:  php " . basename(__FILE__) . " correo@dominio\n";
    exit(0);
}

$elegido = null;
foreach ($usuarios as $u) {
    if (strcasecmp($u['email'], $correo) === 0) {
        $elegido = $u;
        break;
    }
}
if (!$elegido) {
    exit("No existe un usuario con el correo «$correo». Corra el script sin argumentos "
        . "para ver la lista.\n");
}

echo "Usuario:  {$elegido['full_name']} <{$elegido['email']}>\n";
echo "Perfil:   {$elegido['rol']}" . ($elegido['is_active'] ? '' : '  (INACTIVO)') . "\n\n";

$clave = leer_oculto('Nueva contraseña: ');
if (strlen($clave) < 10) {
    exit("Muy corta: use al menos 10 caracteres.\n");
}
if ($clave !== leer_oculto('Repítala: ')) {
    exit("Las dos no coinciden. No se cambió nada.\n");
}

$st = $pdo->prepare('UPDATE users SET password_hash = ?, is_active = 1 WHERE id = ?');
$st->execute([password_hash($clave, PASSWORD_DEFAULT), $elegido['id']]);

// Se borra de memoria en cuanto deja de hacer falta.
$clave = str_repeat("\0", 64);
unset($clave);

echo "\nListo. La contraseña de {$elegido['email']} quedó cambiada"
    . ($elegido['is_active'] ? '' : ' y el usuario se reactivó') . ".\n";
echo "Entre en https://observatorios.boyaca.gov.co/admin/auth/login.php\n";
echo "Guárdela en el gestor de contraseñas; aquí no queda registrada en ninguna parte.\n";
