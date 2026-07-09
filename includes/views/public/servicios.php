<?php /** Página servicios. Recibe $servicios. */ ?>
<section class="hero-inner" style="background:var(--color-blue-inst);color:#fff">
  <div class="container py-14">
    <div class="section-tag" style="color:var(--color-naranja)">Servicios</div>
    <h1 class="text-3xl md:text-4xl font-bold mt-2">Todo lo que Valores puede hacer por vos</h1>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($servicios as $sv): ?>
        <a href="<?= e(url('servicios/' . $sv['slug'])) ?>" class="card card-service animate-fade-up">
          <?php $img = img_url((int) ($sv['imagen_id'] ?? 0)); if ($img): ?>
            <div class="srv-thumb"><img src="<?= e($img) ?>" alt="<?= e($sv['titulo']) ?>" loading="lazy"></div>
          <?php endif; ?>
          <h3><?php if (!empty($sv['icono'])): ?><i class="fa-solid <?= e($sv['icono']) ?>"></i> <?php endif; ?><?= e($sv['titulo']) ?></h3>
          <p><?= e($sv['descripcion_corta']) ?></p>
          <span class="link">Ver servicio →</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
