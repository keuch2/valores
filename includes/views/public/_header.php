<?php
/** Header público común. Usa $meta (title/desc/activo) y $GLOBALS['_sitio']. */
$sitio = $GLOBALS['_sitio'];
$act = $meta['activo'] ?? '';
/** Helper local: clase activa del nav. */
$na = fn(string $k) => $act === $k ? ' active' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= e($meta['title']) ?></title>
  <meta name="description" content="<?= e($meta['desc'] ?? '') ?>"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= e(url('assets/css/styles.css')) ?>"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer"/>
  <script>
    tailwind.config = {
      theme: { extend: {
        colors: {
          'blue-inst': '#0B3C5D', 'blue-fin': '#2E6B96', 'celeste': '#2E6B96',
          'celeste-soft':'#E8EEF3', 'naranja': '#F5A300',
          'gray-bg': '#F5F7FA', 'gray-ui': '#D9E1E8', 'gray-txt': '#6B7C8F',
        },
        fontFamily: { quicksand: ['Quicksand','sans-serif'] }
      } }
    }
  </script>
</head>
<body>

<div class="topbar py-2">
  <div class="container flex justify-between items-center">
    <span class="text-xs hidden md:block" style="color:var(--color-blue-inst);opacity:0.85"><?= e($sitio['contacto_direccion']) ?></span>
    <div class="flex items-center gap-4 ml-auto">
      <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $sitio['contacto_telefono'])) ?>" class="text-xs flex items-center gap-1 transition-colors font-semibold" style="color:var(--color-blue-inst)"><?= e($sitio['contacto_telefono']) ?></a>
      <a href="#" class="text-xs font-semibold transition-colors" style="color:var(--color-blue-inst)">Mi Cuenta →</a>
    </div>
  </div>
</div>

<nav id="main-navbar" class="navbar">
  <div class="container flex items-center justify-between h-24">
    <a href="<?= e(url('')) ?>" class="navbar-logo">
      <img src="<?= e(url('assets/img/logo.png')) ?>" alt="Valores Casa de Bolsa" class="h-8 w-auto">
    </a>

    <div class="hidden lg:flex items-center gap-1">
      <a href="<?= e(url('')) ?>" class="nav-link<?= $na('inicio') ?>">Inicio</a>

      <div class="nav-item">
        <button class="nav-link">Servicios <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="dropdown-menu mega">
          <?php foreach (Publico::servicios() as $sv): ?>
            <a href="<?= e(url('servicios/' . $sv['slug'])) ?>" class="dropdown-item"><div><strong><?= e($sv['titulo']) ?></strong><small><?= e(mb_strimwidth((string) $sv['descripcion_corta'], 0, 40, '…')) ?></small></div></a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="nav-item">
        <button class="nav-link">Invierte <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="dropdown-menu mega">
          <a href="<?= e(url('oportunidades?tipo=bono')) ?>" class="dropdown-item"><div><strong>Bonos</strong><small>Deuda corporativa y municipal</small></div></a>
          <a href="<?= e(url('oportunidades?tipo=cda')) ?>" class="dropdown-item"><div><strong>CDAs</strong><small>Certificados de depósito</small></div></a>
          <a href="<?= e(url('oportunidades?tipo=accion')) ?>" class="dropdown-item"><div><strong>Acciones</strong><small>Participación en empresas</small></div></a>
          <a href="<?= e(url('oportunidades?tipo=inter')) ?>" class="dropdown-item"><div><strong>Mercado Internacional</strong><small>Exposición global</small></div></a>
          <a href="<?= e(url('oportunidades')) ?>" class="dropdown-item"><div><strong>Oportunidades</strong><small>Tablero de inversión</small></div></a>
        </div>
      </div>

      <div class="nav-item">
        <button class="nav-link">Herramientas <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="dropdown-menu">
          <a href="<?= e(url('#simulador')) ?>" class="dropdown-item"><div><strong>Simulador</strong><small>Calcula tu rentabilidad</small></div></a>
          <a href="<?= e(url('glosario')) ?>" class="dropdown-item"><div><strong>Glosario</strong><small>Términos financieros</small></div></a>
        </div>
      </div>

      <div class="nav-item">
        <button class="nav-link">Nosotros <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="dropdown-menu">
          <a href="<?= e(url('nosotros')) ?>" class="dropdown-item"><div><strong>Quiénes Somos</strong><small>Historia y trayectoria</small></div></a>
          <a href="<?= e(url('nosotros#directiva')) ?>" class="dropdown-item"><div><strong>Plana Directiva</strong><small>Nuestro equipo</small></div></a>
        </div>
      </div>

      <a href="<?= e(url('contacto')) ?>" class="nav-link<?= $na('contacto') ?>">Contacto</a>
    </div>

    <div class="hidden lg:flex items-center gap-3">
      <a href="<?= e(url('apertura-de-cuenta')) ?>" class="btn btn-primary btn-sm">Abrir Cuenta</a>
    </div>

    <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition-colors">
      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>
</nav>

<div id="mobile-menu">
  <div class="flex justify-between items-center mb-6">
    <a href="<?= e(url('')) ?>" class="navbar-logo"><img src="<?= e(url('assets/img/logo.png')) ?>" alt="Valores Casa de Bolsa" class="h-8 w-auto"></a>
    <button id="mobile-menu-close" class="p-2 rounded-lg hover:bg-gray-bg">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  <div class="flex flex-col gap-1">
    <a href="<?= e(url('')) ?>" class="mobile-nav-link">Inicio</a>
    <div>
      <button class="mobile-dropdown-toggle">Servicios <svg class="w-4 h-4 m-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
      <div class="mobile-dropdown-content">
        <?php foreach (Publico::servicios() as $sv): ?>
          <a href="<?= e(url('servicios/' . $sv['slug'])) ?>"><?= e($sv['titulo']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div>
      <button class="mobile-dropdown-toggle">Invierte <svg class="w-4 h-4 m-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
      <div class="mobile-dropdown-content">
        <a href="<?= e(url('oportunidades?tipo=bono')) ?>">Bonos</a>
        <a href="<?= e(url('oportunidades?tipo=cda')) ?>">CDAs</a>
        <a href="<?= e(url('oportunidades?tipo=accion')) ?>">Acciones</a>
        <a href="<?= e(url('oportunidades')) ?>">Oportunidades</a>
      </div>
    </div>
    <a href="<?= e(url('nosotros')) ?>" class="mobile-nav-link">Nosotros</a>
    <a href="<?= e(url('contacto')) ?>" class="mobile-nav-link">Contacto</a>
    <div class="mt-6 flex flex-col gap-3">
      <a href="<?= e(url('apertura-de-cuenta')) ?>" class="btn btn-primary text-center">Abrir mi Cuenta</a>
    </div>
  </div>
</div>
