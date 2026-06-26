-- ============================================================
--  Planificateur de bénévoles — Données de démonstration
--  À importer après sql/schema.sql
--
--  Comptes de test : mot de passe = password
--  Ce fichier ne crée pas de base et ne fait pas de USE afin
--  d'être compatible avec une base locale ou une base de CI.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Nettoyage des données métier pour rendre le fichier rejouable
DELETE FROM audit_log;
DELETE FROM assignment;
DELETE FROM availability;
DELETE FROM mission;
DELETE FROM volunteer_skill;
DELETE FROM volunteer;
DELETE FROM zone;
DELETE FROM skill;
DELETE FROM `user`;

ALTER TABLE audit_log AUTO_INCREMENT = 1;
ALTER TABLE assignment AUTO_INCREMENT = 1;
ALTER TABLE availability AUTO_INCREMENT = 1;
ALTER TABLE mission AUTO_INCREMENT = 1;
ALTER TABLE volunteer AUTO_INCREMENT = 1;
ALTER TABLE zone AUTO_INCREMENT = 1;
ALTER TABLE skill AUTO_INCREMENT = 1;
ALTER TABLE `user` AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  UTILISATEURS
--  Rôles attendus par schema.sql :
--  1 = volunteer, 2 = coordinator, 3 = manager, 4 = admin
-- ============================================================

