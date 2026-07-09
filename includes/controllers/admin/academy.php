<?php
/** Controlador admin/academy — delega en el motor genérico de contenido. */
declare(strict_types=1);
require_once __DIR__ . '/_contenido.php';

function accion_index(): void    { contenido_index('academy'); }
function accion_crear(): void    { contenido_crear('academy'); }
function accion_editar(): void   { contenido_editar('academy'); }
function accion_eliminar(): void { contenido_eliminar('academy'); }
