# Rapport de projet — Planificateur de bénévoles

## 1. Informations générales

| Élément | Détail |
|---|---|
| Projet | Planificateur de bénévoles pour un événement culturel |
| Cadre | Mini-projet Scrum — sprint unique de 15 heures |
| Équipe | 3 personnes |
| Date du sprint | TODO: renseigner la date |
| Dépôt GitHub | TODO: ajouter le lien du dépôt |
| Branche de livraison | `main` |
| Version livrée | TODO: tag ou commit final |

---

## 2. Contexte métier

Une association prépare un festival sur une journée et doit affecter des bénévoles à différentes missions : accueil, billetterie, restauration, logistique, sécurité ou support technique.

Avant le projet, les affectations étaient gérées dans des tableurs séparés. Cette organisation entraînait plusieurs problèmes :

- doublons d’affectation ;
- conflits d’horaires ;
- missions sous-dotées ;
- manque de visibilité sur les besoins restants ;
- modifications difficiles à communiquer.

L’application vise donc à construire un planning cohérent, éviter les conflits et rendre visibles les missions incomplètes.

---

## 3. Sprint Goal

> Livrer en 15 heures un incrément utilisable permettant de créer des missions, déclarer les disponibilités, affecter sans conflit et afficher les besoins non couverts.

---

## 4. Équipe et rôles

| Membre | Rôle projet | Responsabilités principales |
|---|---|---|
| Aurélien | Dev backend / métier | Modèle de données, règles métier, actions PHP, validations |
| Jonathan | Dev frontend / parcours utilisateur | Pages PHP, formulaires, tableaux, parcours utilisateur |
| Sophian | DevOps / QA / Scrum Master | Repo GitHub, CI/CD, seed démo, documentation, sauvegarde |

TODO: ajuster si la répartition réelle a évolué.

---

## 5. Méthode Scrum appliquée

Le projet a été traité sous la forme d’un sprint unique de 15 heures. L’équipe a privilégié un incrément réduit mais démontrable, conformément au principe directeur du sujet : mieux vaut un petit MVP terminé qu’un grand périmètre partiellement réalisé.

### 5.1 Organisation temporelle

| Temps | Séquence | Résultat attendu |
|---|---|---|
| H0 → H1h15 | Sprint Planning + backlog | Sprint Goal, stories ordonnées, critères et tâches |
| H1h15 → H2h30 | Conception légère + squelette | Architecture minimale, dépôt, environnement, pipeline initial |
| H2h30 → H5h30 | Construction verticale 1 | Premier parcours démontrable |
| H5h30 → H5h45 | Daily Scrum | Obstacles visibles et plan adapté |
| H5h45 → H8h45 | Construction verticale 2 | Règles métier, tests, exploitation |
| H8h45 → H9h15 | Revue intermédiaire PO | Feedback et adaptation du périmètre |
| H9h15 → H12h15 | Stabilisation | MVP, CI/CD, données, documentation |
| H12h15 → H13h45 | Recette + préparation | Scénarios de revue et correction des défauts |
| H13h45 → H14h30 | Sprint Review | Démonstration et acceptation |
| H14h30 → H15h00 | Rétrospective | Amélioration concrète et mesurable |

### 5.2 Daily Scrum

Un seul Daily Scrum a été retenu pendant le sprint.

| Membre | Fait | Prévu | Blocage |
|---|---|---|---|
| Aurélien | TODO | TODO | TODO |
| Jonathan | TODO | TODO | TODO |
| Sophian | TODO | TODO | TODO |

Adaptation décidée après le Daily :

TODO: compléter avec la décision réelle prise pendant le sprint.

---

## 6. Backlog du sprint

