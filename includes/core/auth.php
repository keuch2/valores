<?php
/**
 * auth.php — Autenticación de administradores.
 *
 * Login con password_verify(), protección anti fuerza bruta (bloqueo temporal),
 * y guards para proteger rutas del panel. Requiere sesión activa (sesiones.php).
 */

declare(strict_types=1);

/**
 * Intenta autenticar a un admin por email + contraseña.
 *
 * @return array{ok:bool, error?:string} Resultado del intento.
 */
function auth_login(string $email, string $password): array
{
    $email = trim($email);

    $stmt = db()->prepare(
        'SELECT id, nombre, email, password_hash, rol, activo, intentos_fallidos, bloqueado_hasta
         FROM admin_users WHERE email = :email LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $u = $stmt->fetch();

    // Mensaje genérico para no revelar si el email existe (anti-enumeración).
    $errGenerico = 'Email o contraseña incorrectos.';

    if (!$u) {
        return ['ok' => false, 'error' => $errGenerico];
    }

    // ¿Bloqueado temporalmente?
    if ($u['bloqueado_hasta'] !== null && strtotime($u['bloqueado_hasta']) > time()) {
        $min = (int) ceil((strtotime($u['bloqueado_hasta']) - time()) / 60);
        return ['ok' => false, 'error' => "Cuenta bloqueada temporalmente. Probá de nuevo en {$min} min."];
    }

    if ((int) $u['activo'] !== 1) {
        return ['ok' => false, 'error' => 'La cuenta está desactivada.'];
    }

    if (!password_verify($password, $u['password_hash'])) {
        auth_registrar_fallo((int) $u['id'], (int) $u['intentos_fallidos']);
        return ['ok' => false, 'error' => $errGenerico];
    }

    // Éxito: rehash si el algoritmo por defecto cambió, limpiar contadores, abrir sesión.
    if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
        $nuevo = password_hash($password, PASSWORD_DEFAULT);
        db()->prepare('UPDATE admin_users SET password_hash = :h WHERE id = :id')
            ->execute([':h' => $nuevo, ':id' => $u['id']]);
    }

    db()->prepare(
        'UPDATE admin_users
         SET intentos_fallidos = 0, bloqueado_hasta = NULL, ultimo_acceso = NOW()
         WHERE id = :id'
    )->execute([':id' => $u['id']]);

    sesion_regenerar();  // previene fijación de sesión
    $_SESSION['admin'] = [
        'id'     => (int) $u['id'],
        'nombre' => $u['nombre'],
        'email'  => $u['email'],
        'rol'    => $u['rol'],
    ];

    return ['ok' => true];
}

/**
 * Registra un intento fallido y aplica bloqueo temporal al superar el límite.
 */
function auth_registrar_fallo(int $userId, int $intentosActuales): void
{
    $nuevos = $intentosActuales + 1;

    if ($nuevos >= LOGIN_MAX_INTENTOS) {
        $hasta = date('Y-m-d H:i:s', time() + LOGIN_BLOQUEO_MINUTOS * 60);
        db()->prepare(
            'UPDATE admin_users SET intentos_fallidos = :n, bloqueado_hasta = :hasta WHERE id = :id'
        )->execute([':n' => $nuevos, ':hasta' => $hasta, ':id' => $userId]);
    } else {
        db()->prepare('UPDATE admin_users SET intentos_fallidos = :n WHERE id = :id')
            ->execute([':n' => $nuevos, ':id' => $userId]);
    }
}

/**
 * ¿Hay un admin autenticado en la sesión actual?
 */
function auth_check(): bool
{
    return isset($_SESSION['admin']['id']);
}

/**
 * Devuelve los datos del admin en sesión (o null).
 */
function auth_usuario(): ?array
{
    return $_SESSION['admin'] ?? null;
}

/**
 * Guard: exige sesión admin; si no la hay, redirige al login.
 * Usar al inicio de cada controlador protegido del panel.
 */
function auth_exigir(): void
{
    if (!auth_check()) {
        flash('error', 'Iniciá sesión para continuar.');
        redirigir('admin/login');
    }
}

/**
 * Cierra la sesión del admin.
 */
function auth_logout(): void
{
    sesion_destruir();
}
