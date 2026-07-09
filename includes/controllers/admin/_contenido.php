<?php
/**
 * _contenido.php — Motor genérico de CRUD para entidades de contenido.
 *
 * Los controladores por entidad (noticias.php, servicios.php, …) sólo definen
 * su clave y delegan aquí. Toda la lógica de listar/crear/editar/eliminar/reordenar
 * vive en estas funciones, dirigidas por la config declarativa de _entidades.php.
 */

declare(strict_types=1);

require_once __DIR__ . '/_entidades.php';

/** Extrae del $_POST sólo los valores de los campos definidos para la entidad. */
function contenido_leer_post(array $cfg): array
{
    $datos = [];
    foreach ($cfg['campos'] as $campo) {
        $n = $campo['nombre'];
        if ($campo['tipo'] === 'checkbox') {
            $datos[$n] = isset($_POST[$n]) ? 1 : 0;
        } else {
            $datos[$n] = post($n);
        }
    }
    return $datos;
}

/** Listado genérico. */
function contenido_index(string $clave): void
{
    $cfg = entidad_cfg($clave);
    $tabla = entidad_tabla($clave, $cfg);
    render_admin('contenido/lista', [
        'clave'    => $clave,
        'cfg'      => $cfg,
        'registros'=> Crud::listar($tabla),
    ], $cfg['titulo_plural']);
}

/** Alta genérica. */
function contenido_crear(string $clave): void
{
    $cfg = entidad_cfg($clave);
    $tabla = entidad_tabla($clave, $cfg);
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $datos = contenido_leer_post($cfg);
        $error = contenido_validar($cfg, $datos);
        if ($error === null) {
            Crud::crear($tabla, $datos);
            flash('exito', $cfg['titulo_singular'] . ' creado/a.');
            redirigir('admin/?r=' . $clave);
        }
    }

    render_admin('contenido/form', [
        'clave' => $clave, 'cfg' => $cfg, 'reg' => null, 'error' => $error, 'modo' => 'crear',
    ], 'Nuevo: ' . $cfg['titulo_singular']);
}

/** Edición genérica. */
function contenido_editar(string $clave): void
{
    $cfg = entidad_cfg($clave);
    $tabla = entidad_tabla($clave, $cfg);
    $id = (int) get('id');
    $reg = Crud::buscar($tabla, $id);
    if (!$reg) {
        flash('error', 'Registro no encontrado.');
        redirigir('admin/?r=' . $clave);
    }
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_exigir();
        $datos = contenido_leer_post($cfg);
        $error = contenido_validar($cfg, $datos);
        if ($error === null) {
            Crud::actualizar($tabla, $id, $datos);
            flash('exito', $cfg['titulo_singular'] . ' actualizado/a.');
            redirigir('admin/?r=' . $clave);
        }
        $reg = array_merge($reg, $datos); // conservar lo editado si hay error
    }

    render_admin('contenido/form', [
        'clave' => $clave, 'cfg' => $cfg, 'reg' => $reg, 'error' => $error, 'modo' => 'editar',
    ], 'Editar: ' . $cfg['titulo_singular']);
}

/** Baja genérica (POST). */
function contenido_eliminar(string $clave): void
{
    csrf_exigir();
    $cfg = entidad_cfg($clave);
    $tabla = entidad_tabla($clave, $cfg);
    Crud::eliminar($tabla, (int) post('id'));
    flash('exito', $cfg['titulo_singular'] . ' eliminado/a.');
    redirigir('admin/?r=' . $clave);
}

/** Validación de campos requeridos según la config. Devuelve mensaje o null. */
function contenido_validar(array $cfg, array $datos): ?string
{
    foreach ($cfg['campos'] as $campo) {
        if (!empty($campo['requerido'])) {
            $v = $datos[$campo['nombre']] ?? '';
            if (is_string($v) ? trim($v) === '' : empty($v)) {
                return 'El campo "' . $campo['etiqueta'] . '" es obligatorio.';
            }
        }
    }
    return null;
}
