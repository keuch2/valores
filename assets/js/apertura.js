/* ============================================================
   Valores — Wizard de apertura de cuenta (JS vanilla)
   Navegación por rama, validación por paso, guardado en localStorage,
   repeaters, titulares de cuenta conjunta, preview de firma y revisión.
   ============================================================ */
(function () {
  'use strict';
  var form = document.getElementById('apertura-form');
  if (!form) return;

  var LS_KEY = 'valores_apertura_v1';
  var tipo = '';
  var pasoActual = 0;
  var pasosVisibles = [];

  var el = {
    tipoInput: document.getElementById('tipo_persona'),
    progress: document.getElementById('ap-progress'),
    fill: document.getElementById('ap-progress-fill'),
    stepNum: document.getElementById('ap-step-num'),
    stepTotal: document.getElementById('ap-step-total'),
    stepTitle: document.getElementById('ap-step-title'),
    nav: document.getElementById('ap-nav'),
    prev: document.getElementById('ap-prev'),
    next: document.getElementById('ap-next'),
    submit: document.getElementById('ap-submit'),
    revision: document.getElementById('ap-revision')
  };

  function stepsForRama(rama) {
    return Array.prototype.filter.call(document.querySelectorAll('.ap-step'), function (s) {
      var r = s.getAttribute('data-rama');
      return r === 'all' ? false : r.split(' ').indexOf(rama) !== -1;
    });
  }

  function elegirTipo(t) {
    tipo = t;
    el.tipoInput.value = t;
    // Paso 0 se oculta; construir la lista de pasos de la rama + paso final.
    document.querySelector('.ap-step[data-step="0"]').style.display = 'none';
    pasosVisibles = stepsForRama(t);
    // Mostrar/ocultar anexo de titulares de conjunta.
    var anexo = document.getElementById('ap-titulares');
    if (anexo) anexo.style.display = (t === 'conjunta') ? 'block' : 'none';
    el.progress.style.display = 'block';
    el.nav.style.display = 'flex';
    pasoActual = 0;
    mostrarPaso();
    guardar();
  }

  function mostrarPaso() {
    pasosVisibles.forEach(function (s, i) { s.style.display = (i === pasoActual) ? 'block' : 'none'; });
    var total = pasosVisibles.length;
    el.stepNum.textContent = pasoActual + 1;
    el.stepTotal.textContent = total;
    var titulo = pasosVisibles[pasoActual].querySelector('h2');
    el.stepTitle.textContent = titulo ? titulo.textContent : '';
    el.fill.style.width = Math.round(((pasoActual + 1) / total) * 100) + '%';
    el.prev.style.visibility = pasoActual === 0 ? 'hidden' : 'visible';
    var esUltimo = pasoActual === total - 1;
    el.next.style.display = esUltimo ? 'none' : 'inline-block';
    el.submit.style.display = esUltimo ? 'inline-block' : 'none';
    if (esUltimo) construirRevision();
    window.scrollTo({ top: form.offsetTop - 80, behavior: 'smooth' });
  }

  function validarPaso() {
    var paso = pasosVisibles[pasoActual];
    var ok = true, primerError = null;
    // Requeridos: inputs/select/textarea con data-req
    paso.querySelectorAll('[data-req="1"]').forEach(function (f) {
      if (f.offsetParent === null) return; // oculto (ej. titulares no-conjunta)
      if (!String(f.value).trim()) { marcar(f, false); ok = false; primerError = primerError || f; }
      else marcar(f, true);
    });
    // Radios requeridos
    paso.querySelectorAll('[data-req-radio]').forEach(function (grp) {
      var name = grp.getAttribute('data-req-radio');
      if (!paso.querySelector('input[name="' + name + '"]:checked')) { ok = false; primerError = primerError || grp; }
    });
    // Checkbox requerido
    paso.querySelectorAll('[data-req-check="1"]').forEach(function (chk) {
      if (!chk.checked) { ok = false; primerError = primerError || chk; }
    });
    if (!ok && primerError && primerError.scrollIntoView) primerError.scrollIntoView({ block: 'center', behavior: 'smooth' });
    return ok;
  }

  function marcar(f, valido) { f.style.borderColor = valido ? '' : '#c0392b'; }

  function construirRevision() {
    var datos = {};
    var campos = form.querySelectorAll('input, select, textarea');
    campos.forEach(function (f) {
      if (!f.name || f.name === '_csrf' || f.name === 'website' || f.type === 'file') return;
      if ((f.type === 'radio' || f.type === 'checkbox') && !f.checked) return;
      var label = etiqueta(f);
      if (String(f.value).trim()) datos[label] = f.value;
    });
    var html = '<h3 class="font-bold text-blue-inst mb-2">Revisá tus datos</h3><dl>';
    Object.keys(datos).forEach(function (k) {
      html += '<div style="display:flex;gap:8px;padding:3px 0;border-bottom:1px solid var(--color-gray-ui)"><dt style="min-width:180px;color:var(--color-gray-text)">' + esc(k) + '</dt><dd>' + esc(datos[k]) + '</dd></div>';
    });
    html += '</dl>';
    el.revision.innerHTML = html;
  }

  function etiqueta(f) {
    var g = f.closest('.form-group');
    var lbl = g ? g.querySelector('.form-label') : null;
    if (lbl) return lbl.textContent.replace(' *', '').trim();
    var wrap = f.closest('label');
    return wrap ? wrap.textContent.trim().slice(0, 60) : f.name;
  }

  function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

  /* --- localStorage: guardar/restaurar (sin la firma) --- */
  function guardar() {
    try {
      var data = { tipo: tipo, paso: pasoActual, campos: {} };
      form.querySelectorAll('input, select, textarea').forEach(function (f) {
        if (!f.name || f.type === 'file' || f.name === '_csrf') return;
        if (f.type === 'radio' || f.type === 'checkbox') { if (f.checked) data.campos[f.name] = f.value; }
        else if (f.value) data.campos[f.name] = f.value;
      });
      localStorage.setItem(LS_KEY, JSON.stringify(data));
    } catch (e) {}
  }
  function restaurar() {
    try {
      var raw = localStorage.getItem(LS_KEY);
      if (!raw) return;
      var data = JSON.parse(raw);
      if (!data.tipo) return;
      // Restaurar los valores que el usuario ya había cargado, pero SIN elegir
      // tipo ni saltar de paso: el wizard siempre arranca en el Paso 0 para que
      // el usuario confirme el tipo de cliente antes de continuar.
      Object.keys(data.campos || {}).forEach(function (name) {
        var val = data.campos[name];
        var f = form.querySelector('[name="' + CSS.escape(name) + '"]');
        if (!f) return;
        if (f.type === 'radio' || f.type === 'checkbox') {
          var m = form.querySelector('[name="' + CSS.escape(name) + '"][value="' + CSS.escape(val) + '"]') || f;
          m.checked = true;
        } else f.value = val;
      });
    } catch (e) {}
  }

  /* --- Repeaters (jurídica) y titulares (conjunta) --- */
  function initRepeaters() {
    document.querySelectorAll('.ap-repeater').forEach(function (rep) {
      var list = rep.querySelector('.ap-repeater-list');
      var tpl = rep.querySelector('.ap-repeater-tpl');
      var add = rep.querySelector('.ap-repeater-add');
      var min = parseInt(rep.getAttribute('data-min') || '0', 10);
      var idx = 0;
      function addRow() {
        var html = tpl.innerHTML.replace(/__i__/g, idx++);
        var wrap = document.createElement('div'); wrap.innerHTML = html;
        var row = wrap.firstElementChild;
        row.querySelector('.ap-repeater-del').addEventListener('click', function () { row.remove(); guardar(); });
        list.appendChild(row);
      }
      add.addEventListener('click', addRow);
      for (var k = 0; k < min; k++) addRow();
    });
  }

  function initTitulares() {
    var add = document.getElementById('ap-add-titular');
    var lista = document.getElementById('ap-titulares-lista');
    if (!add || !lista) return;
    var idx = 0;
    add.addEventListener('click', function () {
      var i = idx++;
      var div = document.createElement('div');
      div.className = 'card p-4 mb-3';
      div.innerHTML =
        '<div class="form-group"><label class="form-label">Nombre y apellido</label><input class="form-input" name="titulares[' + i + '][nombre]"></div>' +
        '<div class="form-group"><label class="form-label">N° de documento</label><input class="form-input" name="titulares[' + i + '][documento]"></div>' +
        '<div class="form-group"><label class="form-label">Correo electrónico</label><input class="form-input" type="email" name="titulares[' + i + '][email]"></div>' +
        '<button type="button" class="btn btn-danger btn-sm">Quitar</button>';
      div.querySelector('button').addEventListener('click', function () { div.remove(); guardar(); });
      lista.appendChild(div);
    });
  }

  /* --- Preview de firma --- */
  var firma = document.getElementById('ap-firma');
  if (firma) {
    firma.addEventListener('change', function () {
      var prev = document.getElementById('ap-firma-preview');
      prev.innerHTML = '';
      if (firma.files && firma.files[0]) {
        var img = document.createElement('img');
        img.style.maxHeight = '90px'; img.style.borderRadius = '6px'; img.style.border = '1px solid #ddd';
        img.src = URL.createObjectURL(firma.files[0]);
        prev.appendChild(img);
      }
    });
  }

  /* --- Eventos --- */
  document.querySelectorAll('.ap-tipo-btn').forEach(function (btn) {
    btn.addEventListener('click', function () { elegirTipo(btn.getAttribute('data-tipo')); });
  });
  el.next.addEventListener('click', function () {
    if (!validarPaso()) return;
    if (pasoActual < pasosVisibles.length - 1) { pasoActual++; mostrarPaso(); guardar(); }
  });
  el.prev.addEventListener('click', function () {
    if (pasoActual > 0) { pasoActual--; mostrarPaso(); }
  });
  form.addEventListener('input', guardar);
  form.addEventListener('submit', function (e) {
    if (!validarPaso()) { e.preventDefault(); return; }
    if (firma && (!firma.files || !firma.files[0])) { e.preventDefault(); alert('Subí la imagen de tu firma para continuar.'); return; }
    localStorage.removeItem(LS_KEY);
  });

  initRepeaters();
  initTitulares();
  restaurar();
})();
