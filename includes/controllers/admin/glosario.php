<?php
/** Controlador admin/glosario — delega en el motor genérico de contenido. */
declare(strict_types=1);
require_once __DIR__ . '/_contenido.php';

function accion_index(): void    { contenido_index('glosario'); }
function accion_crear(): void    { contenido_crear('glosario'); }
function accion_editar(): void   { contenido_editar('glosario'); }
function accion_eliminar(): void { contenido_eliminar('glosario'); }
