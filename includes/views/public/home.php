<?php /** Home. Recibe $servicios, $oportunidades, $tasas. */ ?>
<section class="hero-main">
  <div class="container relative z-10 py-24 lg:py-32">
    <div class="max-w-2xl">
      <div class="hero-eyebrow animate-fade-up">
        <span></span> Más de 30 años en el Mercado de Valores del Paraguay
      </div>
      <h1 class="animate-fade-up animate-delay-1">
        Desde 1993 <em>impulsando inversiones</em> en el Paraguay
      </h1>
      <p class="animate-fade-up animate-delay-2">
        Somos la Casa de Bolsa con mayor trayectoria en Paraguay. Te acompañamos en cada decisión de inversión con ética, experiencia y herramientas de primer nivel.
      </p>
      <div class="flex flex-wrap gap-4 animate-fade-up animate-delay-3">
        <a href="<?= e(url('apertura-de-cuenta')) ?>" class="btn btn-primary btn-lg">Quiero invertir →</a>
        <a href="<?= e(url('oportunidades')) ?>" class="btn btn-ghost btn-lg">Ver mis opciones</a>
      </div>
      <div class="flex flex-wrap gap-6 mt-10 animate-fade-up animate-delay-4">
        <div class="flex items-center gap-2 text-white/70 text-sm">
          <span class="w-2 h-2 rounded-full bg-celeste block"></span> Sin costos de apertura
        </div>
        <div class="flex items-center gap-2 text-white/70 text-sm">
          <span class="w-2 h-2 rounded-full bg-celeste block"></span> Asesoramiento personalizado
        </div>
        <div class="flex items-center gap-2 text-white/70 text-sm">
          <span class="w-2 h-2 rounded-full bg-celeste block"></span> Regulado por la SIV (BCP)
        </div>
      </div>
    </div>
  </div>
  <!-- Abstract graphic -->
  <div class="hero-graphic" aria-hidden="true">
    <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="200" cy="200" r="180" stroke="white" stroke-width="0.5"/>
      <circle cx="200" cy="200" r="140" stroke="white" stroke-width="0.5"/>
      <circle cx="200" cy="200" r="100" stroke="white" stroke-width="0.5"/>
      <path d="M60 280 L120 220 L160 250 L200 180 L240 210 L280 160 L340 120" stroke="white" stroke-width="1.5" fill="none"/>
      <circle cx="120" cy="220" r="4" fill="white"/>
      <circle cx="200" cy="180" r="4" fill="white"/>
      <circle cx="280" cy="160" r="4" fill="white"/>
      <circle cx="340" cy="120" r="6" fill="#2E6B96"/>
    </svg>
  </div>
</section>

<!-- Servicios (dinámico) -->
<section class="section">
  <div class="container">
    <div class="text-center mb-12">
      <div class="section-tag">Qué hacemos</div>
      <h2 class="section-title mt-2">Nuestros servicios</h2>
    </div>
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

<!-- Simulador (tasas desde BD) -->
<section class="section bg-gray" id="simulador">
  <div class="container">
    <div class="simulator-wrap">
      <div class="simulator-header">
        <div class="flex items-center gap-3">
          <div>
            <h2>Simulador de Inversión</h2>
            <p>Resultados orientativos. Las tasas reales pueden variar según el instrumento y el mercado.</p>
          </div>
        </div>
      </div>
      <div class="simulator-body">
        <form id="simulator-form">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="form-group">
              <label class="form-label">Moneda</label>
              <select id="sim-moneda" class="form-select">
                <option value="USD">Dólares (USD)</option>
                <option value="PYG">Guaraníes (PYG)</option>
              </select>
            </div>
            <div class="form-group md:col-span-2">
              <label class="form-label">Capital a invertir</label>
              <input id="sim-capital" type="number" class="form-input" placeholder="Ej: 10,000" min="1" step="any"/>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="form-group">
              <label class="form-label">Producto</label>
              <select id="sim-producto" class="form-select">
                <option value="bono">Bono Corporativo</option>
                <option value="cda">CDA</option>
                <option value="accion">Acciones</option>
                <option value="inter">Mercado Internacional</option>
                <option value="letra">Letra del Tesoro</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Plazo (meses)</label>
              <select id="sim-plazo" class="form-select">
                <option value="3">3 meses</option><option value="6">6 meses</option>
                <option value="12" selected>12 meses</option><option value="24">24 meses</option>
                <option value="36">36 meses</option><option value="60">60 meses</option>
              </select>
            </div>
            <div class="form-group flex items-end">
              <button type="submit" class="btn btn-primary w-full">Calcular →</button>
            </div>
          </div>
        </form>
        <div id="simulator-result" class="simulator-result">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-full bg-celeste-soft flex items-center justify-center text-celeste font-bold">✓</div>
            <h4 class="font-bold text-blue-inst">Resultado estimado</h4>
            <span class="ml-auto text-xs text-gray-txt italic">Tasa referencial — valores orientativos</span>
          </div>
          <div class="grid md:grid-cols-2 gap-0">
            <div>
              <div class="result-row"><span class="label">Producto</span><span class="value" id="res-producto">—</span></div>
              <div class="result-row"><span class="label">Capital invertido</span><span class="value" id="res-capital">—</span></div>
              <div class="result-row"><span class="label">Tasa anual</span><span class="value" id="res-tasa">—</span></div>
            </div>
            <div>
              <div class="result-row"><span class="label">Plazo</span><span class="value" id="res-plazo">—</span></div>
              <div class="result-row"><span class="label">Interés estimado</span><span class="value text-green-600" id="res-interes">—</span></div>
              <div class="result-row highlight"><span class="label font-bold">Total estimado</span><span class="value" id="res-total">—</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Oportunidades destacadas (dinámico) -->
<?php $destacadas = array_slice($oportunidades, 0, 4); if ($destacadas): ?>
<section class="section">
  <div class="container">
    <div class="text-center mb-10">
      <div class="section-tag">Tablero</div>
      <h2 class="section-title mt-2">Oportunidades de inversión</h2>
    </div>
    <?php require APP_ROOT . '/includes/views/public/_tabla_oportunidades.php'; ?>
    <div class="text-center mt-6"><a href="<?= e(url('oportunidades')) ?>" class="btn btn-secondary">Ver todas →</a></div>
  </div>
</section>
<?php endif; ?>

<!-- Las tasas del simulador vienen de la BD (config) -->
<script>window.SIM_TASAS = <?= json_encode($tasas) ?>;</script>
