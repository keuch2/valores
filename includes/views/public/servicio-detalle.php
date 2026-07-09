<?php /** Detalle de servicio. Recibe $s. */ ?>
<section class="hero-inner has-photo" style="background-image:url('<?= e(url('assets/img/hero-servicios.webp')) ?>')">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <a href="<?= e(url('servicios')) ?>">Servicios</a> <span>/</span> <span class="text-white/80"><?= e($s['titulo']) ?></span></div>
    <h1><?php if (!empty($s['icono'])): ?><i class="fa-solid <?= e($s['icono']) ?>"></i> <?php endif; ?><?= e($s['titulo']) ?></h1>
    <?php if (!empty($s['descripcion_corta'])): ?><p><?= e($s['descripcion_corta']) ?></p><?php endif; ?>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="grid lg:grid-cols-<?= !empty($s['imagen_id']) ? '2' : '1' ?> gap-16 items-center">
      <div class="animate-fade-up prose max-w-none">
        <?= $s['contenido'] ?: '<p class="text-gray-txt">' . e((string) $s['descripcion_corta']) . '</p>' ?>
        <div class="mt-8"><a href="<?= e(url('apertura-de-cuenta')) ?>" class="btn btn-primary">Abrir mi cuenta</a></div>
      </div>
      <?php $img = img_url((int) ($s['imagen_id'] ?? 0)); if ($img): ?>
        <div class="animate-fade-up animate-delay-2"><div class="srv-img-blob"><img src="<?= e($img) ?>" alt="<?= e($s['titulo']) ?>"></div></div>
      <?php endif; ?>
    </div>
  </div>
</section>
