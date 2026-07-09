<?php
/** Controlador admin/noticias — delega en el motor genérico de contenido. */
declare(strict_types=1);
require_once __DIR__ . '/_contenido.php';

function accion_index(): void    { contenido_index('noticias'); }
function accion_crear(): void    { contenido_crear('noticias'); }
function accion_editar(): void   { contenido_editar('noticias'); }
function accion_eliminar(): void { contenido_eliminar('noticias'); }
