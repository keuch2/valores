<?php /** Noticias: listado paginado. Recibe $noticias, $pagina, $paginas. */
$cat = ['mercado'=>'Mercado Local','macro'=>'Macroeconomía','inter'=>'Internacional','empresa'=>'Empresas','regulacion'=>'Regulación'];
?>
<section class="hero-inner">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <span class="text-white/80">Noticias</span></div>
    <h1>Noticias y Análisis<br/>del Mercado</h1>
    <p>Mantenete informado con las últimas novedades del mercado de capitales paraguayo e internacional, análisis de coyuntura y oportunidades identificadas por nuestro equipo.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (empty($noticias)): ?>
      <div class="card text-center p-10"><p class="text-gray-txt">Todavía no hay noticias publicadas. Volvé pronto.</p></div>
    <?php else: ?>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($noticias as $n): $img = img_url((int) ($n['imagen_destacada_id'] ?? 0)); ?>
          <a href="<?= e(url('noticias/' . $n['slug'])) ?>" class="card card-service animate-fade-up flex flex-col">
            <?php if ($img): ?><div class="srv-thumb"><img src="<?= e($img) ?>" alt="<?= e($n['titulo']) ?>" loading="lazy"></div><?php endif; ?>
            <div class="flex items-center gap-3 text-xs mb-3">
              <?php if (!empty($n['categoria'])): ?><span class="pill pill-blue"><?= e($cat[$n['categoria']] ?? $n['categoria']) ?></span><?php endif; ?>
              <?php if (!empty($n['fecha_publicacion'])): ?><span class="text-gray-txt"><?= e(date('d/m/Y', strtotime((string) $n['fecha_publicacion']))) ?></span><?php endif; ?>
            </div>
            <h3><?= e($n['titulo']) ?></h3>
            <?php if (!empty($n['resumen'])): ?><p><?= e($n['resumen']) ?></p><?php endif; ?>
            <span class="link mt-auto">Leer más →</span>
          </a>
        <?php endforeach; ?>
      </div>
      <?php if ($paginas > 1): ?>
        <div class="flex justify-center gap-2 mt-10">
          <?php for ($p = 1; $p <= $paginas; $p++): ?>
            <a href="<?= e(url('noticias' . ($p > 1 ? '?p=' . $p : ''))) ?>" class="filter-btn<?= $p === $pagina ? ' active' : '' ?>"><?= $p ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
