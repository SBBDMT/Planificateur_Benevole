# Brief Codex — Planificateur de bénévoles pour un événement culturel

## 1. Objectif du document

Ce fichier Markdown sert de **document de pilotage pour Codex** afin de développer, corriger ou compléter l’application **Planificateur de bénévoles**.

Le projet correspond à un mini-projet Scrum de 15 heures en équipe de 3 personnes. L’objectif est de livrer un **MVP démontrable** permettant de gérer les missions, les bénévoles, les disponibilités, les affectations sans conflit et la visibilité des missions incomplètes.

---

## 2. Contexte métier

Une association prépare un festival sur une journée. Elle doit affecter des bénévoles à différentes missions : accueil, logistique, médiation, etc.

Aujourd’hui, les affectations sont gérées dans des tableurs séparés, ce qui provoque :

- des doublons ;
- des créneaux sous-dotés ;
- des conflits d’horaires ;
- des modifications difficiles à communiquer.

L’application doit construire un planning cohérent, éviter les conflits et rendre visibles les besoins non couverts.

---

## 3. Contraintes techniques du projet

### Stack imposée

Le projet utilise uniquement :

- HTML ;
- CSS ;
- PHP ;
- PDO ;
- MariaDB / MySQL.

### Contraintes importantes

- Ne pas ajouter de JavaScript sauf demande explicite de l’équipe.
- Ne pas ajouter Composer si le projet n’en utilise pas déjà.
- Ne pas ajouter de framework lourd comme Laravel ou Symfony.
- Ne pas modifier inutilement l’architecture existante.
- Ne pas versionner le fichier `.env`.
- Utiliser `.env.example` comme modèle de configuration.
- Les actions de modification doivent passer par le dossier `actions/`.
- Les pages d’affichage doivent rester dans le dossier `pages/`.
- Les règles métier doivent être centralisées dans `includes/functions.php`.
- La connexion à la base doit être centralisée dans `config/db.php`.

---

## 4. Structure actuelle du projet

Codex doit respecter cette structure :

```text
planificateur-benevoles/
│
├── index.php                          # Routing central, redirige selon rôle
├── login.php                          # Page de connexion
├── logout.php                         # Déconnexion + destroy session
│
├── config/
│   └── db.php                         # Connexion PDO MySQL/MariaDB via .env
│
├── includes/
│   ├── header.php                     # Nav adaptée au rôle + vérif session
│   ├── footer.php                     # Pied de page
│   └── functions.php                  # Règles métier
│
├── pages/
│   ├── planning.php                   # Bénévole : mon planning personnel
│   ├── disponibilites.php             # Bénévole : mes disponibilités
│   ├── mes_missions.php               # Coordinateur : ses missions
│   ├── creer_mission.php              # Coordinateur : formulaire création mission
│   ├── mes_affectations.php           # Coordinateur : ses affectations
│   ├── affecter.php                   # Coordinateur : formulaire affectation
│   ├── dashboard.php                  # Responsable : vue globale + sous-dotées
│   ├── toutes_missions.php            # Responsable : toutes les missions
│   └── toutes_affectations.php        # Responsable : toutes les affectations
│
├── actions/
│   ├── login.php                      # Authentification + création session
│   ├── create_mission.php             # Créer mission + compétence éventuelle
│   ├── create_benevole.php            # Créer profil bénévole
│   ├── save_disponibilite.php         # Enregistrer disponibilité + validation
│   ├── affecter.php                   # Affecter avec règles métier + log
│   └── desaffecter.php                # Désaffecter + log audit
│
├── css/
│   └── style.css
│
├── sql/
│   ├── schema.sql
│   └── demo.sql                       # Jeu de données : au moins 8 bénévoles, 6 missions
│
├── .github/
│   └── workflows/
│       └── ci.yml                     # CI/CD GitHub Actions
│
├── .env.example
├── .env                               # Non versionné
└── .gitignore
```

---

## 5. Utilisateurs et rôles

| Rôle | Description | Accès attendu |
|---|---|---|
| Bénévole | Personne affectée aux missions | Planning personnel, disponibilités |
| Coordinateur | Crée des missions et affecte des bénévoles | Ses missions, ses affectations, formulaire d’affectation |
| Responsable | Supervise le festival | Dashboard global, missions sous-dotées, toutes les affectations |
| Admin | Rôle technique éventuel | Gestion ou supervision technique si déjà présent |

Codex doit vérifier les droits d’accès avant d’afficher une page ou d’exécuter une action.

---

## 6. MVP obligatoire

Le MVP doit permettre de :

1. créer des missions avec :
   - lieu ;
   - créneau horaire ;
   - capacité ;
   - compétence éventuelle ;
