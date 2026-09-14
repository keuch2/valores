-- Migración 2026-09-14 (c) — El slug acompaña al título del servicio y la
-- descripción corta deja de repetirlo (se muestra bajo el H1). Idempotente.
UPDATE servicios SET
  slug = 'gestion-profesional-de-inversiones',
  descripcion_corta = 'Análisis y selección de instrumentos, diversificación del portafolio y seguimiento de su evolución según el perfil y los objetivos de cada cliente.'
WHERE slug IN ('administracion-de-carteras', 'gestion-profesional-de-inversiones');
