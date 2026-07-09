<?php
/**
 * Controlador admin/dashboard — resumen del panel.
 */

declare(strict_types=1);

function accion_index(): void
{
    $pdo = db();

    $solicitudesNuevas = (int) $pdo->query(
        "SELECT COUNT(*) FROM solicitudes_apertura WHERE estado = 'nueva'"
    )->fetchColumn();

    $totalSolicitudes = (int) $pdo->query("SELECT COUNT(*) FROM solicitudes_apertura")->fetchColumn();
    $oportActivas     = (int) $pdo->query("SELECT COUNT(*) FROM oportunidades WHERE estado = 'disponible'")->fetchColumn();
    $noticiasPub      = (int) $pdo->query("SELECT COUNT(*) FROM noticias WHERE estado = 'publicado'")->fetchColumn();

    $ultimasNoticias = $pdo->query(
        "SELECT id, titulo, estado, fecha_publicacion
         FROM noticias ORDER BY created_at DESC LIMIT 5"
    )->fetchAll();

    render_admin('dashboard/index', [
        'solicitudesNuevas' => $solicitudesNuevas,
        'totalSolicitudes'  => $totalSolicitudes,
        'oportActivas'      => $oportActivas,
        'noticiasPub'       => $noticiasPub,
        'ultimasNoticias'   => $ultimasNoticias,
    ], 'Dashboard');
}
