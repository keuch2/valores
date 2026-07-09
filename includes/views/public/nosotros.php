<?php /** Nosotros. Recibe $ejecutivos. */ ?>
<section class="hero-inner" style="background:var(--color-blue-inst);color:#fff">
  <div class="container py-14">
    <div class="section-tag" style="color:var(--color-naranja)">Nosotros</div>
    <h1 class="text-3xl md:text-4xl font-bold mt-2">+33 años construyendo el mercado de capitales</h1>
  </div>
</section>

<section class="section">
  <div class="container max-w-3xl">
    <div class="section-tag">Quiénes somos</div>
    <h2 class="section-title mt-2">La Casa de Bolsa con mayor trayectoria del Paraguay</h2>
    <p class="text-gray-txt leading-relaxed mt-4">Valores Casa de Bolsa S.A. acompaña a inversores e instituciones en el mercado de capitales paraguayo con más de tres décadas de experiencia, regulada por la Comisión Nacional de Valores.</p>
  </div>
</section>

<section class="section bg-gray" id="directiva">
  <div class="container">
    <div class="text-center mb-12">
      <div class="section-tag">Equipo</div>
      <h2 class="section-title mt-2">Plana directiva</h2>
    </div>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($ejecutivos as $ej): ?>
        <div class="card-exec animate-fade-up">
          <div class="exec-photo">
            <?php $foto = img_url((int) ($ej['foto_id'] ?? 0)); if ($foto): ?>
              <img src="<?= e($foto) ?>" alt="<?= e($ej['nombre']) ?>" style="width:100%;height:100%;object-fit:cover">
            <?php endif; ?>
          </div>
          <div class="exec-body">
            <?php if (!empty($ej['cargo'])): ?><div class="role mb-2"><?= e($ej['cargo']) ?></div><?php endif; ?>
            <h3><?= e($ej['nombre']) ?></h3>
            <?php if (!empty($ej['bio'])): ?><p class="text-xs text-gray-txt mt-3 leading-relaxed"><?= e($ej['bio']) ?></p><?php endif; ?>
            <div class="flex gap-2 mt-4">
              <?php if (!empty($ej['linkedin'])): ?><a href="<?= e($ej['linkedin']) ?>" class="w-7 h-7 rounded-lg bg-celeste-soft flex items-center justify-center text-blue-fin text-xs font-bold hover:bg-blue-fin hover:text-white transition-colors">in</a><?php endif; ?>
              <?php if (!empty($ej['email'])): ?><a href="mailto:<?= e($ej['email']) ?>" class="w-7 h-7 rounded-lg bg-celeste-soft flex items-center justify-center text-blue-fin text-xs font-bold hover:bg-blue-fin hover:text-white transition-colors" title="Email">@</a><?php endif; ?>
              <?php if (!empty($ej['whatsapp'])): ?><a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $ej['whatsapp'])) ?>" class="w-7 h-7 rounded-lg bg-celeste-soft flex items-center justify-center text-blue-fin text-xs font-bold hover:bg-blue-fin hover:text-white transition-colors" title="WhatsApp">wa</a><?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
