<?php
/**
 * Controlador admin/agentes — receptores de solicitudes (round-robin).
 * CRUD de nombre/email/activo/orden. total_asignadas y ultima_asignacion
 * se muestran (solo lectura) para ver el reparto.
 */

declare(strict_types=1);

function accion_index(): void
{
    $agentes = db()->query(
        'SELECT * FROM agentes ORDER BY activo DESC, orden ASC, id ASC'
    )->fetchAll();
    render_admin('agentes/index', ['agentes' => $agentes], 'Agentes');
}

function accion_crear(): void
{
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $error = agentes_guardar(null);
        if ($error === null) { flash('exito', 'Agente creado.'); redirigir('admin/?r=agentes'); }
    }
    render_admin('agentes/form', ['modo' => 'crear', 'a' => null, 'error' => $error], 'Nuevo agente');
}

function accion_editar(): void
{
    $id = (int) get('id');
    $stmt = db()->prepare('SELECT * FROM agentes WHERE id = :id'); $stmt->execute([':id' => $id]);
    $a = $stmt->fetch();
    if (!$a) { flash('error', 'Agente no encontrado.'); redirigir('admin/?r=agentes'); }

    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $error = agentes_guardar($id);
        if ($error === null) { flash('exito', 'Agente actualizado.'); redirigir('admin/?r=agentes'); }
    }
    render_admin('agentes/form', ['modo' => 'editar', 'a' => $a, 'error' => $error], 'Editar agente');
}

function accion_eliminar(): void
{
    csrf_exigir();
    db()->prepare('DELETE FROM agentes WHERE id = :id')->execute([':id' => (int) post('id')]);
    flash('exito', 'Agente eliminado.');
    redirigir('admin/?r=agentes');
}

/** Valida y guarda (insert/update). Devuelve mensaje de error o null. */
function agentes_guardar(?int $id): ?string
{
    $nombre = post('nombre');
    $email  = post('email');
    $activo = isset($_POST['activo']) ? 1 : 0;
    $orden  = (int) post('orden', '0');

    if ($nombre === '' || !email_valido($email)) {
        return 'Nombre y email válido son obligatorios.';
    }

    if ($id === null) {
        db()->prepare('INSERT INTO agentes (nombre, email, activo, orden) VALUES (:n,:e,:a,:o)')
            ->execute([':n' => $nombre, ':e' => $email, ':a' => $activo, ':o' => $orden]);
    } else {
        db()->prepare('UPDATE agentes SET nombre=:n, email=:e, activo=:a, orden=:o WHERE id=:id')
            ->execute([':n' => $nombre, ':e' => $email, ':a' => $activo, ':o' => $orden, ':id' => $id]);
    }
    return null;
}