| # | ID | Story | Critère d’acceptation | Responsable | Statut |
|---|---|---|---|---|---|
| 1 | T0 | Initialiser dépôt, conventions, CI minimale | Repo créé, pipeline lancé, structure projet disponible | Sophian | ✅ |
| 2 | T1 | Créer le modèle de données minimal | Tables missions, bénévoles, disponibilités, affectations créées | Aurélien | ✅ |
| 3 | US1-A | Créer une mission simple | Titre, lieu, début, fin, capacité enregistrés | Aurélien | ✅ |
| 4 | US1-B | Ajouter compétence éventuelle à une mission | Compétence facultative enregistrée et validée | Aurélien | ✅ |
| 5 | US2-A | Créer un bénévole | Nom, contact et profil créés | Aurélien | ✅ |
| 6 | US2-B | Renseigner les disponibilités | Créneaux enregistrés pour un bénévole | Jonathan | ✅ |
| 7 | US2-C | Empêcher les disponibilités incohérentes | Fin > début ; chevauchements détectés | Aurélien | ✅ |
| 8 | US3-A | Affecter un bénévole à une mission | Affectation créée si disponibilité suffisante | Aurélien | ✅ |
| 9 | US3-B | Refuser une affectation en conflit | Conflit horaire détecté et message clair affiché | Aurélien | ✅ |
| 10 | US3-C | Refuser si capacité dépassée | Mission pleine : affectation impossible | Aurélien | ✅ |
| 11 | US3-D | Tracer affectation et désaffectation | Journal visible ou exploitable dans les logs | Sophian | ✅ |
| 12 | US4-A | Afficher le planning d’un bénévole | Missions triées chronologiquement | Jonathan | ✅ |
| 13 | US4-B | Afficher le planning par mission | Liste des bénévoles affectés visible | Jonathan | ✅ |
| 14 | US5-A | Calculer la couverture d’une mission | Nombre affecté / capacité affiché | Aurélien | ✅ |
| 15 | US5-B | Identifier les missions sous-dotées | Missions incomplètes mises en évidence | Jonathan | ✅ |
| 16 | US5-C | Filtrer les missions sous-dotées | Filtre opérationnel par zone ou statut | Jonathan | ✅ |
| 17 | T2 | Jeu de données de démonstration | Au moins 8 bénévoles et 6 missions | Sophian | ✅ |
| 18 | T3 | Tests essentiels | Affectation, conflit, capacité, disponibilité | Tous | TODO |
| 19 | T4 | Documentation d’exploitation | README, lancement, limites connues, comptes fictifs | Sophian | TODO |
| 20 | T5 | Sauvegarde et restauration | Procédure testée avant revue | Sophian | ✅ |

TODO: mettre à jour les statuts T3 et T4 avant rendu final.

---

## 7. Analyse MVP une fois fini

### 7.1 Fonctionnalités livrées

Le MVP permet de :

- créer des missions avec lieu, horaires, capacité et compétence éventuelle ;
- créer ou gérer des bénévoles ;
- enregistrer les disponibilités des bénévoles ;
- affecter un bénévole à une mission ;
- refuser une affectation si le bénévole n’est pas disponible sur tout le créneau ;
- refuser une affectation en cas de conflit horaire ;
- refuser une affectation si la capacité est atteinte ;
- désaffecter un bénévole ;
- tracer les affectations et désaffectations ;
- afficher le planning d’un bénévole ;
- afficher le planning d’une mission ;
- calculer la couverture d’une mission ;
- afficher les missions sous-dotées ;
- filtrer les missions sous-dotées ;
- afficher toutes les missions pour le responsable ;
- afficher toutes les affectations pour le responsable ;
- fournir un jeu de données de démonstration ;
- fournir une CI/CD minimale ;
- fournir une procédure de sauvegarde et restauration.

### 7.2 Hors périmètre assumé

Les éléments suivants restent volontairement hors MVP :

- suggestion automatique d’affectation ;
- export CSV/PDF ;
- badge ou feuille de présence ;
- notification simulée ;
- optimisation automatique du planning.

Justification : le sprint étant limité à 15 heures, l’équipe a privilégié une affectation manuelle contrôlée, plus simple à démontrer et plus fiable.

### 7.3 Valeur produit atteinte

Le produit répond aux principaux problèmes métier :

| Problème initial | Réponse apportée |
|---|---|
| Doublons d’affectation | Contrôle d’unicité et refus des doublons |
| Conflits d’horaires | Vérification des chevauchements avant affectation |
| Missions sous-dotées | Dashboard et page dédiée aux missions incomplètes |
| Manque de visibilité | Vues responsable, toutes missions, toutes affectations |
| Modifications difficiles à tracer | Journal d’audit des affectations et désaffectations |

---

## 8. Architecture technique

### 8.1 Stack

| Élément | Choix |
|---|---|
| Backend | PHP vanilla |
| Frontend | HTML + CSS |
| Base de données | MariaDB / MySQL |
| Accès BDD | PDO |
| CI/CD | GitHub Actions |
| Serveur local | Apache / PHP |
| Configuration | `.env` non versionné |

### 8.2 Arborescence réelle du projet