2. enregistrer les disponibilités des bénévoles ;
3. affecter un bénévole à une mission sans chevauchement ;
4. afficher le planning par bénévole et par mission ;
5. mettre en évidence les missions incomplètes ;
6. permettre une désaffectation ;
7. fournir un jeu de données de démonstration ;
8. fournir une CI/CD minimale et une documentation d’exploitation.

---

## 7. Hors périmètre du MVP

Ces éléments sont des extensions uniquement après acceptation du MVP :

- suggestion automatique d’affectation ;
- badge ou feuille de présence ;
- export CSV ou PDF ;
- notification simulée de changement.

Ne pas prioriser ces éléments tant que le parcours principal n’est pas fiable.

---

## 8. Règles métier à implémenter ou préserver

### 8.1 Mission

Une mission doit contenir au minimum :

- un titre ou nom ;
- un lieu ;
- une date / heure de début ;
- une date / heure de fin ;
- une capacité attendue ;
- éventuellement une compétence requise ;
- éventuellement une zone ;
- un coordinateur propriétaire.

### 8.2 Disponibilité

Une disponibilité doit :

- appartenir à un bénévole ;
- avoir une date / heure de début ;
- avoir une date / heure de fin ;
- avoir une fin strictement après le début ;
- éviter les doublons ou créneaux incohérents si possible.

### 8.3 Affectation

Une affectation est autorisée uniquement si :

- le bénévole existe ;
- la mission existe ;
- le bénévole est disponible sur toute la durée de la mission ;
- le bénévole n’a pas déjà une mission qui chevauche ce créneau ;
- la capacité de la mission n’est pas atteinte ;
- la compétence requise est respectée si le modèle de données gère les compétences.

### 8.4 Désaffectation

La désaffectation doit :

- supprimer ou annuler l’affectation selon le modèle existant ;
- enregistrer un log d’audit si la table existe ;
- rediriger vers une page cohérente après traitement.

### 8.5 Missions incomplètes

Une mission est incomplète si :

```text
nombre_affectations_valides < capacite_mission
```

Le responsable doit pouvoir les voir clairement dans `pages/dashboard.php`.

---

## 9. Sécurité minimale attendue

Codex doit respecter ces règles :

- utiliser PDO avec requêtes préparées ;
- ne jamais concaténer directement les entrées utilisateur dans une requête SQL ;
- valider les champs obligatoires côté PHP ;
- protéger les pages par session ;
- vérifier le rôle utilisateur avant accès ;
- utiliser `password_verify()` pour vérifier les mots de passe ;
- utiliser `password_hash()` pour créer de nouveaux mots de passe ;
- échapper les sorties HTML avec `htmlspecialchars()` ;
- ne pas exposer les erreurs SQL brutes à l’utilisateur final ;
- ne pas stocker de secret dans Git.

Les mots de passe de démonstration peuvent utiliser un hash bcrypt du mot de passe `password`.

Exemple de hash déjà utilisé dans les données de test :

```text
$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
```

Ce hash correspond au mot de passe de démonstration `password`.

---

## 10. Backlog ordonné du sprint unique

Le projet doit être traité comme un **seul sprint de 15 heures**.

| Priorité | ID | Élément | Points indicatifs | Dépendances |
|---:|---|---|---:|---|
| 1 | T0 | Initialiser dépôt, structure projet et conventions | 2 | Aucune |
| 2 | T1 | Connexion PDO via `.env` | 2 | T0 |
| 3 | T2 | Vérifier ou compléter `schema.sql` | 3 | T1 |
| 4 | US1-A | Créer une mission avec lieu, horaire, capacité | 2 | T2 |
| 5 | US1-B | Ajouter compétence éventuelle à une mission | 1 | US1-A |
| 6 | US2-A | Créer ou gérer un profil bénévole | 1 | T2 |
| 7 | US2-B | Enregistrer les disponibilités | 2 | US2-A |
| 8 | US2-C | Refuser les disponibilités incohérentes | 2 | US2-B |
| 9 | US3-A | Affecter un bénévole à une mission | 3 | US1 + US2 |
| 10 | US3-B | Refuser une affectation avec conflit horaire | 3 | US3-A |
| 11 | US3-C | Refuser une affectation si capacité atteinte | 2 | US3-A |
| 12 | US3-D | Permettre la désaffectation + log | 2 | US3-A |
| 13 | US4-A | Afficher planning d’un bénévole | 2 | US3-A |
| 14 | US4-B | Afficher planning par mission | 2 | US3-A |
| 15 | US5-A | Calculer le taux de couverture | 2 | US3-A |
| 16 | US5-B | Identifier les missions sous-dotées | 3 | US5-A |
| 17 | US5-C | Ajouter filtre ou affichage clair des missions incomplètes | 2 | US5-B |
| 18 | T3 | Créer ou vérifier `sql/demo.sql` | 2 | T2 |
| 19 | T4 | Vérifier CI/CD GitHub Actions | 2 | T0 |
| 20 | T5 | Rédiger README et procédure d’exploitation | 2 | MVP stable |
| 21 | REV | Préparer scénario de Sprint Review | 1 | MVP stable |

