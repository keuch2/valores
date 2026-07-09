<?php /** Detalle de servicio. Recibe $s. El campo `contenido` trae el markup
 * completo de las secciones del servicio (mismo diseño que el sitio original). */ ?>
<section class="hero-inner has-photo" style="background-image:url('<?= e(url('assets/img/hero-servicios.webp')) ?>')">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <a href="<?= e(url('servicios')) ?>">Servicios</a> <span>/</span> <span class="text-white/80"><?= e($s['titulo']) ?></span></div>
    <div class="pill pill-white mb-4">Servicio</div>
    <h1><?= e($s['titulo']) ?></h1>
    <?php if (!empty($s['descripcion_corta'])): ?><p><?= e($s['descripcion_corta']) ?></p><?php endif; ?>
  </div>
</section>

<?php if (!empty($s['contenido'])): ?>
  <?= $s['contenido'] /* HTML de secciones, controlado por el CMS */ ?>
<?php else: ?>
  <section class="section"><div class="container"><p class="text-gray-txt"><?= e((string) $s['descripcion_corta']) ?></p></div></section>
<?php endif; ?>

<!-- CTA final común -->
<section class="cta-strip">
  <div class="container relative z-10 text-center">
    <h2>¿Listo para empezar a invertir?</h2>
    <p>Abrí tu cuenta y accedé al Mercado de Valores con el mejor equipo.</p>
    <div class="flex flex-wrap gap-4 justify-center">
      <a href="<?= e(url('apertura-de-cuenta')) ?>" class="btn btn-white btn-lg">Abrir mi cuenta →</a>
      <a href="<?= e(url('contacto')) ?>" class="btn btn-ghost btn-lg">Hablar con un asesor</a>
    </div>
  </div>
</section>