INSERT INTO `user` (id, name, email, password, role_id) VALUES
    (1,  'Claire Responsable', 'claire.responsable@festival.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
    (2,  'Nora Coordinatrice', 'nora.coordinateur@festival.test',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
    (3,  'Lucas Coordinateur', 'lucas.coordinateur@festival.test',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
    (4,  'Admin Festival',     'admin@festival.test',               '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4),

    -- 8 bénévoles minimum
    (5,  'Emma Martin',        'emma.martin@festival.test',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
    (6,  'Hugo Bernard',       'hugo.bernard@festival.test',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
    (7,  'Lina Petit',         'lina.petit@festival.test',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
    (8,  'Karim Robert',       'karim.robert@festival.test',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
    (9,  'Zoé Durand',         'zoe.durand@festival.test',          '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
    (10, 'Maxime Moreau',      'maxime.moreau@festival.test',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
    (11, 'Inès Lefevre',       'ines.lefevre@festival.test',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
    (12, 'Thomas Leroy',       'thomas.leroy@festival.test',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- ============================================================
--  ZONES DU FESTIVAL
-- ============================================================

INSERT INTO zone (id, name, responsible_id) VALUES
    (1, 'Accueil et entrée', 1),
    (2, 'Scène principale', 1),
    (3, 'Restauration', 1),
    (4, 'Logistique', 1);

-- ============================================================
--  COMPÉTENCES
-- ============================================================

INSERT INTO skill (id, name) VALUES
    (1, 'Accueil'),
    (2, 'Billetterie'),
    (3, 'Sécurité'),
    (4, 'Technique son'),
    (5, 'Restauration'),
    (6, 'Montage');

-- ============================================================
--  PROFILS BÉNÉVOLES
-- ============================================================

INSERT INTO volunteer (id, user_id, phone, active) VALUES
    (1,  5,  '0600000001', 1),
    (2,  6,  '0600000002', 1),
    (3,  7,  '0600000003', 1),
    (4,  8,  '0600000004', 1),
    (5,  9,  '0600000005', 1),
    (6,  10, '0600000006', 1),
    (7,  11, '0600000007', 1),
    (8,  12, '0600000008', 1);

-- Compétences des bénévoles
INSERT INTO volunteer_skill (volunteer_id, skill_id) VALUES
    (1, 1), (1, 2),                 -- Emma : accueil, billetterie
    (2, 3), (2, 4),                 -- Hugo : sécurité, technique son
    (3, 1), (3, 5),                 -- Lina : accueil, restauration
    (4, 3),                         -- Karim : sécurité
    (5, 2), (5, 5),                 -- Zoé : billetterie, restauration
    (6, 4), (6, 6),                 -- Maxime : technique son, montage
    (7, 1), (7, 5),                 -- Inès : accueil, restauration
    (8, 1), (8, 3);                 -- Thomas : accueil, sécurité

-- ============================================================
--  MISSIONS — 6 missions minimum
-- ============================================================

INSERT INTO mission (
    id, title, description, location, zone_id, coordinator_id,
    starts_at, ends_at, required_capacity, required_skill_id, status
) VALUES
    (1, 'Accueil visiteurs matin',
        'Orienter les visiteurs, répondre aux questions et distribuer le programme.',
        'Entrée principale', 1, 3,
        '2026-07-18 09:00:00', '2026-07-18 12:00:00', 3, 1, 'full'),

    (2, 'Billetterie après-midi',
        'Contrôler les billets et gérer les entrées de l’après-midi.',
        'Guichet billetterie', 1, 3,
        '2026-07-18 13:00:00', '2026-07-18 17:00:00', 2, 2, 'full'),

    (3, 'Montage scène matin',
        'Aider à l’installation légère de la scène et du matériel.',
        'Scène principale', 2, 2,
        '2026-07-18 08:00:00', '2026-07-18 11:00:00', 2, 6, 'open'),

    (4, 'Support technique concert',
        'Assister l’équipe technique pendant le concert du soir.',
        'Régie scène principale', 2, 2,
        '2026-07-18 18:00:00', '2026-07-18 23:00:00', 2, 4, 'full'),

    (5, 'Stand restauration midi',
        'Aider au service et au réassort du stand restauration.',
        'Espace restauration', 3, 3,
        '2026-07-18 12:30:00', '2026-07-18 15:30:00', 3, 5, 'open'),

    (6, 'Sécurité entrée soirée',
        'Canaliser les files d’attente et signaler les incidents au responsable.',
        'Entrée principale', 1, 2,
        '2026-07-18 17:00:00', '2026-07-18 22:00:00', 2, 3, 'open');

-- ============================================================
--  DISPONIBILITÉS DES BÉNÉVOLES
-- ============================================================

INSERT INTO availability (id, volunteer_id, starts_at, ends_at) VALUES
    (1,  1, '2026-07-18 08:00:00', '2026-07-18 12:30:00'),
    (2,  1, '2026-07-18 13:00:00', '2026-07-18 17:30:00'),

    (3,  2, '2026-07-18 08:00:00', '2026-07-18 12:00:00'),
    (4,  2, '2026-07-18 18:00:00', '2026-07-18 23:30:00'),

    (5,  3, '2026-07-18 09:00:00', '2026-07-18 16:00:00'),

    (6,  4, '2026-07-18 16:00:00', '2026-07-18 22:30:00'),

    (7,  5, '2026-07-18 10:00:00', '2026-07-18 17:30:00'),

    (8,  6, '2026-07-18 07:30:00', '2026-07-18 11:30:00'),
    (9,  6, '2026-07-18 18:00:00', '2026-07-18 23:30:00'),

    (10, 7, '2026-07-18 09:00:00', '2026-07-18 16:00:00'),

    (11, 8, '2026-07-18 09:00:00', '2026-07-18 12:30:00'),
    (12, 8, '2026-07-18 17:00:00', '2026-07-18 22:30:00');

-- ============================================================
--  AFFECTATIONS VALIDES
--  Certaines missions restent volontairement sous-dotées pour
--  tester le dashboard responsable.
-- ============================================================

INSERT INTO assignment (id, mission_id, volunteer_id, assigned_by, status) VALUES
    -- Mission 1 : complète 3/3
    (1, 1, 1, 3, 'confirmed'),
    (2, 1, 3, 3, 'confirmed'),
    (3, 1, 8, 3, 'confirmed'),

    -- Mission 2 : complète 2/2
    (4, 2, 1, 3, 'confirmed'),
    (5, 2, 5, 3, 'confirmed'),

    -- Mission 3 : sous-dotée 1/2
    (6, 3, 6, 2, 'confirmed'),

    -- Mission 4 : complète 2/2
    (7, 4, 2, 2, 'confirmed'),
    (8, 4, 6, 2, 'confirmed'),

    -- Mission 5 : sous-dotée 2/3
    (9, 5, 3, 3, 'confirmed'),
    (10, 5, 5, 3, 'confirmed'),

    -- Mission 6 : sous-dotée 1/2
    (11, 6, 4, 2, 'confirmed');

-- ============================================================
--  JOURNAL D'AUDIT
-- ============================================================

INSERT INTO audit_log (user_id, action, entity_type, entity_id) VALUES
    (3, 'mission.created',    'mission',    1),
    (3, 'mission.created',    'mission',    2),
    (2, 'mission.created',    'mission',    3),
    (2, 'mission.created',    'mission',    4),
    (3, 'mission.created',    'mission',    5),
    (2, 'mission.created',    'mission',    6),

    (3, 'assignment.created', 'assignment', 1),
    (3, 'assignment.created', 'assignment', 2),
    (3, 'assignment.created', 'assignment', 3),
    (3, 'assignment.created', 'assignment', 4),
    (3, 'assignment.created', 'assignment', 5),
    (2, 'assignment.created', 'assignment', 6),
    (2, 'assignment.created', 'assignment', 7),
    (2, 'assignment.created', 'assignment', 8),
    (3, 'assignment.created', 'assignment', 9),
    (3, 'assignment.created', 'assignment', 10),
    (2, 'assignment.created', 'assignment', 11);
