<?php /** Oportunidades de inversión: texto + descarga del reporte diario. Recibe $pdf (fila media o null). */ ?>
<section class="hero-inner">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <span class="text-white/80">Oportunidades</span></div>
    <h1>Oportunidades de Inversión</h1>
    <p>¿Querés que tu dinero trabaje mejor? Descubrí las oportunidades del mercado, actualizadas hoy.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:760px">
    <div class="card p-10 text-center animate-fade-up">
      <p class="text-gray-txt leading-relaxed">El mercado de capitales cambia todos los días, y con él, las oportunidades para hacer crecer tu patrimonio. En Valores Casa de Bolsa seleccionamos y actualizamos diariamente las alternativas de inversión disponibles —bonos, CDAs y otros instrumentos— para que cuentes con información clara, precisa y oportuna al momento de decidir.</p>
      <p class="text-gray-txt leading-relaxed mt-4">Descargá el reporte de oportunidades de hoy y descubrí qué alternativas tenemos disponibles para vos.</p>
      <?php if ($pdf): ?>
        <div class="mt-8">
          <a href="<?= e(Media::urlPublica($pdf)) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg"><i class="fa-solid fa-file-pdf"></i>&nbsp; Descargar oportunidades de hoy</a>
        </div>
        <p class="text-xs text-gray-txt mt-4">Actualizado el <?= e(date('d/m/Y', strtotime((string) $pdf['created_at']))) ?></p>
      <?php else: ?>
        <p class="text-sm text-gray-txt mt-8">El reporte de hoy estará disponible en breve. Mientras tanto, <a href="<?= e(url('contacto')) ?>" class="text-celeste font-semibold hover:underline">consultá con un asesor</a>.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