Les points sont indicatifs et ne mesurent pas la productivité individuelle.

---

## 11. Découpage temporel du sprint de 15h

| Temps | Séquence | Résultat attendu |
|---|---|---|
| H0 → H1h15 | Sprint Planning + backlog | Sprint Goal, stories ordonnées, critères et tâches |
| H1h15 → H2h30 | Conception légère + squelette | Architecture minimale, dépôt, environnement, pipeline initial |
| H2h30 → H5h30 | Construction verticale 1 | Premier parcours démontrable |
| H5h30 → H5h45 | Daily Scrum | Obstacles visibles et plan adapté |
| H5h45 → H8h45 | Construction verticale 2 | Règles métier, tests, exploitation |
| H8h45 → H9h15 | Revue intermédiaire PO | Feedback et réduction éventuelle du périmètre |
| H9h15 → H12h15 | Stabilisation | MVP, CI/CD, données, documentation |
| H12h15 → H13h45 | Recette + préparation | Scénarios de revue et correction des défauts |
| H13h45 → H14h30 | Sprint Review | Démonstration et acceptation |
| H14h30 → H15 | Rétrospective | Amélioration concrète et mesurable |

---

## 12. Tâches attendues de Codex

### 12.1 Avant toute modification

Codex doit :

1. lire la structure du projet ;
2. lire `sql/schema.sql` ;
3. lire `includes/functions.php` ;
4. lire `config/db.php` ;
5. vérifier le workflow `.github/workflows/ci.yml` ;
6. vérifier `README.md` ;
7. ne modifier que les fichiers nécessaires.

### 12.2 Connexion base de données

Vérifier que `config/db.php` :

- charge les variables de `.env` ;
- crée une connexion PDO ;
- active les exceptions PDO ;
- utilise `utf8mb4` ;
- ne contient pas de mot de passe codé en dur.

### 12.3 Authentification

Vérifier que :

- `actions/login.php` lit l’utilisateur par email ;
- le mot de passe est vérifié par `password_verify()` ;
- la session stocke au minimum :
  - `user_id` ;
  - `role_id` ;
  - `name` ;
- `logout.php` détruit correctement la session ;
- `index.php` redirige selon le rôle.

### 12.4 Pages par rôle

Vérifier que chaque page :

- commence par une vérification de session ;
- vérifie le rôle attendu ;
- inclut `header.php` et `footer.php` ;
- affiche uniquement les données autorisées.

### 12.5 Actions POST

Les fichiers du dossier `actions/` doivent :

- refuser les accès non POST quand il s’agit d’une modification ;
- valider les champs ;
- appeler les fonctions métier ;
- rediriger après traitement ;
- éviter l’affichage direct de contenu métier.

---

## 13. CI/CD déjà attendu

Le projet utilise GitHub Actions avec MariaDB.

Le workflow doit vérifier :

1. récupération du code ;
2. installation de PHP 8.2 avec `pdo_mysql` ;
3. absence de `.env` versionné ;
4. création d’un `.env` de test ;
5. structure attendue du projet ;
6. syntaxe de tous les fichiers PHP avec `php -l` ;
7. démarrage d’une base MariaDB de test ;
8. import de `sql/schema.sql` ;
9. import de `sql/demo.sql` ;
10. démarrage du serveur PHP intégré ;
11. vérification que `login.php` répond.

Ne pas remplacer le workflow par un template Composer si le projet n’utilise pas Composer.

Le badge README attendu pour le dépôt est :

```md
![CI/CD](https://github.com/SBBDMT/Planificateur_Benevole/actions/workflows/ci.yml/badge.svg)
```

---

## 14. Jeu de données de démonstration

Le fichier `sql/demo.sql` doit contenir au minimum :

- 8 bénévoles ;
- 6 missions ;
- des disponibilités ;
- des affectations valides ;
- au moins une situation de mission incomplète ;
- idéalement des compétences, zones et logs si les tables existent.

Les comptes de démonstration doivent être simples à tester.

Mot de passe de démonstration recommandé :

```text
password
```

Exemples de comptes :

