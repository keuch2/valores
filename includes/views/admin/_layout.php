<?php
/**
 * _layout.php — Estructura del panel admin (sidebar + topbar).
 * Recibe: $titulo, $contenido (HTML ya renderizado de la vista).
 */
$admin = auth_usuario();
$flashes = flash_obtener();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> · Valores Admin</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body>
<div class="admin-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <strong>Valores</strong><span>CMS</span>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= e(url('admin/?r=dashboard')) ?>" class="nav-item<?= nav_activo('dashboard') ?>">Dashboard</a>
            <p class="nav-group">Contenido</p>
            <a href="<?= e(url('admin/?r=noticias')) ?>" class="nav-item<?= nav_activo('noticias') ?>">Noticias</a>
            <a href="<?= e(url('admin/?r=oportunidades')) ?>" class="nav-item<?= nav_activo('oportunidades') ?>">Oportunidades</a>
            <a href="<?= e(url('admin/?r=servicios')) ?>" class="nav-item<?= nav_activo('servicios') ?>">Servicios</a>
            <a href="<?= e(url('admin/?r=ejecutivos')) ?>" class="nav-item<?= nav_activo('ejecutivos') ?>">Ejecutivos</a>
            <a href="<?= e(url('admin/?r=faqs')) ?>" class="nav-item<?= nav_activo('faqs') ?>">FAQ</a>
            <a href="<?= e(url('admin/?r=glosario')) ?>" class="nav-item<?= nav_activo('glosario') ?>">Glosario</a>
            <a href="<?= e(url('admin/?r=academy')) ?>" class="nav-item<?= nav_activo('academy') ?>">Academy</a>
            <a href="<?= e(url('admin/?r=media')) ?>" class="nav-item<?= nav_activo('media') ?>">Biblioteca de medios</a>
            <p class="nav-group">Apertura</p>
            <a href="<?= e(url('admin/?r=solicitudes')) ?>" class="nav-item<?= nav_activo('solicitudes') ?>">Solicitudes</a>
            <a href="<?= e(url('admin/?r=agentes')) ?>" class="nav-item<?= nav_activo('agentes') ?>">Agentes</a>
            <p class="nav-group">Sistema</p>
            <a href="<?= e(url('admin/?r=usuarios')) ?>" class="nav-item<?= nav_activo('usuarios') ?>">Usuarios admin</a>
            <a href="<?= e(url('admin/?r=configuracion')) ?>" class="nav-item<?= nav_activo('configuracion') ?>">Configuración</a>
        </nav>
    </aside>

    <main class="content">
        <header class="topbar">
            <h1 class="topbar-title"><?= e($titulo) ?></h1>
            <div class="topbar-user">
                <span><?= e($admin['nombre'] ?? '') ?></span>
                <a class="btn btn-sm btn-ghost" href="<?= e(url('admin/?r=auth/logout')) ?>">Salir</a>
            </div>
        </header>

        <?php foreach ($flashes as $tipo => $msgs): foreach ($msgs as $m): ?>
            <div class="alert alert-<?= e($tipo) ?>"><?= e($m) ?></div>
        <?php endforeach; endforeach; ?>

        <div class="content-body">
            <?= $contenido /* HTML ya escapado en la vista */ ?>
        </div>
    </main>
</div>
<script>window.ADMIN_BASE = <?= json_encode(BASE_URL) ?>;</script>
<script src="<?= e(url('assets/js/admin.js')) ?>"></script>
</body>
</html>
