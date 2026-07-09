<?php /** Oportunidades dinámicas. Recibe $oportunidades, $tipoActivo. */ ?>
<section class="hero-inner">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <a href="<?= e(url('oportunidades')) ?>">Invierte</a> <span>/</span> <span class="text-white/80">Oportunidades</span></div>
    <h1>Tablero de Oportunidades</h1>
    <p>Instrumentos activos en el Mercado de Valores paraguayo disponibles para inversión. Filtrá por tipo, moneda o calificación y encontrá la opción ideal para tu perfil.</p>
  </div>
</section>
<section class="section">
  <div class="container">
    <!-- Filtros -->
    <div class="filter-bar mb-6 flex flex-wrap gap-2">
      <?php
      $filtros = ['' => 'Todas', 'bono' => 'Bonos', 'cda' => 'CDAs', 'accion' => 'Acciones', 'inter' => 'Internacional'];
      foreach ($filtros as $f => $lbl):
        $activo = ((string) ($tipoActivo ?? '')) === $f ? ' active' : '';
        $href = $f === '' ? url('oportunidades') : url('oportunidades?tipo=' . $f);
      ?>
        <a href="<?= e($href) ?>" class="filter-btn<?= $activo ?>"><?= e($lbl) ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($oportunidades)): ?>
      <div class="card text-center p-10"><p class="text-gray-txt">No hay oportunidades disponibles en esta categoría por el momento.</p></div>
    <?php else: ?>
      <?php require APP_ROOT . '/includes/views/public/_tabla_oportunidades.php'; ?>
    <?php endif; ?>

    <div class="mt-8 p-5 bg-yellow-50 rounded-xl border border-yellow-200 text-left">
      <div class="flex gap-3">
        <span class="text-xl flex-shrink-0">ℹ️</span>
        <div class="text-sm text-yellow-800">
          <strong>Información orientativa.</strong> Las tasas, condiciones y disponibilidad pueden variar. Para condiciones exactas y actuales, consultá con un asesor de Valores.
        </div>
      </div>
    </div>
  </div>
</section>
