-- ============================================================================
--  Valores Casa de Bolsa — CMS a medida
--  seed.sql — Superadmin inicial + configuración global + datos de ejemplo
--
--  ⚠️ Cambiar la contraseña del superadmin tras el primer login.
--  Credenciales por defecto:  admin@valores.com.py  /  Valores2026!
--  (El hash es bcrypt de "Valores2026!". Generar uno propio en producción.)
-- ============================================================================

USE valores_cms;

-- ----------------------------------------------------------------------------
--  Superadmin inicial
-- ----------------------------------------------------------------------------
INSERT INTO admin_users (nombre, email, password_hash, rol, activo)
VALUES (
  'Administrador',
  'admin@valores.com.py',
  '$2y$12$9tZOwXyTCM.QEOHt6lmEpulM7uUWJ/68bvVDS/Bw.nPAZQ2ws.cge',  -- bcrypt de "Valores2026!" — cambiar tras primer login
  'superadmin',
  1
);

-- ----------------------------------------------------------------------------
--  Configuración global (extraída del sitio estático actual)
-- ----------------------------------------------------------------------------
INSERT INTO configuracion (clave, valor, grupo) VALUES
  ('contacto_telefono',  '+595 (021) 600 450',          'contacto'),
  ('contacto_whatsapp',  '+595 994 100 003',            'contacto'),
  ('contacto_email',     'valores@valores.com.py',      'contacto'),
  ('contacto_direccion', 'Torre 3, Piso 10, Paseo La Galería, Asunción', 'contacto'),
  ('red_linkedin',  '', 'redes'),
  ('red_facebook',  '', 'redes'),
  ('red_instagram', '', 'redes'),
  ('red_twitter',   '', 'redes'),
  ('red_youtube',   '', 'redes'),
  -- Tasas del simulador (antes hardcodeadas en assets/js/main.js)
  ('tasa_bono',   '8.5',  'simulador'),
  ('tasa_cda',    '6.0',  'simulador'),
  ('tasa_accion', '12.0', 'simulador'),
  ('tasa_inter',  '9.5',  'simulador'),
  ('tasa_letra',  '5.5',  'simulador'),
  -- SMTP (a completar desde el panel)
  ('smtp_host', '', 'smtp'),
  ('smtp_port', '587', 'smtp'),
  ('smtp_user', '', 'smtp'),
  ('smtp_pass', '', 'smtp'),
  ('smtp_remitente', 'no-reply@valores.com.py', 'smtp'),
  ('smtp_encriptacion', 'tls', 'smtp'),
  -- Parámetros del módulo de apertura
  ('apertura_firma_max_bytes', '5242880', 'apertura'),
  ('apertura_firma_formatos',  'jpg,jpeg,png', 'apertura'),
  ('apertura_email_agente_asunto', 'Nueva solicitud de apertura de cuenta', 'apertura'),
  ('apertura_email_cliente_asunto', 'Recibimos tu solicitud — Valores Casa de Bolsa', 'apertura'),
  ('apertura_whatsapp', '+595 994 100 003', 'apertura'),
  ('oportunidades_pdf_id', '', 'oportunidades'),
  ('trabaja_email', 'administracion@valores.com.py', 'contacto');

-- ----------------------------------------------------------------------------
--  Servicios (8 según revisión editorial del cliente, ago-2026)
-- ----------------------------------------------------------------------------
INSERT INTO servicios (titulo, slug, icono, descripcion_corta, orden, activo) VALUES
  ('Intermediación Bursátil', 'intermediacion-bursatil', 'fa-building', 'Comprá y vendé valores en la Bolsa de Valores y Productos de Asunción con el respaldo de nuestros corredores especializados.', 1, 1),
  ('Mercados Internacionales', 'mercado-internacional', 'fa-globe', 'Acceso a oportunidades de inversión y financiamiento en mercados internacionales, diversificación de portafolios y operaciones con instrumentos globales.', 2, 1),
  ('Análisis Económico y Financiero', 'analisis-financiero', 'fa-chart-line', 'Análisis de empresas, sectores y mercados para apoyar la toma de decisiones de inversión y financiamiento.', 3, 1),
  ('Estructuración de Financiamiento', 'estructuracion-de-financiamiento', 'fa-coins', 'Diseño y estructuración de alternativas de financiamiento a través del mercado de valores, incluyendo emisiones de bonos, acciones y otros instrumentos.', 4, 1),
  ('Estructuración Fiduciaria', 'estructuracion-fiduciaria', 'fa-shield-halved', 'Diseño de estructuras fiduciarias orientadas a proyectos de inversión, financiamiento, administración de activos y generación de vehículos especializados.', 5, 1),
  ('Asociación Público-Privada (APP)', 'app', 'fa-handshake', 'Asesoramiento financiero y estructuración de proyectos bajo esquemas de participación público-privada, desde el análisis de viabilidad hasta la estructuración financiera.', 6, 1),
  ('Asesoramiento Financiero Corporativo', 'asesoramiento-financiero-corporativo', 'fa-briefcase', 'Acompañamiento a empresas en decisiones estratégicas de financiamiento, inversión, reorganización financiera y acceso al mercado de capitales.', 7, 1),
  ('Reportes de Mercado', 'reportes', 'fa-file-lines', 'Elaboración de reportes financieros, análisis de mercado, seguimiento de emisiones, tasas, instrumentos y principales indicadores para facilitar la toma de decisiones.', 8, 1);

-- ----------------------------------------------------------------------------
--  Ejecutivos / Plana directiva (3 reales; foto/email/tel a completar)
-- ----------------------------------------------------------------------------
INSERT INTO ejecutivos (nombre, cargo, orden, activo) VALUES
  ('Diego Christian Borja Terán', 'Presidente', 1, 1),
  ('Gustavo Mathias Angulo Turitich', 'Vicepresidente', 2, 1),
  ('Yanina Monges Chávez', 'Directora Titular', 3, 1);

-- ----------------------------------------------------------------------------
--  FAQ de ejemplo (las ~13 reales se cargarán/migrarán en Fase 6)
-- ----------------------------------------------------------------------------
INSERT INTO faqs (pregunta, respuesta, categoria, orden, activo) VALUES
  ('¿Cuál es el monto mínimo para invertir en bonos?', 'El monto mínimo varía según la emisión; consultá las oportunidades vigentes.', 'bono', 1, 1),
  ('¿Qué es un CDA?', 'Un Certificado de Depósito de Ahorro es un instrumento de renta fija a plazo.', 'cda', 1, 1);

-- ----------------------------------------------------------------------------
--  Oportunidades de ejemplo (reemplazan el PDF placeholder de 83 bytes)
-- ----------------------------------------------------------------------------
INSERT INTO oportunidades (tipo, instrumento, emisor, tasa, plazo, moneda, calificacion, monto_minimo, estado, destacado, orden) VALUES
  ('bono', 'Bono Corporativo Serie A', 'Emisor Ejemplo S.A.', 8.50, '360 días', 'Gs', 'AA', 5000000, 'disponible', 1, 1),
  ('cda',  'CDA 180 días', 'Banco Ejemplo', 6.00, '180 días', 'Gs', 'A', 1000000, 'disponible', 0, 2);

-- ----------------------------------------------------------------------------
--  Agentes de ejemplo (round-robin)
-- ----------------------------------------------------------------------------
INSERT INTO agentes (nombre, email, activo, orden) VALUES
  ('Agente Uno', 'agente1@valores.com.py', 1, 1),
  ('Agente Dos', 'agente2@valores.com.py', 1, 2);
