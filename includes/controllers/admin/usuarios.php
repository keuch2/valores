<?php
/**
 * Controlador admin/usuarios — CRUD de administradores.
 */

declare(strict_types=1);

/** Listado. Ruta: admin/?r=usuarios */
function accion_index(): void
{
    render_admin('usuarios/index', [
        'usuarios'  => Usuario::todos(),
        'idSesion'  => auth_usuario()['id'],
    ], 'Usuarios admin');
}

/** Alta. Ruta: admin/?r=usuarios/crear */
function accion_crear(): void
{
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $r = Usuario::crear(post('nombre'), post('email'), post('password'), (int) post('activo', '1'));
        if ($r['ok']) {
            flash('exito', 'Usuario creado.');
            redirigir('admin/?r=usuarios');
        }
        $error = $r['error'];
    }
    render_admin('usuarios/form', [
        'modo'   => 'crear',
        'u'      => null,
        'error'  => $error,
    ], 'Nuevo usuario');
}

/** Edición. Ruta: admin/?r=usuarios/editar&id=N */
function accion_editar(): void
{
    $id = (int) get('id');
    $u  = Usuario::buscar($id);
    if (!$u) {
        flash('error', 'Usuario no encontrado.');
        redirigir('admin/?r=usuarios');
    }

    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $r = Usuario::actualizar(
            $id, post('nombre'), post('email'), post('password'),
            (int) post('activo', '0'), auth_usuario()['id']
        );
        if ($r['ok']) {
            flash('exito', 'Usuario actualizado.');
            redirigir('admin/?r=usuarios');
        }
        $error = $r['error'];
        $u = Usuario::buscar($id); // refrescar
    }

    render_admin('usuarios/form', [
        'modo'  => 'editar',
        'u'     => $u,
        'error' => $error,
    ], 'Editar usuario');
}

/** Baja. Ruta: admin/?r=usuarios/eliminar (POST) */
function accion_eliminar(): void
{
    csrf_exigir();
    $r = Usuario::eliminar((int) post('id'), auth_usuario()['id']);
    flash($r['ok'] ? 'exito' : 'error', $r['ok'] ? 'Usuario eliminado.' : $r['error']);
    redirigir('admin/?r=usuarios');
}
