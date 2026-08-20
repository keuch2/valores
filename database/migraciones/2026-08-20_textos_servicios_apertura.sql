-- ----------------------------------------------------------------------------
-- Migración 2026-08-20 — Revisión editorial de servicios + flujo simple de
-- apertura (WhatsApp configurable). Idempotente: se puede ejecutar más de una
-- vez (los INSERT usan claves únicas con IGNORE).
--
-- Ejecutar en el servidor tras el deploy del código:
--   mysql -u<usuario> -p <base_de_datos> < database/migraciones/2026-08-20_textos_servicios_apertura.sql
-- ----------------------------------------------------------------------------

-- 1) Config: número de WhatsApp destino de las solicitudes de apertura.
--    (Config::guardar() solo hace UPDATE; la fila debe existir para poder
--    editarla desde Admin → Configuración → Módulo de apertura.)
INSERT IGNORE INTO configuracion (clave, valor, grupo)
VALUES ('apertura_whatsapp', '', 'apertura');

-- 2) Servicios: descripciones nuevas según la revisión editorial del cliente.
UPDATE servicios SET
  descripcion_corta = 'Comprá y vendé valores en la Bolsa de Valores y Productos de Asunción con el respaldo de nuestros corredores especializados.'
WHERE slug = 'intermediacion-bursatil';

UPDATE servicios SET
  titulo = 'Mercados Internacionales',
  descripcion_corta = 'Acceso a oportunidades de inversión y financiamiento en mercados internacionales, diversificación de portafolios y operaciones con instrumentos globales.'
WHERE slug = 'mercado-internacional';

UPDATE servicios SET
  descripcion_corta = 'Análisis de empresas, sectores y mercados para apoyar la toma de decisiones de inversión y financiamiento.'
WHERE slug = 'analisis-financiero';

UPDATE servicios SET
  descripcion_corta = 'Diseño de estructuras fiduciarias orientadas a proyectos de inversión, financiamiento, administración de activos y generación de vehículos especializados.',
  orden = 5
WHERE slug = 'estructuracion-fiduciaria';

UPDATE servicios SET
  descripcion_corta = 'Asesoramiento financiero y estructuración de proyectos bajo esquemas de participación público-privada, desde el análisis de viabilidad hasta la estructuración financiera.',
  orden = 6
WHERE slug = 'app';

UPDATE servicios SET
  descripcion_corta = 'Elaboración de reportes financieros, análisis de mercado, seguimiento de emisiones, tasas, instrumentos y principales indicadores para facilitar la toma de decisiones.',
  orden = 8
WHERE slug = 'reportes';

-- 3) Servicios nuevos (slug es UNIQUE → INSERT IGNORE es idempotente).
INSERT IGNORE INTO servicios (titulo, slug, icono, descripcion_corta, orden, activo) VALUES
  ('Estructuración de Financiamiento', 'estructuracion-de-financiamiento', 'fa-coins',
   'Diseño y estructuración de alternativas de financiamiento a través del mercado de valores, incluyendo emisiones de bonos, acciones y otros instrumentos.', 4, 1),
  ('Asesoramiento Financiero Corporativo', 'asesoramiento-financiero-corporativo', 'fa-briefcase',
   'Acompañamiento a empresas en decisiones estratégicas de financiamiento, inversión, reorganización financiera y acceso al mercado de capitales.', 7, 1);
