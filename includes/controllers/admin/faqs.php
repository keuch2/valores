<?php
/** Controlador admin/faqs — delega en el motor genérico de contenido. */
declare(strict_types=1);
require_once __DIR__ . '/_contenido.php';

function accion_index(): void    { contenido_index('faqs'); }
function accion_crear(): void    { contenido_crear('faqs'); }
function accion_editar(): void   { contenido_editar('faqs'); }
function accion_eliminar(): void { contenido_eliminar('faqs'); }
