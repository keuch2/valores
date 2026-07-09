<?php
/** Controlador admin/servicios — delega en el motor genérico de contenido. */
declare(strict_types=1);
require_once __DIR__ . '/_contenido.php';

function accion_index(): void    { contenido_index('servicios'); }
function accion_crear(): void    { contenido_crear('servicios'); }
function accion_editar(): void   { contenido_editar('servicios'); }
function accion_eliminar(): void { contenido_eliminar('servicios'); }
