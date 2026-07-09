/* ============================================================
   VALORES CASA DE BOLSA — main.js
   Vanilla JavaScript: navbar, simulador, perfil inversor,
   filtros oportunidades, accordions, tabs, scroll animations
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================================
  // 1. NAVBAR — sticky + mobile menu + dropdowns
  // ============================================================
  const navbar = document.getElementById('main-navbar');
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenuClose = document.getElementById('mobile-menu-close');
  const mobileMenu = document.getElementById('mobile-menu');
  const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');

  // Sticky scroll effect
  if (navbar) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 40) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  }

  // Mobile menu open/close
  if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function () {
      mobileMenu.classList.add('open');
      document.body.style.overflow = 'hidden';
    });
  }

  if (mobileMenuClose) {
    mobileMenuClose.addEventListener('click', closeMobileMenu);
  }

  function closeMobileMenu() {
    if (mobileMenu) {
      mobileMenu.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  // Close mobile menu on outside click
  if (mobileMenu) {
    mobileMenu.addEventListener('click', function (e) {
      if (e.target === mobileMenu) closeMobileMenu();
    });
  }

  // Mobile dropdowns
  mobileDropdownToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      const content = this.nextElementSibling;
      const chevron = this.querySelector('.m-chevron');
      const isOpen = content.classList.contains('open');

      // Close all others
      document.querySelectorAll('.mobile-dropdown-content.open').forEach(function (el) {
        el.classList.remove('open');
      });
      document.querySelectorAll('.m-chevron.rotated').forEach(function (el) {
        el.classList.remove('rotated');
      });

      if (!isOpen) {
        content.classList.add('open');
        if (chevron) chevron.classList.add('rotated');
      }
    });
  });

  // Close mobile menu on nav link click
  document.querySelectorAll('#mobile-menu a').forEach(function (link) {
    link.addEventListener('click', closeMobileMenu);
  });

  // ============================================================
  // 2. SIMULADOR DE INVERSIÓN
  // ============================================================
  const simForm = document.getElementById('simulator-form');
  if (simForm) {
    simForm.addEventListener('submit', function (e) {
      e.preventDefault();
      runSimulator();
    });

    // Also react on input changes for live update feel
    simForm.addEventListener('input', function () {
      const capital = parseFloat(document.getElementById('sim-capital')?.value || 0);
      if (capital > 0) runSimulator();
    });
  }

  function runSimulator() {
    const capital   = parseFloat(document.getElementById('sim-capital')?.value || 0);
    const plazo     = parseInt(document.getElementById('sim-plazo')?.value || 12);
    const producto  = document.getElementById('sim-producto')?.value || 'bono';
    const moneda    = document.getElementById('sim-moneda')?.value || 'USD';
    const resultado = document.getElementById('simulator-result');

    if (!capital || capital <= 0) return;

    // Tasas referenciales por producto (anuales %).
    // Se toman de la configuración del CMS (window.SIM_TASAS) si está disponible;
    // si no, se usan estos valores por defecto.
    const tasas = Object.assign({
      bono:      8.5,
      cda:       6.0,
      accion:    12.0,
      inter:     9.5,
      letra:     5.5
    }, window.SIM_TASAS || {});

    const tasaAnual  = (tasas[producto] || 7.5) / 100;
    const plazoAnios = plazo / 12;
    const interes    = capital * tasaAnual * plazoAnios;
    const total      = capital + interes;
    const tasaDisplay = (tasas[producto] || 7.5).toFixed(1) + '% anual';

    const fmt = new Intl.NumberFormat('es-PY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const curr = moneda === 'PYG' ? '₲' : moneda === 'EUR' ? '€' : '$';

    // Update UI
    const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

    setEl('res-capital',  curr + ' ' + fmt.format(capital));
    setEl('res-tasa',     tasaDisplay);
    setEl('res-plazo',    plazo + ' meses');
    setEl('res-interes',  curr + ' ' + fmt.format(interes));
    setEl('res-total',    curr + ' ' + fmt.format(total));
    setEl('res-producto', getProductoLabel(producto));

    if (resultado) {
      resultado.classList.add('visible');
      resultado.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }

  function getProductoLabel(key) {
    const labels = {
      bono:   'Bono Corporativo',
      cda:    'CDA — Certificado de Depósito',
      accion: 'Acciones',
      inter:  'Mercado Internacional',
      letra:  'Letra del Tesoro'
    };
    return labels[key] || key;
  }

  // ============================================================
  // 3. PERFIL DE INVERSOR — Multistep
  // ============================================================
  const profileForm = document.getElementById('profile-form');
  if (profileForm) {
    let currentStep = 0;
    const steps = profileForm.querySelectorAll('.step-question');
    const progressFill = document.getElementById('stepper-fill');
    const stepCounter  = document.getElementById('step-counter');
    const btnPrev      = document.getElementById('profile-prev');
    const btnNext      = document.getElementById('profile-next');
    const btnCalc      = document.getElementById('profile-calc');
    const resultSection = document.getElementById('profile-result');
    const formSection   = document.getElementById('profile-steps');

    const totalSteps = steps.length;
    let answers = {};

    function updateStepper() {
      steps.forEach((s, i) => s.classList.toggle('active', i === currentStep));
      const pct = ((currentStep + 1) / totalSteps) * 100;
      if (progressFill) progressFill.style.width = pct + '%';
      if (stepCounter) stepCounter.textContent = (currentStep + 1) + ' / ' + totalSteps;
      if (btnPrev) btnPrev.style.display = currentStep === 0 ? 'none' : 'inline-flex';
      if (btnNext) btnNext.style.display = currentStep < totalSteps - 1 ? 'inline-flex' : 'none';
      if (btnCalc) btnCalc.style.display = currentStep === totalSteps - 1 ? 'inline-flex' : 'none';
    }

    if (btnNext) {
      btnNext.addEventListener('click', function () {
        if (currentStep < totalSteps - 1) {
          currentStep++;
          updateStepper();
        }
      });
    }

    if (btnPrev) {
      btnPrev.addEventListener('click', function () {
        if (currentStep > 0) {
          currentStep--;
          updateStepper();
        }
      });
    }

    // Select step option
    profileForm.querySelectorAll('.step-option').forEach(function (opt) {
      opt.addEventListener('click', function () {
        const step = this.closest('.step-question');
        step.querySelectorAll('.step-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
        const input = this.querySelector('input[type="radio"]');
        if (input) {
          input.checked = true;
          const stepNum = step.dataset.step;
          answers[stepNum] = input.value;
        }
      });
    });

    if (btnCalc) {
      btnCalc.addEventListener('click', function () {
        calculateProfile();
      });
    }

    function calculateProfile() {
      // Simple scoring: conservative=1, moderate=2, dynamic=3
      let score = 0;
      let count = 0;
      Object.values(answers).forEach(function (val) {
        const v = parseInt(val);
        if (!isNaN(v)) { score += v; count++; }
      });

      if (count === 0) { score = totalSteps; count = totalSteps; }
      const avg = score / count;

      let type = 'conservador';
      if (avg >= 2.2) type = 'dinamico';
      else if (avg >= 1.5) type = 'moderado';

      showProfileResult(type);
    }

    function showProfileResult(type) {
      if (formSection)   formSection.style.display  = 'none';
      if (resultSection) resultSection.style.display = 'block';

      document.querySelectorAll('.result-card').forEach(c => c.classList.remove('active'));
      const card = document.getElementById('result-' + type);
      if (card) card.classList.add('active');
    }

    // Restart button
    const btnRestart = document.getElementById('profile-restart');
    if (btnRestart) {
      btnRestart.addEventListener('click', function () {
        currentStep = 0;
        answers = {};
        profileForm.querySelectorAll('.step-option').forEach(o => o.classList.remove('selected'));
        profileForm.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);
        if (formSection)   formSection.style.display  = 'block';
        if (resultSection) resultSection.style.display = 'none';
        updateStepper();
      });
    }

    updateStepper();
  }

  // ============================================================
  // 5. FAQ ACCORDION
  // ============================================================
  document.querySelectorAll('.faq-trigger').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      const item = this.closest('.faq-item');
      const isOpen = item.classList.contains('open');

      // Close all
      document.querySelectorAll('.faq-item.open').forEach(function (el) {
        el.classList.remove('open');
      });

      if (!isOpen) item.classList.add('open');
    });
  });

  // ============================================================
  // 6. TABS
  // ============================================================
  document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const container = this.closest('[data-tabs]') || this.closest('section') || document;
      const target = this.dataset.tab;

      container.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      container.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

      this.classList.add('active');
      const panel = document.getElementById('tab-' + target);
      if (panel) panel.classList.add('active');
    });
  });

  // ============================================================
  // 7. SCROLL ANIMATIONS
  // ============================================================
  const animEls = document.querySelectorAll('.animate-fade-up');

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    animEls.forEach(function (el) { observer.observe(el); });
  } else {
    animEls.forEach(function (el) { el.classList.add('visible'); });
  }

  // ============================================================
  // 8. FORMS — basic validation + post-submit
  // ============================================================
  document.querySelectorAll('form[data-validate]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      let valid = true;

      form.querySelectorAll('[required]').forEach(function (field) {
        field.classList.remove('border-red-400');
        if (!field.value.trim()) {
          field.classList.add('border-red-400');
          valid = false;
        }
      });

      if (!valid) return;

      // Show success message
      const msg = form.nextElementSibling;
      if (msg && msg.classList.contains('success-msg')) {
        form.style.display = 'none';
        msg.classList.add('visible');
      }
    });
  });

  // ============================================================
  // 9. NEWSLETTER FORM
  // ============================================================
  const newsletterForms = document.querySelectorAll('.newsletter-form');
  newsletterForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const input  = form.querySelector('input[type="email"]');
      const btn    = form.querySelector('button');
      const msg    = form.querySelector('.nl-success');

      if (!input || !input.value.trim() || !input.value.includes('@')) {
        input && (input.style.borderColor = '#f87171');
        return;
      }

      if (btn)  { btn.textContent = '¡Suscripto!'; btn.disabled = true; }
      if (msg)  { msg.style.display = 'block'; }
      if (input){ input.value = ''; }
    });
  });

  // ============================================================
  // 10. CONTACT FORM
  // ============================================================
  const contactForm = document.getElementById('contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();
      let valid = true;

      contactForm.querySelectorAll('[required]').forEach(function (field) {
        field.style.borderColor = '';
        if (!field.value.trim()) {
          field.style.borderColor = '#f87171';
          valid = false;
        }
      });

      const checkbox = document.getElementById('acepto-contacto');
      if (checkbox && !checkbox.checked) {
        valid = false;
        checkbox.style.outline = '2px solid #f87171';
      } else if (checkbox) {
        checkbox.style.outline = '';
      }

      if (!valid) return;

      const btn = document.getElementById('contact-submit');
      const successMsg = document.getElementById('contact-success');

      if (btn) { btn.textContent = 'Mensaje enviado ✓'; btn.disabled = true; btn.classList.add('opacity-75'); }
      if (successMsg) { successMsg.classList.remove('hidden'); successMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
    });
  }

  // ============================================================
  // 10b. APERTURA DE CUENTA — tabs persona física / jurídica
  // ============================================================
  const acTabs = document.querySelectorAll('.ac-tab');
  const acPanels = document.querySelectorAll('.ac-panel');
  acTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      acTabs.forEach(t => t.classList.remove('active'));
      acPanels.forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      const panel = document.getElementById('ac-' + this.dataset.type);
      if (panel) panel.classList.add('active');
    });
  });

  // ============================================================
  // 11. MOBILE — chevron rotation style
  // ============================================================
  const mChevronStyle = document.createElement('style');
  mChevronStyle.textContent = '.m-chevron.rotated { transform: rotate(180deg); transition: transform 0.25s; }';
  document.head.appendChild(mChevronStyle);

  // ============================================================
  // 12. COUNTER ANIMATION for stats
  // ============================================================
  const counters = document.querySelectorAll('[data-count]');
  if (counters.length) {
    const counterObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(function (el) { counterObserver.observe(el); });
  }

  function animateCounter(el) {
    const target = parseFloat(el.dataset.count) || 0;
    const prefix = el.dataset.prefix || '';
    const suffix = el.dataset.suffix || '';
    const duration = 1800;
    const start = performance.now();

    function update(time) {
      const elapsed = time - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3); // ease out cubic
      const current = target * eased;
      el.textContent = prefix + (Number.isInteger(target) ? Math.round(current) : current.toFixed(1)) + suffix;
      if (progress < 1) requestAnimationFrame(update);
    }

    requestAnimationFrame(update);
  }

  // ============================================================
  // 13. ACTIVE NAV LINK
  // ============================================================
  const currentPath = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-link, .mobile-nav-link').forEach(function (link) {
    const href = (link.getAttribute('href') || '').split('/').pop();
    if (href === currentPath || (currentPath === '' && href === 'index.html')) {
      link.classList.add('active');
    }
  });

});
