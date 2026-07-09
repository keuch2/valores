<?php
/**
 * Modelo Usuario — CRUD de administradores con sus reglas de negocio.
 *
 * Reglas duras (sección 5.1 del prompt):
 *  - El superadmin no puede auto-desactivarse.
 *  - Nunca puede quedar el sistema sin ningún admin activo.
 */

declare(strict_types=1);

final class Usuario
{
    /** Lista todos los usuarios admin. */
    public static function todos(): array
    {
        return db()->query(
            'SELECT id, nombre, email, rol, activo, ultimo_acceso, created_at
             FROM admin_users ORDER BY nombre'
        )->fetchAll();
    }

    /** Busca un usuario por id. */
    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM admin_users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** ¿El email ya está en uso por otro usuario? */
    public static function emailEnUso(string $email, ?int $excluirId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM admin_users WHERE email = :e';
        $p = [':e' => $email];
        if ($excluirId !== null) { $sql .= ' AND id <> :id'; $p[':id'] = $excluirId; }
        $stmt = db()->prepare($sql);
        $stmt->execute($p);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Cantidad de admins activos (para la regla de "no dejar sin admin"). */
    public static function activosCount(): int
    {
        return (int) db()->query('SELECT COUNT(*) FROM admin_users WHERE activo = 1')->fetchColumn();
    }

    /**
     * Crea un usuario admin.
     * @return array{ok:bool, error?:string, id?:int}
     */
    public static function crear(string $nombre, string $email, string $password, int $activo): array
    {
        $nombre = trim($nombre);
        $email  = trim($email);

        if ($nombre === '' || !email_valido($email) || strlen($password) < 8) {
            return ['ok' => false, 'error' => 'Datos inválidos: nombre, email válido y contraseña de 8+ caracteres.'];
        }
        if (self::emailEnUso($email)) {
            return ['ok' => false, 'error' => 'Ya existe un usuario con ese email.'];
        }

        $stmt = db()->prepare(
            'INSERT INTO admin_users (nombre, email, password_hash, rol, activo)
             VALUES (:n, :e, :h, :rol, :activo)'
        );
        $stmt->execute([
            ':n'      => $nombre,
            ':e'      => $email,
            ':h'      => password_hash($password, PASSWORD_DEFAULT),
            ':rol'    => 'superadmin',
            ':activo' => $activo ? 1 : 0,
        ]);
        return ['ok' => true, 'id' => (int) db()->lastInsertId()];
    }

    /**
     * Actualiza un usuario. $password vacío = no cambia la contraseña.
     * Aplica las reglas de auto-desactivación y de "no dejar sin admin activo".
     * @return array{ok:bool, error?:string}
     */
    public static function actualizar(int $id, string $nombre, string $email, string $password, int $activo, int $idSesion): array
    {
        $u = self::buscar($id);
        if (!$u) {
            return ['ok' => false, 'error' => 'Usuario no encontrado.'];
        }

        $nombre = trim($nombre);
        $email  = trim($email);
        if ($nombre === '' || !email_valido($email)) {
            return ['ok' => false, 'error' => 'Nombre o email inválido.'];
        }
        if (self::emailEnUso($email, $id)) {
            return ['ok' => false, 'error' => 'Ese email ya pertenece a otro usuario.'];
        }
        if ($password !== '' && strlen($password) < 8) {
            return ['ok' => false, 'error' => 'La contraseña debe tener 8+ caracteres.'];
        }

        $desactivando = ((int) $u['activo'] === 1 && !$activo);

        // Regla 1: no auto-desactivarse.
        if ($desactivando && $id === $idSesion) {
            return ['ok' => false, 'error' => 'No podés desactivar tu propia cuenta.'];
        }
        // Regla 2: no dejar el sistema sin ningún admin activo.
        if ($desactivando && self::activosCount() <= 1) {
            return ['ok' => false, 'error' => 'No se puede desactivar: es el único administrador activo.'];
        }

        $campos = 'nombre = :n, email = :e, activo = :activo';
        $p = [':n' => $nombre, ':e' => $email, ':activo' => $activo ? 1 : 0, ':id' => $id];
        if ($password !== '') {
            $campos .= ', password_hash = :h, intentos_fallidos = 0, bloqueado_hasta = NULL';
            $p[':h'] = password_hash($password, PASSWORD_DEFAULT);
        }

        db()->prepare("UPDATE admin_users SET {$campos} WHERE id = :id")->execute($p);
        return ['ok' => true];
    }

    /**
     * Elimina un usuario, respetando las mismas reglas de seguridad.
     * @return array{ok:bool, error?:string}
     */
    public static function eliminar(int $id, int $idSesion): array
    {
        $u = self::buscar($id);
        if (!$u) {
            return ['ok' => false, 'error' => 'Usuario no encontrado.'];
        }
        if ($id === $idSesion) {
            return ['ok' => false, 'error' => 'No podés eliminar tu propia cuenta.'];
        }
        if ((int) $u['activo'] === 1 && self::activosCount() <= 1) {
            return ['ok' => false, 'error' => 'No se puede eliminar: es el único administrador activo.'];
        }
        db()->prepare('DELETE FROM admin_users WHERE id = :id')->execute([':id' => $id]);
        return ['ok' => true];
    }
}
