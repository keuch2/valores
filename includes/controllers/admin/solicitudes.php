<?php
/**
 * Controlador admin/solicitudes — sección exclusiva de apertura de cuenta.
 * Datos sensibles: cada visualización de detalle se registra en auditoría.
 */

declare(strict_types=1);

require_once APP_ROOT . '/includes/apertura/pasos.php';

/** Listado con filtros. Ruta: admin/?r=solicitudes */
function accion_index(): void
{
    $filtros = [
        'estado' => get('estado'),
        'tipo'   => get('tipo'),
        'agente' => get('agente'),
        'q'      => get('q'),
    ];
    render_admin('solicitudes/index', [
        'solicitudes' => Solicitud::listar($filtros),
        'agentes'     => db()->query('SELECT id, nombre FROM agentes ORDER BY nombre')->fetchAll(),
        'filtros'     => $filtros,
    ], 'Solicitudes de apertura');
}

/** Detalle (descifra el JSON y audita el acceso). Ruta: admin/?r=solicitudes/ver&id=N */
function accion_ver(): void
{
    $id = (int) get('id');
    $sol = Solicitud::buscarDescifrada($id);
    if (!$sol) {
        flash('error', 'Solicitud no encontrada.');
        redirigir('admin/?r=solicitudes');
    }
    Solicitud::auditar(auth_usuario()['id'], 'ver', $id);

    $tipo = $sol['tipo_persona'] === 'juridica' ? 'juridica' : 'fisica';
    render_admin('solicitudes/detalle', [
        'sol'       => $sol,
        'etiquetas' => apertura_etiquetas($tipo),
        'agentes'   => db()->query('SELECT id, nombre FROM agentes WHERE activo = 1 ORDER BY nombre')->fetchAll(),
    ], 'Solicitud #' . $id);
}

/** Cambiar estado (POST). Ruta: admin/?r=solicitudes/estado */
function accion_estado(): void
{
    csrf_exigir();
    $id = (int) post('id');
    if (Solicitud::cambiarEstado($id, post('estado'), auth_usuario()['id'])) {
        flash('exito', 'Estado actualizado.');
    } else {
        flash('error', 'Estado inválido.');
    }
    redirigir('admin/?r=solicitudes/ver&id=' . $id);
}

/** Reasignar agente (POST). Ruta: admin/?r=solicitudes/reasignar */
function accion_reasignar(): void
{
    csrf_exigir();
    $id = (int) post('id');
    Solicitud::reasignar($id, (int) post('agente_id'), auth_usuario()['id']);
    flash('exito', 'Agente reasignado.');
    redirigir('admin/?r=solicitudes/ver&id=' . $id);
}

/**
 * Sirve la imagen de firma con verificación de sesión admin (nunca por URL pública).
 * Ruta: admin/?r=solicitudes/firma&id=N   (id = id de la solicitud)
 */
function accion_firma(): void
{
    $id = (int) get('id');
    $sol = Solicitud::buscarDescifrada($id);
    if (!$sol || empty($sol['firma_media_id'])) {
        http_response_code(404);
        exit('Firma no encontrada.');
    }
    $m = Media::buscar((int) $sol['firma_media_id']);
    $ruta = $m ? Media::rutaFirmaEnDisco($m) : null;
    if (!$ruta) {
        http_response_code(404);
        exit('Archivo no disponible.');
    }
    Solicitud::auditar(auth_usuario()['id'], 'ver_firma', $id);
    header('Content-Type: ' . $m['mime_type']);
    header('Content-Disposition: inline; filename="firma-solicitud-' . $id . '"');
    header('Cache-Control: private, no-store');
    readfile($ruta);
    exit;
}
