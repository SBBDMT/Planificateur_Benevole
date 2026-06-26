#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="${ENV_FILE:-$ROOT_DIR/.env}"

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
CLIENT_CNF="$(mktemp)"
trap 'rm -f "$CLIENT_CNF"' EXIT

QUERY="
SELECT 'users' AS item, COUNT(*) AS count FROM user
UNION ALL
SELECT 'volunteers', COUNT(*) FROM volunteer
UNION ALL
SELECT 'missions', COUNT(*) FROM mission
UNION ALL
SELECT 'assignments', COUNT(*) FROM assignment
UNION ALL
SELECT 'understaffed_missions', COUNT(*) FROM (
    SELECT m.id
    FROM mission m
    LEFT JOIN assignment a
        ON a.mission_id = m.id
        AND a.status = 'confirmed'
    GROUP BY m.id, m.required_capacity
    HAVING COUNT(a.id) < m.required_capacity
) t;
SELECT email, 'present' AS status
FROM user
WHERE email IN (
    'claire.responsable@festival.test',
    'nora.coordinateur@festival.test',
    'lucas.coordinateur@festival.test',
    'emma.martin@festival.test',
    'admin@festival.test'
)
ORDER BY email;
"

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
    "$DB_NAME" \
    --table \
    --execute="$QUERY"

echo "Vérification terminée."
