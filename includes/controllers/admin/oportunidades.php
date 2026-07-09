<?php
/** Controlador admin/oportunidades — delega en el motor genérico de contenido. */
declare(strict_types=1);
require_once __DIR__ . '/_contenido.php';

function accion_index(): void    { contenido_index('oportunidades'); }
function accion_crear(): void    { contenido_crear('oportunidades'); }
function accion_editar(): void   { contenido_editar('oportunidades'); }
function accion_eliminar(): void { contenido_eliminar('oportunidades'); }
