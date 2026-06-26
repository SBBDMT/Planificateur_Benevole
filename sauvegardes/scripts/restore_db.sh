#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="${ENV_FILE:-$ROOT_DIR/.env}"
ASSUME_YES="no"

if [ "${1:-}" = "--yes" ]; then
    ASSUME_YES="yes"
    shift
fi

BACKUP_FILE="${1:-}"

read_env() {
    local key="$1"
    grep -E "^[[:space:]]*${key}=" "$ENV_FILE" \
        | tail -n 1 \
        | cut -d '=' -f 2- \
        | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

if [ -z "$BACKUP_FILE" ]; then
    echo "Usage : $0 [--yes] chemin/vers/backup.sql" >&2
    exit 1
fi

if [ ! -f "$BACKUP_FILE" ]; then
    echo "Erreur : backup introuvable : $BACKUP_FILE" >&2
    exit 1
fi

if [ ! -f "$ENV_FILE" ]; then
    echo "Erreur : fichier .env introuvable : $ENV_FILE" >&2
    exit 1
fi

DB_HOST="$(read_env DB_HOST)"
DB_PORT="$(read_env DB_PORT)"
DB_NAME="$(read_env DB_NAME)"
DB_USER="$(read_env DB_USER)"
DB_PASS="$(read_env DB_PASS)"
CLIENT_CNF="$(mktemp)"
trap 'rm -f "$CLIENT_CNF"' EXIT

if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    echo "Erreur : variables DB_HOST, DB_PORT, DB_NAME ou DB_USER manquantes dans .env" >&2
    exit 1
fi

if [ -f "${BACKUP_FILE}.sha256" ]; then
    sha256sum -c "${BACKUP_FILE}.sha256"
fi

if [ "$ASSUME_YES" != "yes" ]; then
    echo "Cette opération va restaurer le dump dans la base : $DB_NAME"
    echo "Fichier : $BACKUP_FILE"
    printf "Confirmer la restauration ? Taper RESTORE : "
    read -r CONFIRMATION

    if [ "$CONFIRMATION" != "RESTORE" ]; then
        echo "Restauration annulée."
        exit 0
    fi
fi

chmod 600 "$CLIENT_CNF"
cat > "$CLIENT_CNF" <<EOF
[client]
host=$DB_HOST
port=$DB_PORT
user=$DB_USER
password=$DB_PASS
EOF

mariadb \
    --defaults-extra-file="$CLIENT_CNF" \
    "$DB_NAME" < "$BACKUP_FILE"

echo "Restauration terminée dans la base : $DB_NAME"
