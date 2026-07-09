<?php
/**
 * Controlador admin/media — biblioteca de medios.
 */

declare(strict_types=1);

/** Grilla principal. Ruta: admin/?r=media */
function accion_index(): void
{
    $tipo = get('tipo') ?: null;
    $q    = get('q');
    render_admin('media/index', [
        'medios' => Media::listar($tipo, $q),
        'tipo'   => $tipo,
        'q'      => $q,
    ], 'Biblioteca de medios');
}

/** Subida de archivo (POST multipart). Ruta: admin/?r=media/subir */
function accion_subir(): void
{
    csrf_exigir();
    if (empty($_FILES['archivo'])) {
        flash('error', 'No se recibió ningún archivo.');
        redirigir('admin/?r=media');
    }
    $r = Media::subir($_FILES['archivo'], auth_usuario()['id']);
    flash($r['ok'] ? 'exito' : 'error', $r['ok'] ? 'Archivo subido.' : $r['error']);
    redirigir('admin/?r=media');
}

/** Registrar video por URL externa. Ruta: admin/?r=media/video */
function accion_video(): void
{
    csrf_exigir();
    $r = Media::agregarVideoUrl(post('video_url'), post('titulo'), auth_usuario()['id']);
    flash($r['ok'] ? 'exito' : 'error', $r['ok'] ? 'Video agregado.' : $r['error']);
    redirigir('admin/?r=media');
}

/** Editar alt_text (POST). Ruta: admin/?r=media/alt */
function accion_alt(): void
{
    csrf_exigir();
    Media::actualizarAlt((int) post('id'), post('alt_text'));
    flash('exito', 'Texto alternativo actualizado.');
    redirigir('admin/?r=media');
}

/** Eliminar (POST). Ruta: admin/?r=media/eliminar */
function accion_eliminar(): void
{
    csrf_exigir();
    $r = Media::eliminar((int) post('id'));
    flash($r['ok'] ? 'exito' : 'error', $r['ok'] ? 'Medio eliminado.' : $r['error']);
    redirigir('admin/?r=media');
}

/**
 * Endpoint JSON para el modal selector reutilizable.
 * Ruta: admin/?r=media/api  (GET, opcional &tipo= &q=)
 */
function accion_api(): void
{
    $tipo = get('tipo') ?: null;
    $q    = get('q');
    $items = array_map(static function (array $m): array {
        return [
            'id'    => (int) $m['id'],
            'tipo'  => $m['tipo'],
            'nombre'=> $m['nombre_original'],
            'alt'   => $m['alt_text'],
            'url'   => Media::urlPublica($m),
        ];
    }, Media::listar($tipo, $q));
    json_respuesta(['ok' => true, 'items' => $items]);
}
