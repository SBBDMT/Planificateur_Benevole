<?php

function getCouvertureMission(PDO $pdo, int $missionId): ?array
{
    $missions = getCouvertureToutes($pdo, null, ['mission_id' => $missionId]);

    return $missions[0] ?? null;
}

function getCouvertureToutes(PDO $pdo, ?int $coordinatorId = null, array $filters = []): array
{
    $where = [];
    $params = [];

    if (!empty($filters['mission_id'])) {
        $where[] = 'm.id = :mission_id';
        $params[':mission_id'] = (int) $filters['mission_id'];
    }

    if ($coordinatorId !== null) {
        $where[] = 'm.coordinator_id = :coordinator_id';
        $params[':coordinator_id'] = $coordinatorId;
    }

    if (!empty($filters['zone_id'])) {
        $where[] = 'm.zone_id = :zone_id';
        $params[':zone_id'] = (int) $filters['zone_id'];
    }

    if (!empty($filters['statut'])) {
        $where[] = 'm.status = :statut';
        $params[':statut'] = $filters['statut'];
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $havingSql = !empty($filters['sous_dotees_only'])
        ? 'HAVING nb_assigned < m.required_capacity'
        : '';

    $sql = "
        SELECT
            m.id,
            m.title,
            m.location,
            m.starts_at,
            m.ends_at,
            m.required_capacity,
            m.status,
            m.zone_id,
            z.name AS zone_name,
            u.name AS coordinateur_name,
            COUNT(a.id) AS nb_assigned,
            GREATEST(m.required_capacity - COUNT(a.id), 0) AS manquants,
            CASE
                WHEN m.required_capacity > 0
                    THEN ROUND((COUNT(a.id) / m.required_capacity) * 100)
                ELSE 100
            END AS taux_pct,
            CASE
                WHEN COUNT(a.id) < m.required_capacity THEN 1
                ELSE 0
            END AS est_sous_dotee
        FROM mission m
        LEFT JOIN zone z ON z.id = m.zone_id
        INNER JOIN user u ON u.id = m.coordinator_id
        LEFT JOIN assignment a
            ON a.mission_id = m.id
            AND a.status = 'confirmed'
        $whereSql
        GROUP BY
            m.id,
            m.title,
            m.location,
            m.starts_at,
            m.ends_at,
            m.required_capacity,
            m.status,
            m.zone_id,
            z.name,
            u.name
        $havingSql
        ORDER BY m.starts_at ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function getMissionsAvecCouverture(PDO $pdo, ?int $coordinatorId = null, array $filters = []): array
{
    return getCouvertureToutes($pdo, $coordinatorId, $filters);
}

function formatDateTimeFr(string $dateTime): string
{
    return date('d/m/Y H:i', strtotime($dateTime));
}