#!/usr/bin/env bash
set -euo pipefail

# Local-first deploy script for homolog theme sync.
# Default mode is dry-run. Use --apply to perform changes.

REMOTE_USER="${REMOTE_USER:-06215350443}"
REMOTE_HOST="${REMOTE_HOST:-150.161.0.230}"
HOST_HEADER="${HOST_HEADER:-ru-homolog.ufpe.br}"

LOCAL_THEME_DIR="${LOCAL_THEME_DIR:-wp-content/themes/ru-ufpe-theme}"
REMOTE_STAGE_DIR="${REMOTE_STAGE_DIR:-~/deploy/portal-ru3/wp-content/themes/ru-ufpe-theme}"
REMOTE_THEME_DIR="${REMOTE_THEME_DIR:-/var/www/ru/wp-classic/wp-content/themes/ru-ufpe-theme}"
WP_PATH="${WP_PATH:-/var/www/ru/wp-classic}"

DRY_RUN=1
if [[ "${1:-}" == "--apply" ]]; then
  DRY_RUN=0
elif [[ "${1:-}" == "--help" ]]; then
  cat <<'EOF'
Usage:
  scripts/deploy-homolog-theme.sh           # dry-run (default)
  scripts/deploy-homolog-theme.sh --apply   # execute deploy

Optional environment overrides:
  REMOTE_USER, REMOTE_HOST, HOST_HEADER
  LOCAL_THEME_DIR, REMOTE_STAGE_DIR, REMOTE_THEME_DIR, WP_PATH
EOF
  exit 0
fi

if [[ ! -d "$LOCAL_THEME_DIR" ]]; then
  echo "Local theme directory not found: $LOCAL_THEME_DIR" >&2
  exit 1
fi

REMOTE_TARGET="${REMOTE_USER}@${REMOTE_HOST}"

RSYNC_COMMON=(
  -avz
  --delete
  --omit-dir-times
  --no-perms
  --no-owner
  --no-group
)

if [[ "$DRY_RUN" == "1" ]]; then
  RSYNC_COMMON+=( -n )
  echo "[dry-run] staging theme to remote home"
else
  echo "[apply] staging theme to remote home"
fi

ssh "$REMOTE_TARGET" "mkdir -p '$REMOTE_STAGE_DIR'"
rsync "${RSYNC_COMMON[@]}" \
  "$LOCAL_THEME_DIR/" \
  "$REMOTE_TARGET:$REMOTE_STAGE_DIR/"

PROMOTE_FLAGS="-avh --delete --omit-dir-times --no-perms --no-owner --no-group"
if [[ "$DRY_RUN" == "1" ]]; then
  PROMOTE_FLAGS="$PROMOTE_FLAGS -n"
  echo "[dry-run] promoting staged theme to web root"
else
  echo "[apply] promoting staged theme to web root"
fi

ssh "$REMOTE_TARGET" \
  "set -euo pipefail; \
   sudo rsync $PROMOTE_FLAGS '$REMOTE_STAGE_DIR/' '$REMOTE_THEME_DIR/'"

if [[ "$DRY_RUN" == "1" ]]; then
  echo "Dry-run finished. Re-run with --apply to execute deploy."
  exit 0
fi

echo "[apply] running post-deploy checks"
ssh "$REMOTE_TARGET" \
  "set -euo pipefail; \
   sudo find '$REMOTE_THEME_DIR' -type d -exec chmod 755 {} \\\;; \
   sudo find '$REMOTE_THEME_DIR' -type f -exec chmod 644 {} \\\;; \
   sudo restorecon -Rv '$REMOTE_THEME_DIR' >/dev/null 2>&1 || true; \
   sudo -u apache -H wp --path='$WP_PATH' cache flush; \
   echo '== HTTP checks =='; \
   curl -sSI -H 'Host: $HOST_HEADER' 'http://127.0.0.1/' | head -n 1; \
   curl -sSI -H 'Host: $HOST_HEADER' 'http://127.0.0.1/wp-content/themes/ru-ufpe-theme/assets/images/illustrations/bandejao.png' | head -n 1; \
   curl -sSI -H 'Host: $HOST_HEADER' 'http://127.0.0.1/wp-content/themes/ru-ufpe-theme/assets/images/brand/institutional/institutional-signature-horizontal.svg' | head -n 1"

echo "Deploy complete."
