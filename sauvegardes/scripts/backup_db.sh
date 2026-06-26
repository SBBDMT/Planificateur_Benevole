#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="${ENV_FILE:-$ROOT_DIR/.env}"
BACKUP_DIR="${BACKUP_DIR:-$ROOT_DIR/sauvegardes/backups/database}"

read_env() {
    local key="$1"
    grep -E "^[[:space:]]*${key}=" "$ENV_FILE" \
        | tail -n 1 \
        | cut -d '=' -f 2- \
        | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

if [ ! -f "$ENV_FILE" ]; then
    echo "Erreur : fichier .env introuvable : $ENV_FILE" >&2
    exit 1
fi

DB_HOST="$(read_env DB_HOST)"
DB_PORT="$(read_env DB_PORT)"
DB_NAME="$(read_env DB_NAME)"
DB_USER="$(read_env DB_USER)"
DB_PASS="$(read_env DB_PASS)"

if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo "Erreur : variables DB_HOST, DB_PORT, DB_NAME ou DB_USER manquantes dans .env" >&2
    exit 1
fi

if command -v mariadb-dump >/dev/null 2>&1; then
    DUMP_BIN="mariadb-dump"
elif command -v mysqldump >/dev/null 2>&1; then
    DUMP_BIN="mysqldump"
else
    echo "Erreur : mariadb-dump ou mysqldump est requis." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_${TIMESTAMP}.sql"
CHECKSUM_FILE="${BACKUP_FILE}.sha256"
CLIENT_CNF="$(mktemp)"
trap 'rm -f "$CLIENT_CNF"' EXIT

chmod 600 "$CLIENT_CNF"
cat > "$CLIENT_CNF" <<EOF
[client]
host=$DB_HOST
port=$DB_PORT
user=$DB_USER
password=$DB_PASS
EOF

"$DUMP_BIN" \
    --defaults-extra-file="$CLIENT_CNF" \
    --single-transaction \
    --skip-events \
    --add-drop-table \
    --set-charset \
    "$DB_NAME" > "$BACKUP_FILE"

sha256sum "$BACKUP_FILE" > "$CHECKSUM_FILE"
chmod 600 "$BACKUP_FILE" "$CHECKSUM_FILE"

echo "Sauvegarde créée : $BACKUP_FILE"
echo "Empreinte SHA256 : $CHECKSUM_FILE"
