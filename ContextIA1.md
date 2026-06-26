# 📝 Contexte de la conversation — Projet SCRUM Planificateur de bénévoles

**Date :** 26/06/2026
**Projet :** Planificateur de bénévoles pour un événement culturel
**Cadre :** Mastère ESI — Mini-projet Scrum — Fiche 5
**Équipe :** 3 personnes — Sprint unique de 15H

---

## 👥 Équipe

| Membre | Rôle | Responsabilités |
|--------|------|----------------|
| Aurélien (toi) | Dev backend / Membre 1 | Modèle de données, règles métier, actions PHP, validations |
| Jonathan | Dev frontend / Membre 2 | Interfaces, pages PHP, formulaires, planning |
| Sophian | DevOps / Scrum Master / Membre 3 | Repo GitHub, CI/CD, seed démo, README, sauvegarde |

---

## 🎯 Sprint Goal

> Livrer en 15 heures un incrément utilisable permettant de créer des missions, déclarer les disponibilités, affecter sans conflit et afficher les besoins non couverts.

---

## 🗂️ Sujet (Fiche 5)

- **Domaine :** Culture / organisation événementielle
- **Problème métier :** Affectations gérées dans des tableurs séparés → doublons, créneaux sous-dotés, modifications difficiles à communiquer
- **Utilisateurs :** Bénévole, Coordinateur de zone, Responsable du festival
- **Valeur attendue :** Planning cohérent, zéro conflit, visibilité des besoins non couverts

---

## 🏗️ Stack technique

| Élément | Choix |
|---------|-------|
| Backend | PHP vanilla (sans framework) |
| Frontend | HTML + CSS pur |
| Base de données | MySQL / MariaDB |
| Environnement dev | UniServerZ (Windows) |
| Versioning | GitHub |
| CI/CD | GitHub Actions (Sophian) |
| Conteneurisation | Docker + docker-compose (Sophian) |

---

## 📁 Structure du projet

```
planificateur-benevoles/
│
├── index.php                          # Routing (Jonathan)
├── login.php                          # Page connexion (Jonathan)
├── logout.php                         # Déconnexion ✅ (Aurélien)
│
├── config/
│   └── db.php                         # Connexion PDO MySQL ✅ (Aurélien)
│
├── includes/
│   ├── header.php                     # Nav selon rôle ✅ (généré)
│   ├── footer.php                     # Pied de page ✅ (généré)
│   └── functions.php                  # Règles métier ✅ (Aurélien)
│
├── pages/
│   ├── — BÉNÉVOLE —
│   ├── planning.php                   # Mon planning ✅ (Jonathan)
│   ├── disponibilites.php             # Mes dispos ✅ (Jonathan)
│   │
│   ├── — COORDINATEUR —
│   ├── mes_missions.php               # Ses missions ✅ (Jonathan)
│   ├── creer_mission.php              # Formulaire création ✅ (Jonathan)
│   ├── mes_affectations.php           # Ses affectations ✅ (Jonathan)
│   ├── creer_benevole.php             # Créer bénévole ✅ (Jonathan)
│   ├── audit_log.php                  # Journal actions ✅ (Jonathan)
│   ├── couverture.php                 # Taux de couverture ✅ (Jonathan)
│   ├── planning_mission.php           # Planning par mission ✅ (Jonathan)
│   │
│   └── — RESPONSABLE —
│       ├── dashboard.php              # Vue globale (Jonathan)
│       ├── toutes_missions.php        # Toutes les missions (Jonathan)
│       ├── toutes_affectations.php    # Toutes les affectations (Jonathan)
│       └── missions_sous_dotees.php   # Missions incomplètes ✅ (Jonathan)
│
├── actions/
│   ├── login.php                      # Auth + session ✅ (Aurélien)
│   ├── create_mission.php             # US1-A/B ✅ (Aurélien)
│   ├── create_benevole.php            # US2-A ✅ (Aurélien)
│   ├── save_disponibilite.php         # US2-B/C ✅ (Aurélien)
│   ├── affecter.php                   # US3-A/B/C ✅ (Aurélien)
│   └── desaffecter.php                # US3-D ✅ (Aurélien/Sophian)
│
├── css/
│   └── style.css                      # CSS pur ✅ (Jonathan)
│
├── sql/
│   ├── schema.sql                     # Création tables ✅ (Aurélien)
│   └── demo.sql                       # Seed démo ⬜ (Sophian)
│
├── .env.example                       # ✅ (Aurélien)
├── .env                               # ⛔ dans .gitignore
├── .gitignore                         # ✅ (Aurélien)
└── docker-compose.yml                 # ✅ (Sophian)
```

---

## 🗄️ Base de données

### Tables (9 tables)

| Table | Rôle |
|-------|------|
| `role` | Rôles applicatifs (volunteer, coordinator, manager, admin) |
| `user` | Compte applicatif lié à un rôle |
| `volunteer` | Profil bénévole lié à un user |
| `volunteer_skill` | Jointure M2M bénévole ↔ compétence |
| `skill` | Compétences disponibles |
| `zone` | Zones du festival |
| `mission` | Missions à couvrir |
| `availability` | Disponibilités des bénévoles |
| `assignment` | Affectations contrôlées |
| `audit_log` | Traçabilité de toutes les actions |

### Règles métier implémentées

| Règle | Où |
|-------|----|
| Fin > début (dispo) | `save_disponibilite.php` + CHECK SQL |
| Pas de chevauchement dispo | `save_disponibilite.php` |
| Dispo couvre tout le créneau mission | `affecter.php` |
| Pas de conflit horaire entre missions | `affecter.php` |
| Capacité non dépassée | `affecter.php` |
| Unicité affectation | UNIQUE KEY SQL |
| Traçabilité audit | `affecter.php` + `desaffecter.php` |

