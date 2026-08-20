<?php /** Página servicios. Recibe $servicios. */ ?>
<section class="hero-inner has-photo" style="background-image:url('<?= e(url('assets/img/hero-servicios.webp')) ?>')">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <span class="text-white/80">Servicios</span></div>
    <h1>Servicios diseñados para<br/>cada etapa de tu estrategia</h1>
    <p>Desde la intermediación bursátil hasta la estructuración de emisiones complejas. Soluciones financieras con ética, experiencia y criterio técnico de primer nivel.</p>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($servicios as $sv): ?>
        <a href="<?= e(url('servicios/' . $sv['slug'])) ?>" class="card card-service animate-fade-up">
          <h3><?php if (!empty($sv['icono'])): ?><i class="fa-solid <?= e($sv['icono']) ?>"></i> <?php endif; ?><?= e($sv['titulo']) ?></h3>
          <p><?= e($sv['descripcion_corta']) ?></p>
          <span class="link">Ver servicio →</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
