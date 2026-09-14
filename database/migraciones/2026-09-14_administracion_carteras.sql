-- Migración 2026-09-14 — El servicio "Asociación Público-Privada (APP)" se
-- reemplaza por "Administración de Carteras" (pedido del cliente). Idempotente.
UPDATE servicios SET
  titulo = 'Administración de Carteras',
  slug   = 'administracion-de-carteras',
  icono  = 'fa-chart-pie',
  descripcion_corta = 'Gestión profesional de inversiones: análisis y selección de instrumentos, diversificación del portafolio y seguimiento de su evolución según el perfil y los objetivos de cada cliente.'
WHERE slug IN ('app', 'administracion-de-carteras');
