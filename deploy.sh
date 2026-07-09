#!/usr/bin/env bash
#
# deploy.sh — Deploy de Valores CMS en PRODUCCIÓN (codexpy.com/valores).
#
# Está pensado para ejecutarse DESDE EL SERVIDOR, dentro del propio repo:
#   cd /home/codexpy/public_html/valores && ./deploy.sh
#
# Qué hace:
#   1. (opcional) Si hay cambios sin commitear en el server y se pasó un mensaje,
#      hace commit + push a GitHub.
#   2. git fetch + reset --hard a origin/main  → trae lo último de GitHub.
#   3. Verifica la sintaxis PHP de los archivos clave con el PHP 8.3 del server.
#   4. Restaura permisos: código propiedad del usuario web, uploads escribibles.
#
# NO toca includes/config/config.php (está en .gitignore, es propio de cada
# entorno) ni el contenido subido en uploads/ (media y solicitudes KYC).
#
# Uso:
#   ./deploy.sh                      # pull de GitHub + verificación + permisos
#   ./deploy.sh "mensaje de commit"  # commit+push de cambios locales del server, luego pull
#   ./deploy.sh --pull-only          # igual que sin args (explícito)
#
set -euo pipefail

# --- Config del entorno de producción ---
REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BRANCH="main"
WEB_USER="codexpy"                       # dueño de los archivos servidos por PHP
PHP_BIN="/opt/php8-3/bin/php"            # PHP 8.3 del server (ver README/CLAUDE.md)

c_ok()   { printf '\033[0;32m%s\033[0m\n' "$*"; }
c_info() { printf '\033[0;36m%s\033[0m\n' "$*"; }
c_warn() { printf '\033[0;33m%s\033[0m\n' "$*"; }
c_err()  { printf '\033[0;31m%s\033[0m\n' "$*" >&2; }

cd "$REPO_DIR"

# --- Parse args ---
MSG=""
case "${1:-}" in
  --pull-only|"") : ;;
  *)              MSG="$1" ;;
esac

# --- 1. Commit + push de cambios hechos EN el server (solo si hay mensaje) ---
if [[ -n "$MSG" ]]; then
  if [[ -n "$(git status --porcelain)" ]]; then
    c_info "==> Cambios locales en el server. Commiteando…"
    git add -A
    git commit -m "$MSG"
    c_info "==> Push a GitHub (origin/$BRANCH)…"
    git push origin "$BRANCH"
    c_ok "Commit + push OK."
  else
    c_warn "Se pasó un mensaje pero no hay cambios locales para commitear."
  fi
fi

# --- 2. Traer lo último de GitHub ---
c_info "==> Actualizando desde GitHub (origin/$BRANCH)…"
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"
c_ok "Código actualizado a $(git rev-parse --short HEAD)."

# --- 3. Verificar sintaxis PHP (falla el deploy si algo no compila) ---
c_info "==> Verificando sintaxis PHP con $PHP_BIN…"
ERR=0
while IFS= read -r -d '' f; do
  if ! "$PHP_BIN" -l "$f" >/dev/null 2>&1; then
    c_err "  Error de sintaxis: $f"
    "$PHP_BIN" -l "$f" || true
    ERR=1
  fi
done < <(find . -path ./.git -prune -o -name '*.php' -print0)
if [[ "$ERR" -ne 0 ]]; then
  c_err "Hay errores de sintaxis PHP. Revisá antes de continuar."
  exit 1
fi
c_ok "Sintaxis PHP OK."

# --- 4. Permisos: código del usuario web, uploads escribibles ---
c_info "==> Restaurando permisos…"
# El .git puede quedar como root (si se clonó/deployó como root); el árbol de
# trabajo debe ser del usuario web para que PHP lo lea y escriba en uploads.
chown -R "$WEB_USER":"$WEB_USER" "$REPO_DIR" 2>/dev/null || \
  c_warn "  No se pudo chown (¿no sos root?). Continúo."
chmod -R u+rwX,go+rX "$REPO_DIR/uploads" 2>/dev/null || true
# config.php con permisos restringidos (contiene secretos).
[[ -f includes/config/config.php ]] && chmod 640 includes/config/config.php || true
c_ok "Permisos restaurados."

c_ok "==> Deploy completo. https://codexpy.com/valores"