```text
Planificateur_Benevole/
├── index.php
├── login.php
├── logout.php
├── config/
│   └── db.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── pages/
│   ├── affecter.php
│   ├── audit_log.php
│   ├── creer_benevole.php
│   ├── creer_mission.php
│   ├── dashboard.php
│   ├── disponibilites.php
│   ├── mes_affectations.php
│   ├── mes_missions.php
│   ├── missions_sous_dotees.php
│   ├── planning.php
│   ├── planning_missions.php
│   ├── toutes_affectations.php
│   └── toutes_missions.php
├── actions/
│   ├── affecter.php
│   ├── create_benevole.php
│   ├── create_mission.php
│   ├── desaffecter.php
│   ├── login.php
│   └── save_disponibilite.php
├── css/
│   └── style.css
├── sql/
│   ├── .htaccess
│   ├── schema.sql
│   └── demo.sql
├── sauvegardes/
│   ├── .htaccess
│   ├── BACKUP_RESTORE.md
│   └── scripts/
│       ├── backup_db.sh
│       ├── restore_db.sh
│       └── check_restore.sh
├── .github/
│   └── workflows/
│       └── ci.yml
├── .env.example
├── .gitignore
├── README.md
├── SPRINT_BACKLOG.md
├── DAILY_LOG.md
├── TRACABILITE_IA.md
├── TEST.md
├── ScrumTP.md
├── ContextIA1.md
└── rapport.md
```

Note : `.env` existe localement mais n’est pas versionné.

### 8.3 Séparation des responsabilités

| Zone | Responsabilité |
|---|---|
| `config/db.php` | Connexion PDO et lecture `.env` |
| `includes/functions.php` | Règles métier partagées |
| `pages/` | Affichage et formulaires |
| `actions/` | Traitements POST et redirections |
| `sql/` | Schéma et données de démonstration |
| `.github/workflows/ci.yml` | Pipeline CI/CD |
| `sauvegardes/` | Procédure et scripts backup/restauration |

---

## 9. Modèle de données

La base contient les tables principales suivantes :

| Table | Rôle |
|---|---|
| `role` | Rôles applicatifs |
| `user` | Comptes utilisateurs |
| `volunteer` | Profils bénévoles |
| `skill` | Compétences |
| `volunteer_skill` | Compétences des bénévoles |
| `zone` | Zones du festival |
| `mission` | Missions à couvrir |
| `availability` | Disponibilités |
| `assignment` | Affectations |
| `audit_log` | Journal d’audit |

TODO: insérer une capture ou un schéma relationnel.

```md
![Schéma de base de données](captures/TODO-schema-bdd.png)
```

---

## 10. Règles métier implémentées

| Règle | Implémentation | Preuve attendue |
|---|---|---|
| Fin disponibilité > début | `actions/save_disponibilite.php` + contrainte SQL | TODO |
| Refus des disponibilités incohérentes | `actions/save_disponibilite.php` | TODO |
| Disponibilité couvre toute la mission | `actions/affecter.php` | TODO |
| Pas de conflit horaire entre missions | `actions/affecter.php` | TODO |
| Capacité non dépassée | `actions/affecter.php` | TODO |
| Unicité affectation mission/bénévole | contrainte SQL + validation PHP | TODO |
| Désaffectation tracée | `actions/desaffecter.php` + `audit_log` | TODO |
| Missions sous-dotées visibles | `dashboard.php`, `missions_sous_dotees.php` | TODO |

---

## 11. Sécurité minimale

Mesures appliquées :

- connexion PDO ;
- requêtes préparées ;
- mots de passe vérifiés avec `password_verify()` ;
- mots de passe de démonstration hashés en bcrypt ;
- `.env` non versionné ;
- sorties HTML échappées avec `htmlspecialchars()` ;
- pages protégées par session ;
- accès contrôlé selon les rôles ;
- actions de modification placées dans `actions/` ;
- dossier `sql/` non servi par Apache ;
- fichiers `.md` non servis par Apache ;
- dossier `sauvegardes/` non servi par Apache.

TODO: insérer la preuve du blocage Apache.

```md
![Accès interdit aux fichiers sensibles](captures/TODO-403-sql-md-sauvegardes.png)
```

---

## 12. Jeu de données de démonstration

Le fichier `sql/demo.sql` fournit :

- 12 utilisateurs ;
- 8 bénévoles actifs ;
- au moins 6 missions ;
- plusieurs zones ;
- plusieurs compétences ;
- des disponibilités ;
- des affectations valides ;
- des missions complètes ;
- des missions sous-dotées ;
- des comptes de test.

### Comptes de démonstration

