<?php
/** Controlador admin/ejecutivos — delega en el motor genérico de contenido. */
declare(strict_types=1);
require_once __DIR__ . '/_contenido.php';

function accion_index(): void    { contenido_index('ejecutivos'); }
function accion_crear(): void    { contenido_crear('ejecutivos'); }
function accion_editar(): void   { contenido_editar('ejecutivos'); }
function accion_eliminar(): void { contenido_eliminar('ejecutivos'); }
