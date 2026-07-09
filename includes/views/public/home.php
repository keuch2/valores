<?php /** Home. Recibe $servicios, $oportunidades, $tasas. */ ?>
<section class="hero-main" style="background:linear-gradient(135deg,var(--color-blue-inst),var(--color-blue-fin));color:#fff">
  <div class="container py-20">
    <div class="max-w-2xl animate-fade-up">
      <div class="section-tag" style="color:var(--color-naranja)">+33 años de trayectoria</div>
      <h1 class="text-4xl md:text-5xl font-bold mt-3 mb-4">Inversiones con la Casa de Bolsa líder del Paraguay</h1>
      <p class="text-white/80 text-lg mb-8">Intermediación bursátil, bonos, CDAs, acciones y estructuración fiduciaria, con el respaldo de los que más saben del mercado de capitales.</p>
      <div class="flex flex-wrap gap-3">
        <a href="<?= e(url('apertura-de-cuenta')) ?>" class="btn btn-primary btn-lg">Abrir mi Cuenta</a>
        <a href="<?= e(url('oportunidades')) ?>" class="btn btn-secondary btn-lg">Ver oportunidades</a>
      </div>
    </div>
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
