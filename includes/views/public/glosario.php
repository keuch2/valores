<?php /** Glosario financiero. Recibe $terminos. */ ?>
<section class="hero-inner" style="background:var(--color-blue-inst);color:#fff">
  <div class="container py-14">
    <div class="section-tag" style="color:var(--color-naranja)">Herramientas</div>
    <h1 class="text-3xl md:text-4xl font-bold mt-2">Glosario financiero</h1>
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
