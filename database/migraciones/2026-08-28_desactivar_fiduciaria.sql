-- Migración 2026-08-28 — Servicio Estructuración Fiduciaria desactivado (pedido del cliente).
-- Sigue en el admin (activo=0); se reactiva desde Admin → Servicios cuando haga falta.
UPDATE servicios SET activo = 0 WHERE slug = 'estructuracion-fiduciaria';
