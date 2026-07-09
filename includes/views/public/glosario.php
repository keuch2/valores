<?php /** Glosario financiero. Recibe $terminos. */ ?>
<section class="hero-inner">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <span class="text-white/80">Glosario</span></div>
    <h1>Glosario financiero</h1>
    <p>Los términos del mercado de capitales, explicados en lenguaje claro. Una herramienta para invertir con más confianza.</p>
  </div>
</section>
<section class="section">
  <div class="container max-w-3xl">
    <?php if (empty($terminos)): ?>
      <div class="card text-center p-10"><p class="text-gray-txt">El glosario estará disponible pronto.</p></div>
    <?php else: ?>
      <?php foreach ($terminos as $t): ?>
        <div class="faq-item">
          <button class="faq-trigger"><?= e($t['termino']) ?> <svg class="w-5 h-5 chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
          <div class="faq-content"><?= e($t['definicion']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