---

## 👤 Rôles et accès

| Page | Bénévole | Coordinateur | Responsable |
|------|----------|--------------|-------------|
| `planning.php` | ✅ son planning | ❌ | ❌ |
| `disponibilites.php` | ✅ ses dispos | ❌ | ❌ |
| `mes_missions.php` | ❌ | ✅ ses missions | ❌ |
| `creer_mission.php` | ❌ | ✅ | ❌ |
| `mes_affectations.php` | ❌ | ✅ ses affectations | ❌ |
| `audit_log.php` | ❌ | ✅ ses logs | ✅ tous |
| `couverture.php` | ❌ | ✅ ses missions | ✅ toutes |
| `missions_sous_dotees.php` | ❌ | ✅ ses missions | ✅ toutes |
| `dashboard.php` | ❌ | ❌ | ✅ |
| `toutes_missions.php` | ❌ | ❌ | ✅ |

---

## ✅ Sprint Backlog — État actuel

| # | ID | Story | Responsable | Statut |
|---|-----|-------|-------------|--------|
| 1 | T0 | Initialiser dépôt, conventions, CI | Sophian | ⬜ |
| 2 | T1 | Créer le modèle de données minimal | Aurélien | ✅ |
| 3 | US1-A | Créer une mission simple | Aurélien | ✅ |
| 4 | US1-B | Ajouter compétence éventuelle | Aurélien | ✅ |
| 5 | US2-A | Créer un bénévole | Aurélien | ✅ |
| 6 | US2-B | Renseigner les disponibilités | Jonathan | ✅ |
| 7 | US2-C | Empêcher dispos incohérentes | Aurélien | ✅ |
| 8 | US3-A | Affecter un bénévole | Aurélien | ✅ |
| 9 | US3-B | Refuser affectation en conflit | Aurélien | ✅ |
| 10 | US3-C | Refuser si capacité dépassée | Aurélien | ✅ |
| 11 | US3-D | Tracer affectation/désaffectation | Sophian | ✅ |
| 12 | US4-A | Planning d'un bénévole | Jonathan | ✅ |
| 13 | US4-B | Planning par mission | Jonathan | ✅ |
| 14 | US5-A | Calculer couverture d'une mission | Aurélien | ✅ |
| 15 | US5-B | Identifier missions sous-dotées | Jonathan | ✅ |
| 16 | US5-C | Filtrer missions sous-dotées | Jonathan | ✅ |
| 17 | T2 | Jeu de données de démonstration | Sophian | ⬜ |
| 18 | T3 | Tests essentiels | Tous | ⬜ |
| 19 | T4 | Documentation d'exploitation | Sophian | ⬜ |
| 20 | T5 | Sauvegarde et restauration | Sophian | ⬜ |

---

## 📄 Fichiers produits dans cette conversation

### Documents de cadrage
- `Projet_Scrum_Benevoles.docx` — Document Word complet (11 sections)
- `SPRINT_BACKLOG.md` — Backlog officiel avec phases et DoD
- `DAILY_LOG.md` — Templates 3 Dailys
- `TRACABILITE_IA.md` — Traçabilité usage IA

### Code backend (Aurélien)
- `config/db.php` — Connexion PDO + parsing .env
- `actions/login.php` — Auth + session + audit
- `logout.php` — Destruction session + audit
- `actions/create_mission.php` — US1-A/B
- `actions/create_benevole.php` — US2-A
- `actions/save_disponibilite.php` — US2-B/C
- `actions/affecter.php` — US3-A/B/C
- `actions/desaffecter.php` — US3-D
- `includes/functions.php` — US5-A (getCouvertureMission, getCouvertureToutes)

### Code frontend (Jonathan)
- `includes/header.php` — Nav selon rôle
- `includes/footer.php`
- `css/style.css`
- `pages/creer_mission.php`
- `pages/mes_missions.php`
- `pages/creer_benevole.php`
- `pages/disponibilites.php`
- `pages/mes_affectations.php`
- `pages/planning.php`
- `pages/planning_mission.php`
- `pages/couverture.php`
- `pages/audit_log.php`
- `pages/missions_sous_dotees.php`

### SQL
- `sql/schema.sql` — 9 tables + index + contraintes

### Config
- `.env.example`
- `.gitignore`
- `docker-compose.yml`

---

## ⚠️ Points non résolus

- **Problème MySQL UniServerZ** — connexion refusée même avec `127.0.0.1:3306` et `DB_PASS=root`
- **Page `demo.sql`** — à faire par Sophian (8+ bénévoles, 6+ missions, hash bcrypt)
- **Pages manquantes** — `dashboard.php`, `toutes_missions.php`, `toutes_affectations.php`, `login.php`, `index.php` à faire par Jonathan
- **T3 Tests** — à faire collectivement
- **T4 README** — à faire par Sophian
- **T5 Sauvegarde/restauration** — à faire par Sophian

---

## 🔑 Notes importantes

- Générer le hash bcrypt pour demo.sql : `php -r "echo password_hash('password123', PASSWORD_BCRYPT);"`
- Coordinateurs/managers/admins créés directement en SQL via `demo.sql` ou phpMyAdmin
- Bénévoles créés via `create_benevole.php` (coordinateur) ou future page `register.php`
- `SESSION_SECRET` à générer : `php -r "echo bin2hex(random_bytes(32));"`
- Le `.env` ne doit **jamais** être commité sur GitHub