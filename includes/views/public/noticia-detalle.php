<?php /** Detalle de noticia. Recibe $n (fila de `noticias`). */
$cat = ['mercado'=>'Mercado de Capitales','macro'=>'Macroeconomía','inter'=>'Internacional','empresa'=>'Empresas','regulacion'=>'Regulación','negocios'=>'Negocios','inversiones'=>'Inversiones','summit'=>'Summit Forbes'];
$img = img_url((int) ($n['imagen_destacada_id'] ?? 0));
?>
<section class="hero-inner">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <a href="<?= e(url('noticias')) ?>">Noticias</a> <span>/</span> <span class="text-white/80"><?= e($cat[$n['categoria']] ?? 'Noticia') ?></span></div>
    <?php if (!empty($n['categoria'])): ?><div class="pill pill-white mb-4"><?= e($cat[$n['categoria']] ?? $n['categoria']) ?></div><?php endif; ?>
    <h1><?= e($n['titulo']) ?></h1>
    <p class="text-white/70 text-sm">
      <?php if (!empty($n['fecha_publicacion'])): ?><?= e(date('d/m/Y', strtotime((string) $n['fecha_publicacion']))) ?><?php endif; ?>
      <?php if (!empty($n['autor'])): ?> · <?= e($n['autor']) ?><?php endif; ?>
    </p>
  </div>
</section>

<section class="section">
  <div class="container">
    <article class="max-w-3xl mx-auto">
      <?php if ($img): ?><img src="<?= e($img) ?>" alt="<?= e($n['titulo']) ?>" class="w-full rounded-2xl mb-8" style="max-height:480px;object-fit:cover"><?php endif; ?>
      <?php if (!empty($n['resumen'])): ?><p class="text-lg text-blue-inst font-semibold leading-relaxed mb-6"><?= e($n['resumen']) ?></p><?php endif; ?>
      <div class="prose">
        <?php /* HTML del editor enriquecido del panel (contenido de confianza, autoría admin). */ ?>
        <?= (string) $n['contenido'] ?>
      </div>
      <div class="mt-10 pt-6 border-t border-gray-ui">
        <a href="<?= e(url('noticias')) ?>" class="btn btn-secondary">← Volver a Noticias</a>
      </div>
    </article>
  </div>
</section>
