<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('users', true);
require_once __DIR__ . '/../config/database.php';

$cmsTitle = 'Usuarios';
$cmsNav = 'users';
$pdo = cms_pdo();
$error = '';
$message = '';

$me = auth_user();
$myId = (int) ($me['id'] ?? 0);

/** Cuenta administradores generales activos (para no dejar el sistema sin admin). */
function cms_active_admin_count(PDO $pdo): int
{
    $sql = 'SELECT COUNT(*) FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE r.code = "admin_general" AND u.is_active = 1';

    return (int) $pdo->query($sql)->fetchColumn();
}

/** Indica si el usuario objetivo es admin_general activo. */
function cms_is_active_admin(PDO $pdo, int $userId): bool
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE u.id = ? AND r.code = "admin_general" AND u.is_active = 1');
    $st->execute([$userId]);

    return (int) $st->fetchColumn() > 0;
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $targetId = (int) ($_POST['id'] ?? 0);
    try {
        if ($action === 'add') {
            $email = trim($_POST['email'] ?? '');
            $name = trim($_POST['full_name'] ?? '');
            $pass = $_POST['password'] ?? '';
            $rid = (int) ($_POST['role_id'] ?? 0);
            if ($email === '' || $name === '' || strlen($pass) < 8 || $rid < 1) {
                throw new RuntimeException('Correo, nombre, contraseña (8+) y rol son obligatorios.');
            }
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role_id, is_active) VALUES (?,?,?,?,1)');
            $stmt->execute([$name, $email, $hash, $rid]);
            $message = 'Usuario creado.';
        } elseif ($action === 'toggle') {
            if ($targetId === $myId) {
                throw new RuntimeException('No puede desactivar su propia cuenta.');
            }
            // Si va a desactivar un admin activo, verificar que no sea el último.
            if (cms_is_active_admin($pdo, $targetId) && cms_active_admin_count($pdo) <= 1) {
                throw new RuntimeException('No puede desactivar al último administrador general activo.');
            }
            $stmt = $pdo->prepare('UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?');
            $stmt->execute([$targetId]);
            $message = 'Estado del usuario actualizado.';
        } elseif ($action === 'role') {
            $rid = (int) ($_POST['role_id'] ?? 0);
            if ($rid < 1) {
                throw new RuntimeException('Rol inválido.');
            }
            if ($targetId === $myId) {
                throw new RuntimeException('No puede cambiar su propio rol (evita perder el acceso de administrador).');
            }
            // Si el objetivo es el último admin y se le cambia el rol, bloquear.
            if (cms_is_active_admin($pdo, $targetId) && cms_active_admin_count($pdo) <= 1) {
                $chk = $pdo->prepare('SELECT code FROM roles WHERE id = ?');
                $chk->execute([$rid]);
                if ($chk->fetchColumn() !== 'admin_general') {
                    throw new RuntimeException('No puede quitar el rol de administrador al último admin activo.');
                }
            }
            $stmt = $pdo->prepare('UPDATE users SET role_id = ? WHERE id = ?');
            $stmt->execute([$rid, $targetId]);
            $message = 'Rol actualizado.';
        } elseif ($action === 'reset') {
            $pass = $_POST['password'] ?? '';
            if (strlen($pass) < 8) {
                throw new RuntimeException('La nueva contraseña debe tener al menos 8 caracteres.');
            }
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$hash, $targetId]);
            $message = 'Contraseña restablecida.';
        } elseif ($action === 'delete') {
            if ($targetId === $myId) {
                throw new RuntimeException('No puede eliminar su propia cuenta.');
            }
            if (cms_is_active_admin($pdo, $targetId) && cms_active_admin_count($pdo) <= 1) {
                throw new RuntimeException('No puede eliminar al último administrador general activo.');
            }
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$targetId]);
            $message = 'Usuario eliminado.';
        }
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // Mensaje amigable para correo duplicado en alta.
        if ($action === 'add' && stripos($msg, 'Duplicate') !== false) {
            $msg = 'Ya existe un usuario con ese correo.';
        }
        $error = $msg ?: 'No se pudo completar la operación.';
    }
}

$roles = [];
$users = [];
if ($pdo) {
    try {
        $roles = $pdo->query('SELECT id, code, name FROM roles ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
        $users = $pdo->query('SELECT u.id, u.full_name, u.email, u.is_active, u.role_id, u.last_login_at, r.code AS role FROM users u INNER JOIN roles r ON r.id = u.role_id ORDER BY u.id ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'No se pudieron cargar usuarios.';
    }
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-3">Usuarios y roles</h1>
            <p class="small text-muted">Los permisos por módulo se definen en la tabla <code>role_permissions</code>. Cada usuario tiene un rol que determina a qué secciones del CMS accede.</p>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Nuevo usuario</h2>
                    <form method="post" class="row g-2">
                        <input type="hidden" name="action" value="add">
                        <div class="col-md-4"><label class="form-label">Nombre</label><input type="text" name="full_name" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">Contraseña</label><input type="password" name="password" class="form-control" required minlength="8"></div>
                        <div class="col-md-4">
                            <label class="form-label">Rol</label>
                            <select name="role_id" class="form-select" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= (int) $r['id'] ?>"><?= htmlspecialchars($r['name'] . ' (' . $r['code'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-user-plus me-1"></i> Crear</button></div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle bg-white shadow-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th style="min-width:210px">Rol</th>
                            <th>Estado</th>
                            <th>Último ingreso</th>
                            <th style="min-width:200px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): $isMe = (int) $u['id'] === $myId; ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($u['full_name']) ?>
                                <?php if ($isMe): ?><span class="badge text-bg-info ms-1">tú</span><?php endif; ?>
                            </td>
                            <td class="small"><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-1">
                                    <input type="hidden" name="action" value="role">
                                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                    <select name="role_id" class="form-select form-select-sm" <?= $isMe ? 'disabled' : '' ?>>
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= (int) $r['id'] ?>" <?= (int) $r['id'] === (int) $u['role_id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['code']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (!$isMe): ?><button class="btn btn-sm btn-outline-secondary" type="submit" title="Guardar rol"><i class="fa-solid fa-check"></i></button><?php endif; ?>
                                </form>
                            </td>
                            <td>
                                <span class="badge bg-<?= !empty($u['is_active']) ? 'success' : 'secondary' ?>"><?= !empty($u['is_active']) ? 'Activo' : 'Inactivo' ?></span>
                            </td>
                            <td class="small text-muted"><?= $u['last_login_at'] ? htmlspecialchars((string) $u['last_login_at']) : '—' ?></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#reset-<?= (int) $u['id'] ?>" title="Restablecer contraseña"><i class="fa-solid fa-key"></i></button>
                                    <?php if (!$isMe): ?>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <button class="btn btn-sm btn-outline-warning" type="submit" title="<?= !empty($u['is_active']) ? 'Desactivar' : 'Reactivar' ?>">
                                                <i class="fa-solid <?= !empty($u['is_active']) ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                            </button>
                                        </form>
                                        <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar definitivamente a <?= htmlspecialchars(addslashes($u['email'])) ?>?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div class="collapse mt-2" id="reset-<?= (int) $u['id'] ?>">
                                    <form method="post" class="input-group input-group-sm" style="max-width:320px">
                                        <input type="hidden" name="action" value="reset">
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <input type="text" name="password" class="form-control" placeholder="Nueva contraseña (8+)" minlength="8" required>
                                        <button class="btn btn-outline-primary" type="submit">Guardar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay usuarios.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
