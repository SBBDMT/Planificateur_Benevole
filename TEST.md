# T3 — Tests essentiels et vérification du jeu de démonstration

## Objectif

Valider que le MVP est démontrable avec des données connues, conformément au Sprint Goal :

> Créer des missions, déclarer les disponibilités, affecter sans conflit et afficher les besoins non couverts.

---

## 1. Préconditions

- `sql/schema.sql` importé.
- `sql/demo.sql` importé.
- `.env` configuré.
- CI/CD GitHub Actions vert.
- Aucun fichier `.env` versionné.

Comptes de test, mot de passe : `password`.

| Rôle | Email |
|---|---|
| Responsable | claire.responsable@festival.test |
| Coordinateur | nora.coordinateur@festival.test |
| Coordinateur | lucas.coordinateur@festival.test |
| Bénévole | emma.martin@festival.test |
| Bénévole | hugo.bernard@festival.test |
| Admin | admin@festival.test |

---

## 2. Tests données de démonstration

| ID | Test | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|
| T3-01 | Import schema | Importer `sql/schema.sql` | Base créée sans erreur | ⬜ |
| T3-02 | Import demo | Importer `sql/demo.sql` | Données chargées sans erreur | ⬜ |
| T3-03 | Comptes test | Vérifier les comptes `@festival.test` | Connexion possible avec `password` | ⬜ |
| T3-04 | Bénévoles | Compter les bénévoles | Au moins 8 bénévoles | ⬜ |
| T3-05 | Missions | Compter les missions | Au moins 6 missions | ⬜ |
| T3-06 | Sous-dotation | Vérifier les missions incomplètes | Au moins 1 mission sous-dotée, attendu demo : 3 | ⬜ |
| T3-07 | Affectations valides | Vérifier les affectations demo | Les affectations ne dépassent pas les capacités | ⬜ |

---

## 3. Tests authentification et rôles

| ID | Test | Compte | Résultat attendu | Statut |
|---|---|---|---|---|
| T3-08 | Connexion responsable | Claire | Redirection vers `dashboard.php` | ⬜ |
| T3-09 | Connexion coordinateur | Nora ou Lucas | Redirection vers `mes_missions.php` | ⬜ |
| T3-10 | Connexion bénévole | Emma | Redirection vers `planning.php` | ⬜ |
| T3-11 | Protection rôle | Emma ouvre `dashboard.php` | Accès refusé ou redirection | ⬜ |
| T3-12 | Protection rôle | Claire ouvre `mes_missions.php` | Accès refusé ou redirection | ⬜ |

---

## 4. Tests disponibilités

| ID | Test | Compte | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|---|
| T3-13 | Ajouter disponibilité valide | Emma | Créer une disponibilité début < fin | Disponibilité enregistrée | ⬜ |
| T3-14 | Refuser disponibilité incohérente | Emma | Créer une disponibilité fin <= début | Refus ou message d’erreur | ⬜ |
| T3-15 | Voir ses disponibilités | Emma | Ouvrir `disponibilites.php` | Liste affichée | ⬜ |

---

## 5. Tests affectation contrôlée

| ID | Test | Compte | Résultat attendu | Statut |
|---|---|---|---|---|
| T3-16 | Affecter un bénévole disponible | Coordinateur | Affectation créée, couverture mise à jour | ⬜ |
| T3-17 | Refuser bénévole indisponible | Coordinateur | Message d’erreur, aucune affectation créée | ⬜ |
| T3-18 | Refuser conflit horaire | Coordinateur | Message de conflit, aucune affectation créée | ⬜ |
| T3-19 | Refuser capacité dépassée | Coordinateur | Mission pleine, affectation impossible | ⬜ |
| T3-20 | Refuser doublon même mission | Coordinateur | Même bénévole non affecté deux fois à la même mission | ⬜ |

---

## 6. Tests désaffectation et audit

| ID | Test | Compte | Résultat attendu | Statut |
|---|---|---|---|---|
| T3-21 | Désaffecter bénévole | Coordinateur | Affectation annulée ou passée `cancelled` | ⬜ |
| T3-22 | Journal affectation | Coordinateur / Responsable | Action visible dans `audit_log.php` | ⬜ |
| T3-23 | Couverture après désaffectation | Coordinateur | Mission repasse sous-dotée si nécessaire | ⬜ |

---

## 7. Tests planning

| ID | Test | Compte | Résultat attendu | Statut |
|---|---|---|---|---|
| T3-24 | Planning bénévole | Emma | Missions affectées affichées chronologiquement | ⬜ |
| T3-25 | Planning par mission | Coordinateur / Responsable | Bénévoles affectés visibles | ⬜ |

---

## 8. Tests responsable et missions incomplètes

| ID | Test | Compte | Résultat attendu | Statut |
|---|---|---|---|---|
| T3-26 | Dashboard responsable | Claire | Stats globales visibles | ⬜ |
| T3-27 | Missions sous-dotées | Claire | Missions incomplètes mises en évidence | ⬜ |
| T3-28 | Filtre par zone | Claire | Liste filtrée correctement | ⬜ |
| T3-29 | Filtre par statut | Claire | Liste filtrée correctement | ⬜ |
| T3-30 | Toutes les missions | Claire | Toutes les missions tous coordinateurs visibles | ⬜ |
| T3-31 | Toutes les affectations | Claire | Toutes les affectations visibles | ⬜ |

---

## 9. Tests CI/CD et qualité

| ID | Test | Commande / Action | Résultat attendu | Statut |
|---|---|---|---|---|
| T3-32 | Syntaxe PHP | `find . -type f -name "*.php" -not -path "./vendor/*" -print0 \| xargs -0 -n1 php -l` | Aucune erreur | ⬜ |
| T3-33 | `.env` non versionné | `git ls-files .env` | Aucun résultat | ⬜ |
| T3-34 | CI GitHub Actions | Consulter Actions GitHub | Pipeline vert | ⬜ |
| T3-35 | Login répond | Ouvrir `login.php` | Page HTTP 200 | ⬜ |

---

## 10. Scénario Sprint Review à rejouer

1. Se connecter comme coordinateur.
2. Créer une mission.
3. Consulter ses missions.
4. Affecter un bénévole disponible.
5. Tenter une affectation impossible à cause d’un conflit.
6. Vérifier que l’application refuse l’affectation.
7. Consulter le planning du bénévole.
8. Se connecter comme responsable.
9. Voir les missions sous-dotées.
10. Montrer les filtres des missions sous-dotées.
11. Montrer `toutes_missions.php`.
12. Montrer `toutes_affectations.php`.
13. Montrer le pipeline CI/CD vert.
14. Montrer le README et les comptes de test.

---

## 11. Preuves à conserver

- Capture du dashboard responsable.
- Capture d’une mission sous-dotée.
- Capture du filtre zone/statut.
- Capture d’un refus de conflit horaire.
- Capture d’un refus de capacité dépassée.
- Capture du planning bénévole.
- Capture du journal d’audit.
- Capture du pipeline CI/CD vert.

---

## Critère de validation T3

T3 est validé si :

- `sql/demo.sql` fournit un jeu de données reproductible ;
- le parcours principal est démontrable sans manipulation cachée ;
- les affectations respectent disponibilité, conflit et capacité ;
- les missions sous-dotées sont visibles ;
- les pages sont protégées par rôle ;
- le CI/CD est vert.