Mot de passe commun : `password`.

| Rôle | Email |
|---|---|
| Responsable | `claire.responsable@festival.test` |
| Coordinateur | `nora.coordinateur@festival.test` |
| Coordinateur | `lucas.coordinateur@festival.test` |
| Bénévole | `emma.martin@festival.test` |
| Bénévole | `hugo.bernard@festival.test` |
| Admin | `admin@festival.test` |

TODO: confirmer les compteurs finaux après import propre.

```sql
SELECT COUNT(*) FROM user;
SELECT COUNT(*) FROM volunteer;
SELECT COUNT(*) FROM mission;
SELECT COUNT(*) FROM assignment;
```

```md
![Données de démonstration](captures/TODO-demo-sql-counts.png)
```

---

## 13. Fonctionnalités livrées

### 13.1 Authentification

- connexion par email ;
- vérification du mot de passe ;
- création de session ;
- redirection selon rôle ;
- déconnexion.

```md
![Page de connexion](captures/TODO-login.png)
```

### 13.2 Parcours bénévole

Fonctionnalités :

- consulter son planning ;
- consulter ses disponibilités ;
- ajouter une disponibilité.

Pages :

- `pages/planning.php`
- `pages/disponibilites.php`

```md
![Planning bénévole](captures/TODO-planning-benevole.png)
![Disponibilités bénévole](captures/TODO-disponibilites.png)
```

### 13.3 Parcours coordinateur

Fonctionnalités :

- voir ses missions ;
- créer une mission ;
- voir les affectations d’une mission ;
- affecter un bénévole ;
- désaffecter un bénévole ;
- consulter le journal.

Pages/actions :

- `pages/mes_missions.php`
- `pages/creer_mission.php`
- `pages/mes_affectations.php`
- `actions/affecter.php`
- `actions/desaffecter.php`
- `pages/audit_log.php`

```md
![Missions coordinateur](captures/TODO-mes-missions.png)
![Affectations mission](captures/TODO-mes-affectations.png)
![Journal audit](captures/TODO-audit-log.png)
```

### 13.4 Parcours responsable

Fonctionnalités :

- dashboard global ;
- visualisation des missions sous-dotées ;
- filtre par zone/statut ;
- vue de toutes les missions ;
- vue de toutes les affectations.

Pages :

- `pages/dashboard.php`
- `pages/missions_sous_dotees.php`
- `pages/toutes_missions.php`
- `pages/toutes_affectations.php`

```md
![Dashboard responsable](captures/TODO-dashboard.png)
![Missions sous-dotées](captures/TODO-missions-sous-dotees.png)
![Toutes les missions](captures/TODO-toutes-missions.png)
![Toutes les affectations](captures/TODO-toutes-affectations.png)
```

---

## 14. Rapport de tests — mission 18 / T3

Les tests détaillés sont listés dans `TEST.md`.

### 14.1 Synthèse des tests

| Domaine | Résultat attendu | Statut | Preuve |
|---|---|---|---|
| Import schema/demo | Base chargée sans erreur | TODO | TODO |
| Authentification | Redirections selon rôle | TODO | TODO |
| Disponibilités | Création valide et refus incohérent | TODO | TODO |
| Affectation | Disponible accepté, conflit refusé | TODO | TODO |
| Capacité | Mission pleine refusée | TODO | TODO |
| Désaffectation | Annulation et audit | TODO | TODO |
| Planning | Planning bénévole et mission visibles | TODO | TODO |
| Sous-dotation | Missions incomplètes visibles et filtrables | TODO | TODO |
| CI/CD | Pipeline vert | TODO | TODO |
| Sauvegarde | Backup/restauration vérifiables | TODO | TODO |

### 14.2 Preuves de tests à insérer

```md
![Test conflit horaire](captures/TODO-test-conflit.png)
![Test capacité dépassée](captures/TODO-test-capacite.png)
![Test disponibilité invalide](captures/TODO-test-disponibilite-invalide.png)
![Test CI vert](captures/TODO-ci-vert.png)
![Test sauvegarde](captures/TODO-backup-check.png)
```

### 14.3 Critère de validation T3

T3 est validé si :

- `sql/demo.sql` fournit un jeu de données reproductible ;
- le parcours principal est démontrable sans manipulation cachée ;
- les affectations respectent disponibilité, conflit et capacité ;
- les missions sous-dotées sont visibles ;
- les pages sont protégées par rôle ;
- le CI/CD est vert.

---

## 15. CI/CD

Le projet utilise GitHub Actions.

