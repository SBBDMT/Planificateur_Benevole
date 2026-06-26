-- ============================================================
--  Planificateur de bénévoles — Festival
--  Schéma MySQL / MariaDB
--  Projet Scrum — Mastère ESI
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS planificateur_benevoles;
CREATE DATABASE planificateur_benevoles
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE planificateur_benevoles;

-- ============================================================
--  TABLE : role
--  Rôles applicatifs disponibles
-- ============================================================
CREATE TABLE role (
    id    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name  VARCHAR(50)   NOT NULL UNIQUE,                          -- ex. 'volunteer'
    label VARCHAR(100)  NOT NULL,                                 -- ex. 'Bénévole'

    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Insertion des rôles par défaut
INSERT INTO role (name, label) VALUES
    ('volunteer',   'Bénévole'),
    ('coordinator', 'Coordinateur'),
    ('manager',     'Responsable'),
    ('admin',       'Administrateur');

-- ============================================================
--  TABLE : user
--  Compte applicatif lié à un rôle
-- ============================================================
CREATE TABLE user (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,                            -- hash bcrypt
    role_id    INT UNSIGNED  NOT NULL DEFAULT 1,                  -- FK → role (défaut : volunteer)
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT fk_user_role
        FOREIGN KEY (role_id) REFERENCES role(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : zone
--  Zone du festival sous la responsabilité d'un responsable
-- ============================================================
CREATE TABLE zone (
    id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name           VARCHAR(100)  NOT NULL,
    responsible_id INT UNSIGNED  NULL,                            -- FK → user (manager)

    PRIMARY KEY (id),

    CONSTRAINT fk_zone_responsible
        FOREIGN KEY (responsible_id) REFERENCES user(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : skill
--  Compétence éventuelle requise par une mission
-- ============================================================
CREATE TABLE skill (
    id   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name VARCHAR(100)  NOT NULL UNIQUE,

    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : volunteer
--  Profil bénévole lié à un utilisateur
-- ============================================================
CREATE TABLE volunteer (
    id      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED  NOT NULL UNIQUE,                        -- 1 user = 1 volunteer max
    phone   VARCHAR(20)   NULL,
    active  TINYINT(1)    NOT NULL DEFAULT 1,

    PRIMARY KEY (id),

    CONSTRAINT fk_volunteer_user
        FOREIGN KEY (user_id) REFERENCES user(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : volunteer_skill
--  Table de jointure : compétences d'un bénévole (M2M)
-- ============================================================
CREATE TABLE volunteer_skill (
    volunteer_id INT UNSIGNED NOT NULL,
    skill_id     INT UNSIGNED NOT NULL,

    PRIMARY KEY (volunteer_id, skill_id),

    CONSTRAINT fk_vs_volunteer
        FOREIGN KEY (volunteer_id) REFERENCES volunteer(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_vs_skill
        FOREIGN KEY (skill_id) REFERENCES skill(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : mission
--  Mission à couvrir pendant le festival
-- ============================================================
CREATE TABLE mission (
    id                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title             VARCHAR(150)  NOT NULL,
    description       TEXT          NULL,
    location          VARCHAR(150)  NOT NULL,
    zone_id           INT UNSIGNED  NULL,
    coordinator_id    INT UNSIGNED  NOT NULL,                     -- FK → user (coordinateur créateur)
    starts_at         DATETIME      NOT NULL,
    ends_at           DATETIME      NOT NULL,
    required_capacity INT UNSIGNED  NOT NULL DEFAULT 1,
    required_skill_id INT UNSIGNED  NULL,                         -- compétence facultative
    status            ENUM('draft', 'open', 'full', 'closed')
                                    NOT NULL DEFAULT 'open',

    PRIMARY KEY (id),

    -- fin > début
    CONSTRAINT chk_mission_dates
        CHECK (ends_at > starts_at),

    CONSTRAINT fk_mission_zone
        FOREIGN KEY (zone_id) REFERENCES zone(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_mission_coordinator
        FOREIGN KEY (coordinator_id) REFERENCES user(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_mission_skill
        FOREIGN KEY (required_skill_id) REFERENCES skill(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : availability
--  Créneau de disponibilité déclaré par un bénévole
-- ============================================================
CREATE TABLE availability (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    volunteer_id INT UNSIGNED NOT NULL,
    starts_at    DATETIME     NOT NULL,
    ends_at      DATETIME     NOT NULL,

    PRIMARY KEY (id),

    -- fin > début
    CONSTRAINT chk_availability_dates
        CHECK (ends_at > starts_at),

    CONSTRAINT fk_availability_volunteer
        FOREIGN KEY (volunteer_id) REFERENCES volunteer(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : assignment
--  Affectation contrôlée d'un bénévole à une mission
-- ============================================================
CREATE TABLE assignment (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    mission_id   INT UNSIGNED NOT NULL,
    volunteer_id INT UNSIGNED NOT NULL,
    assigned_by  INT UNSIGNED NOT NULL,                           -- FK → user (coordinateur)
    status       ENUM('pending', 'confirmed', 'cancelled')
                              NOT NULL DEFAULT 'confirmed',
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- un bénévole ne peut être affecté qu'une fois à la même mission
    UNIQUE KEY uq_assignment (mission_id, volunteer_id),

    CONSTRAINT fk_assignment_mission
        FOREIGN KEY (mission_id) REFERENCES mission(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_assignment_volunteer
        FOREIGN KEY (volunteer_id) REFERENCES volunteer(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_assignment_assigned_by
        FOREIGN KEY (assigned_by) REFERENCES user(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : audit_log
--  Traçabilité des actions clés
-- ============================================================
CREATE TABLE audit_log (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  NULL,                               -- NULL si action système
    action      VARCHAR(50)   NOT NULL,                           -- ex. 'assignment.created'
    entity_type VARCHAR(50)   NOT NULL,                           -- ex. 'assignment'
    entity_id   INT UNSIGNED  NULL,                               -- ID de l'entité concernée
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES user(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  INDEX utiles pour les requêtes fréquentes
-- ============================================================

-- Recherche des disponibilités d'un bénévole sur un créneau
CREATE INDEX idx_availability_volunteer ON availability (volunteer_id, starts_at, ends_at);

-- Recherche des affectations d'une mission
CREATE INDEX idx_assignment_mission     ON assignment (mission_id, status);

-- Recherche des affectations d'un bénévole
CREATE INDEX idx_assignment_volunteer   ON assignment (volunteer_id, status);

-- Recherche des missions par coordinateur
CREATE INDEX idx_mission_coordinator    ON mission (coordinator_id, status);

-- Recherche des missions par zone et statut
CREATE INDEX idx_mission_zone_status    ON mission (zone_id, status);

-- Recherche des missions par créneau horaire
CREATE INDEX idx_mission_dates          ON mission (starts_at, ends_at);

-- Audit log par entité
CREATE INDEX idx_audit_entity           ON audit_log (entity_type, entity_id);

SET FOREIGN_KEY_CHECKS = 1;