<?php /** Nosotros. Recibe $ejecutivos. */ ?>
<section class="hero-inner has-photo" style="background-image:url('<?= e(url('assets/img/hero-nosotros.webp')) ?>')">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <span class="text-white/80">Nosotros</span></div>
    <h1>Más de 33 años construyendo<br/>el mercado de capitales paraguayo</h1>
    <p>Una firma con historia, con propósito y con la mirada siempre puesta en el futuro del mercado financiero del Paraguay.</p>
  </div>
</section>

<section class="section">
  <div class="container max-w-3xl">
    <div class="section-tag">Quiénes somos</div>
    <h2 class="section-title mt-2">Una firma con propósito, con ética y con experiencia</h2>
    <p class="text-gray-txt leading-relaxed mt-4">En Valores trabajamos todos los días para acercar el mercado de capitales a las personas y a las empresas que quieren crecer de manera sólida y sostenible.</p>
    <p class="text-gray-txt leading-relaxed mt-3">Somos agentes organizadores y estructuradores de emisiones de acciones y títulos de deuda para entidades privadas y municipales a nivel nacional, acompañando cada proyecto con seriedad, conocimiento y una mirada estratégica.</p>
    <p class="text-gray-txt leading-relaxed mt-3">Fuimos pioneros en el desarrollo de estructuras fiduciarias en Paraguay e impulsores de la figura de la titularización, marcando hitos que ayudaron a fortalecer y modernizar el mercado de valores del país.</p>
    <p class="text-gray-txt leading-relaxed mt-3">Con más de 33 años de trayectoria, diseñamos opciones de inversión a medida, entendiendo que detrás de cada inversor y cada emisor hay objetivos, desafíos y proyectos únicos.</p>
    <p class="text-gray-txt leading-relaxed mt-3">Hoy seguimos evolucionando, con una mirada puesta también en el mercado inmobiliario, con el firme propósito de facilitar soluciones financieras sólidas, transparentes y confiables.</p>
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