Le pipeline vérifie :

1. récupération du code ;
2. installation de PHP 8.2 ;
3. extension `pdo_mysql` ;
4. absence de `.env` versionné ;
5. création d’un `.env` de test ;
6. structure attendue ;
7. syntaxe PHP ;
8. démarrage MariaDB ;
9. import `schema.sql` ;
10. import `demo.sql` ;
11. démarrage du serveur PHP ;
12. réponse de `login.php`.

Badge attendu :

```md
![CI/CD](https://github.com/SBBDMT/Planificateur_Benevole/actions/workflows/ci.yml/badge.svg)
```

TODO: insérer la capture du pipeline vert.

```md
![Pipeline CI/CD vert](captures/TODO-ci-github-actions.png)
```

---

## 16. Sauvegarde et restauration

La mission T5 est couverte par :

- `sauvegardes/BACKUP_RESTORE.md`
- `sauvegardes/scripts/backup_db.sh`
- `sauvegardes/scripts/restore_db.sh`
- `sauvegardes/scripts/check_restore.sh`

Un cron serveur exécute une sauvegarde quotidienne à 17h15 UTC :

```cron
15 17 * * * root cd /var/www/html/Planificateur_Benevole && sauvegardes/scripts/backup_db.sh >> /var/log/planificateur_benevole_backup.log 2>&1
```

Les dumps sont générés dans :

```text
sauvegardes/backups/database/
```

Ils ne doivent pas être versionnés.

Mesures de sécurité :

- `sauvegardes/` interdit par Apache ;
- dumps ignorés par Git ;
- scripts sans secret en dur ;
- lecture de `.env`.

TODO: insérer les preuves.

```md
![Cron sauvegarde](captures/TODO-cron-backup.png)
![Backup SHA256](captures/TODO-backup-sha256.png)
```

---

## 17. Sprint Review

### 17.1 Scénario démontré

1. Connexion comme coordinateur.
2. Création d’une mission.
3. Consultation des missions du coordinateur.
4. Affectation d’un bénévole disponible.
5. Tentative d’affectation impossible à cause d’un conflit.
6. Refus de l’affectation par l’application.
7. Consultation du planning bénévole.
8. Connexion comme responsable.
9. Visualisation des missions sous-dotées.
10. Filtre des missions sous-dotées.
11. Consultation de toutes les missions.
12. Consultation de toutes les affectations.
13. Présentation de la CI/CD.
14. Présentation du README et de la procédure de sauvegarde.

### 17.2 Résultat de la review

TODO: compléter après démonstration.

- Ce qui a été accepté :
  - TODO
- Ce qui a été discuté :
  - TODO
- Ce qui reste hors périmètre :
  - TODO

---

## 18. Definition of Done

| Dimension | Condition minimale | Statut | Preuve |
|---|---|---|---|
| Fonctionnel | Critères MVP démontrables | TODO | TODO |
| Code | Versionné, lisible, sans secret | TODO | TODO |
| Qualité | Tests essentiels exécutés | TODO | `TEST.md` |
| DevOps | Pipeline CI/CD vert | TODO | TODO |
| Données | 8+ bénévoles et 6+ missions | TODO | `sql/demo.sql` |
| Documentation | README, procédure, comptes test | TODO | `README.md`, `BACKUP_RESTORE.md` |
| Sécurité | Entrées validées, rôles protégés | TODO | TODO |
| Produit | Parcours PO sans manipulation cachée | TODO | TODO |

---

## 19. Collaboration

TODO: compléter avec la réalité des commits et de l’organisation de l’équipe.

| Membre | Contributions principales | Validation |
|---|---|---|
| Aurélien | Backend, règles métier, SQL, actions | TODO |
| Jonathan | Pages, formulaires, parcours utilisateur | TODO |
| Sophian | CI/CD, documentation, seed, sauvegarde | TODO |

Éléments montrant le travail partagé :

- commits Git ;
- daily log ;
- répartition du backlog ;
- revue intermédiaire ;
- corrections croisées.

```md
![Historique Git](captures/TODO-git-history.png)
```

---

## 20. Usage de l’IA

L’usage de l’IA est tracé dans `TRACABILITE_IA.md`.

### 20.1 Synthèse

| Type d’usage | Exemple | Vérification humaine |
|---|---|---|
| Génération de code | TODO | TODO |
| Documentation | TODO | TODO |
| Tests | TODO | TODO |
| DevOps | TODO | TODO |

### 20.2 Recul critique