| Rôle | Email | Mot de passe |
|---|---|---|
| Responsable | claire.responsable@festival.test | password |
| Coordinateur | nora.coordinateur@festival.test | password |
| Coordinateur | lucas.coordinateur@festival.test | password |
| Bénévole | emma.martin@festival.test | password |
| Bénévole | hugo.bernard@festival.test | password |
| Admin | admin@festival.test | password |

---

## 15. Scénario de démonstration Sprint Review

Le parcours de démonstration doit permettre de :

1. se connecter comme coordinateur ;
2. créer une mission ;
3. consulter ses missions ;
4. affecter un bénévole disponible ;
5. tenter une affectation impossible à cause d’un conflit ;
6. vérifier que l’application refuse l’affectation ;
7. consulter le planning du bénévole ;
8. se connecter comme responsable ;
9. voir les missions sous-dotées ;
10. montrer que la CI/CD passe ;
11. montrer le README et la procédure de lancement.

---

## 16. Definition of Done

Un élément est terminé uniquement si :

| Dimension | Condition minimale |
|---|---|
| Fonctionnel | Les critères d’acceptation sont démontrables avec des données connues |
| Code | Code versionné, lisible, sans secret, erreurs principales gérées |
| Qualité | Syntaxe PHP valide, règles importantes testables manuellement ou automatiquement |
| DevOps | Pipeline CI/CD vert, configuration externalisée, lancement reproductible |
| Documentation | README clair : installation, lancement, architecture, comptes de test, limites |
| Sécurité | Entrées validées, requêtes préparées, mots de passe hashés, sorties échappées |
| Produit | Le PO peut utiliser le parcours principal sans manipulation cachée |

---

## 17. Critères d’évaluation du TP

Le projet est évalué sur :

| Critère | Indicateurs | Poids |
|---|---|---:|
| Maîtrise de Scrum | Sprint Goal, backlog ordonné, transparence, adaptation, rétrospective | 20% |
| Valeur produit | Parcours cohérent, critères satisfaits, démonstration convaincante | 20% |
| Qualité technique | Architecture, lisibilité, tests, erreurs, sécurité | 20% |
| DevOps | CI/CD, reproductibilité, configuration, santé, sauvegarde ou restauration | 20% |
| Collaboration | Travail réellement partagé entre les trois profils | 10% |
| Réflexivité et IA | Traçabilité, vérification, recul critique | 10% |

---

## 18. Risques et arbitrages

| Risque | Arbitrage attendu |
|---|---|
| Algorithme d’optimisation trop ambitieux | Garder l’affectation manuelle contrôlée |
| Créneaux ou fuseaux horaires mal gérés | Réduire le périmètre et documenter clairement les limites |
| Planning incomplet faute de données | Produire rapidement un jeu de données reproductible |
| Interface trop ambitieuse | Privilégier des tableaux simples et lisibles |
| CI/CD instable | Garder les vérifications minimales : syntaxe, SQL, démarrage login |

Principe directeur :

> Mieux vaut un petit incrément réellement terminé qu’un grand périmètre partiellement réalisé.

---

## 19. Instructions de réponse pour Codex

Quand Codex intervient sur le projet :

1. expliquer brièvement les fichiers modifiés ;
2. ne pas modifier les fichiers sans rapport avec la demande ;
3. ne pas casser le CI/CD ;
4. fournir les commandes de test à lancer ;
5. signaler les hypothèses prises ;
6. signaler les limites restantes ;
7. préférer des corrections simples et fiables.

Commandes de vérification recommandées :

```bash
php -v
find . -type f -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
mariadb -u root -p planificateur_benevoles < sql/schema.sql
mariadb -u root -p planificateur_benevoles < sql/demo.sql
php -S 127.0.0.1:8000 -t .
```

---

## 20. Prompt court utilisable directement dans Codex

```text
Tu travailles sur un projet PHP/HTML/CSS/MariaDB nommé Planificateur_Benevole.
Respecte strictement la structure existante du dépôt.
N’ajoute pas JavaScript, Composer, Laravel ou Symfony.
Centralise les règles métier dans includes/functions.php.
Utilise PDO et des requêtes préparées.
Protège les pages par session et rôle.
Les actions POST doivent être dans actions/ et rediriger après traitement.
Le MVP doit permettre : créer missions, gérer disponibilités, affecter sans conflit, afficher planning, voir missions sous-dotées, désaffecter.
Conserve le CI/CD GitHub Actions existant et ne versionne jamais .env.
Avant de modifier, lis schema.sql, config/db.php, includes/functions.php et la page/action concernée.
Après modification, indique les fichiers changés et les commandes de test.
```