Ce que l’IA a bien aidé à faire :

TODO

Ce qui a nécessité une correction humaine :

TODO

Ce que l’équipe aurait fait différemment :

TODO

Position de l’équipe :

TODO

---

## 21. Risques, arbitrages et limites

| Risque | Arbitrage retenu |
|---|---|
| Algorithme d’optimisation trop ambitieux | Affectation manuelle contrôlée |
| Interface trop ambitieuse | Tableaux simples et lisibles |
| CI/CD instable | Pipeline minimal mais utile |
| Données insuffisantes | `demo.sql` reproductible |
| Sécurité des fichiers sensibles | Blocage Apache sur `sql/`, `.md`, `sauvegardes/` |

Limites connues :

- pas d’optimisation automatique ;
- pas d’export CSV/PDF ;
- pas de notifications ;
- pas de gestion avancée des fuseaux horaires ;
- TODO: ajouter autres limites si nécessaire.

---

## 22. Estimation du prochain sprint

Le prochain sprint pourrait porter sur des extensions hors MVP et sur l’amélioration de l’expérience utilisateur.

| Priorité | Élément | Description | Estimation | Valeur |
|---|---|---|---:|---|
| 1 | Amélioration messages d’erreur | Remplacer les `die()` restants par des messages intégrés à l’interface | 2 pts | Qualité UX |
| 2 | Suggestion d’affectation | Proposer les bénévoles disponibles et compétents pour une mission | 5 pts | Gain temps coordinateur |
| 3 | Export planning | Export CSV/PDF du planning bénévole ou mission | 3 pts | Exploitation terrain |
| 4 | Notification simulée | Message visible après modification d’affectation | 2 pts | Communication |
| 5 | Tableau responsable enrichi | Graphiques simples ou filtres supplémentaires | 3 pts | Pilotage |
| 6 | Tests automatisés métier | Scripts de tests SQL/PHP rejouables | 5 pts | Fiabilité |
| 7 | Documentation utilisateur | Guide court par rôle | 2 pts | Adoption |

Objectif possible du prochain sprint :

> Améliorer l’exploitation opérationnelle du planning en accélérant les affectations, en facilitant les exports et en renforçant les tests automatisés.

---

## 23. Sprint Retrospective

| Continue | Stop | Start |
|---|---|---|
| TODO | TODO | TODO |
| TODO | TODO | TODO |

Action d’amélioration retenue :

TODO: une action concrète, mesurable et réalisable au prochain sprint.

Exemple :

> Ajouter une checklist de revue avant merge pour vérifier droits d’accès, `.env`, syntaxe PHP et scénario de démonstration.

---

## 24. Conclusion

Le projet livre un MVP fonctionnel permettant de gérer les missions, les bénévoles, les disponibilités et les affectations d’un festival. Les règles prioritaires du domaine sont couvertes : disponibilité complète, absence de conflit horaire, capacité non dépassée et visibilité des missions sous-dotées.

Le projet respecte les contraintes techniques du TP : PHP vanilla, HTML/CSS, PDO, MariaDB, configuration externalisée, CI/CD minimale, documentation, données de démonstration et procédure de sauvegarde/restauration.

TODO: ajouter conclusion finale après tests et Sprint Review.

---

## 25. Annexes

### Annexe A — Documents du projet

- README : `README.md`
- Backlog : `SPRINT_BACKLOG.md`
- Tests : `TEST.md`
- Daily log : `DAILY_LOG.md`
- Traçabilité IA : `TRACABILITE_IA.md`
- Procédure sauvegarde : `sauvegardes/BACKUP_RESTORE.md`
- CI/CD : `.github/workflows/ci.yml`

### Annexe B — Captures à insérer

| Capture | Fichier attendu | Statut |
|---|---|---|
| Login | `captures/TODO-login.png` | ⬜ |
| Dashboard responsable | `captures/TODO-dashboard.png` | ⬜ |
| Missions sous-dotées | `captures/TODO-missions-sous-dotees.png` | ⬜ |
| Refus conflit | `captures/TODO-test-conflit.png` | ⬜ |
| Refus capacité | `captures/TODO-test-capacite.png` | ⬜ |
| Planning bénévole | `captures/TODO-planning-benevole.png` | ⬜ |
| Journal audit | `captures/TODO-audit-log.png` | ⬜ |
| CI/CD vert | `captures/TODO-ci-github-actions.png` | ⬜ |
| Backup/restauration | `captures/TODO-backup-check.png` | ⬜ |

