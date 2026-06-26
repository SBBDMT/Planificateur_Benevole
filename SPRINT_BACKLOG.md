# 📋 Sprint Backlog — Planificateur de bénévoles

**Sprint Goal :** Livrer en 15 heures un incrément utilisable permettant de créer des missions, déclarer les disponibilités, affecter sans conflit et afficher les besoins non couverts.

**Équipe :** Membre 1 (Dev backend) · Membre 2 (Dev frontend) · Membre 3 (DevOps / Scrum Master)
**Durée totale :** 15H · **Date de début :** ___/___/2025 · **Date de fin :** ___/___/2025

---

## 🔴 Phase 1 — Setup & fondations (H0 → H2h30)

| ID | Tâche | Responsable | Estimation | Statut | Notes |
|----|-------|-------------|------------|--------|-------|
| T0-1 | Créer le dépôt GitHub + conventions de nommage | Membre 3 | 20 min | ⬜ À faire | Branches : `main`, `develop`, `feat/xxx` |
| T0-2 | Configurer `.gitignore` + `.env.example` | Membre 3 | 10 min | ⬜ À faire | Ne jamais commit `.env` réel |
| T0-3 | Créer la structure Django (`django-admin startproject`) | Membre 1 | 15 min | ⬜ À faire | App : `festival/` |
| T0-4 | Mettre en place `requirements.txt` + `venv` | Membre 1 | 10 min | ⬜ À faire | Django, pytest-django, flake8 |
| T0-5 | Créer `docker-compose.yml` (app + db) | Membre 3 | 30 min | ⬜ À faire | SQLite en dev suffit |
| T0-6 | Créer pipeline CI GitHub Actions (lint + tests) | Membre 3 | 30 min | ⬜ À faire | `.github/workflows/ci.yml` |
| T0-7 | Créer GitHub Project (kanban) + colonnes | Membre 3 | 10 min | ⬜ À faire | À faire / En cours / Terminé |
| T1-1 | Modéliser et coder `models.py` (8 tables) | Membre 1 | 1h | ⬜ À faire | USER, VOLUNTEER, MISSION, AVAILABILITY, ASSIGNMENT, SKILL, ZONE, AUDIT_LOG |
| T1-2 | Générer et appliquer les migrations | Membre 1 | 15 min | ⬜ À faire | `makemigrations` + `migrate` |
| T1-3 | Configurer `admin.py` pour toutes les entités | Membre 1 | 20 min | ⬜ À faire | Permet de vérifier les données visuellement |
| CON-1 | Maquettes rapides des écrans principaux | Membre 2 | 30 min | ⬜ À faire | Papier ou Figma, 3 écrans max |

---

## 🟠 Phase 2 — Construction verticale 1 (H2h30 → H5h30)

| ID | Tâche | Responsable | Estimation | Statut | Notes |
|----|-------|-------------|------------|--------|-------|
| US1-A | Vue : créer une mission (titre, lieu, horaire, capacité) | Membre 1 + 2 | 1h | ⬜ À faire | Form Django + vue + template |
| US1-B | Validation : compétence facultative sur une mission | Membre 1 | 30 min | ⬜ À faire | Champ optionnel `required_skill` |
| US2-A | Vue : créer un bénévole (nom, contact, profil) | Membre 1 | 30 min | ⬜ À faire | Lié à USER |
| US2-B | Vue : renseigner les disponibilités d'un bénévole | Membre 2 | 1h | ⬜ À faire | Formulaire créneaux horaires |
| US2-C | Validation : empêcher disponibilités incohérentes | Membre 1 | 30 min | ⬜ À faire | fin > début, pas de chevauchement |
| T2-1 | Créer le jeu de données de démo (fixtures Django) | Membre 3 | 1h | ⬜ À faire | 8+ bénévoles, 6+ missions, dispos |

---

## 🟡 Daily Scrum #1 (H5h30 → H5h45)

> **Durée max : 15 min** — Voir DAILY_LOG.md pour les réponses

| Question | M1 | M2 | M3 |
|----------|----|----|----|
| Qu'est-ce que j'ai fait ? | | | |
| Qu'est-ce que je fais ? | | | |
| Blocages ? | | | |

---

## 🟠 Phase 3 — Construction verticale 2 (H5h45 → H8h45)

| ID | Tâche | Responsable | Estimation | Statut | Notes |
|----|-------|-------------|------------|--------|-------|
| US3-A | Logique : affecter un bénévole à une mission | Membre 1 | 1h | ⬜ À faire | Vérif disponibilité + capacité |
| US3-B | Logique : refuser si conflit horaire | Membre 1 | 45 min | ⬜ À faire | Message d'erreur clair |
| US3-C | Logique : refuser si capacité dépassée | Membre 1 | 30 min | ⬜ À faire | `required_capacity` atteinte |
| US3-D | Traçabilité : logger affectation/désaffectation | Membre 3 | 30 min | ⬜ À faire | Table AUDIT_LOG |
| US4-A | Vue : planning personnel d'un bénévole | Membre 2 | 1h | ⬜ À faire | Trié chronologiquement |
| US4-B | Vue : planning par mission (liste bénévoles affectés) | Membre 2 | 45 min | ⬜ À faire | |
| T3-1 | Tests : affectation valide | Membre 3 | 30 min | ⬜ À faire | pytest |
| T3-2 | Tests : conflit horaire détecté | Membre 3 | 20 min | ⬜ À faire | pytest |
| T3-3 | Tests : capacité dépassée bloquée | Membre 3 | 20 min | ⬜ À faire | pytest |

---

## 🔵 Revue intermédiaire PO (H8h45 → H9h15)

> **Durée : 30 min** — Le PO peut poser ces questions :

- [ ] Quel utilisateur bénéficie réellement de la prochaine story ?
- [ ] Quelle preuve permettra d'accepter cette story ?
- [ ] Que retirez-vous du périmètre pour protéger le Sprint Goal ?
- [ ] Quel risque doit être testé avant d'ajouter une fonctionnalité ?
- [ ] Quel élément est réellement Done maintenant ?

**Décisions prises :** _(à remplir pendant la revue)_

---

## 🟢 Phase 4 — Stabilisation MVP (H9h15 → H12h15)

| ID | Tâche | Responsable | Estimation | Statut | Notes |
|----|-------|-------------|------------|--------|-------|
| US5-A | Vue : calculer et afficher taux de couverture | Membre 1 | 45 min | ⬜ À faire | nb affectés / capacité |
| US5-B | Vue : missions incomplètes en évidence | Membre 2 | 1h | ⬜ À faire | Couleur / badge |
| US5-C | Filtre : missions sous-dotées par zone/statut | Membre 2 | 45 min | ⬜ À faire | |
| T4-1 | Rédiger README (install, lancement, limites, comptes) | Membre 3 | 45 min | ⬜ À faire | Lancement en < 5 min |
| T5-1 | Procédure de sauvegarde testée | Membre 3 | 30 min | ⬜ À faire | `dumpdata` Django |
| T5-2 | Procédure de restauration testée | Membre 3 | 30 min | ⬜ À faire | `loaddata` Django |
| STB-1 | Corrections bugs identifiés en revue intermédiaire | Tous | 1h | ⬜ À faire | |
| STB-2 | Vérifier pipeline CI vert sur `main` | Membre 3 | 20 min | ⬜ À faire | |

---

## 🟣 Phase 5 — Recette & préparation (H12h15 → H13h45)

| ID | Tâche | Responsable | Estimation | Statut | Notes |
|----|-------|-------------|------------|--------|-------|
| REC-1 | Tests finaux sur le scénario de démo complet | Tous | 45 min | ⬜ À faire | 9 étapes du scénario |
| REC-2 | Préparer le script de démo (qui montre quoi) | Membre 2 | 30 min | ⬜ À faire | |
| REC-3 | Vérifier que le seed se charge proprement | Membre 3 | 15 min | ⬜ À faire | `loaddata fixtures/demo.json` |
| REC-4 | Compléter TRACABILITE_IA.md | Tous | 15 min | ⬜ À faire | |
| REC-5 | Dernier push + tag de version `v1.0-mvp` | Membre 3 | 10 min | ⬜ À faire | |

---

## 🏁 Sprint Review (H13h45 → H14h30)

**Scénario de démo à exécuter dans l'ordre :**

- [ ] 1. Créer 6 missions avec horaires, lieux, capacités et compétences
- [ ] 2. Créer au moins 8 bénévoles
- [ ] 3. Déclarer leurs disponibilités
- [ ] 4. Affecter un bénévole disponible à une mission ✅ nominal
- [ ] 5. Tenter une affectation en conflit → refusée ❌ erreur
- [ ] 6. Afficher le planning d'un bénévole
- [ ] 7. Afficher la couverture des missions
- [ ] 8. Montrer les missions incomplètes
- [ ] 9. Montrer la CI réussie + README + restauration

**Retour critique à préparer :**
- Un choix produit : _(ex. pourquoi affectation manuelle plutôt qu'automatique ?)_
- Un choix technique : _(ex. pourquoi Django + SQLite ?)_
- Un usage de l'IA : _(ex. ce que l'IA a généré, ce qu'on a corrigé)_

---

## 🔄 Rétrospective (H14h30 → H15h00)

| Continue ✅ | Stop ❌ | Start 🚀 |
|------------|---------|---------|
| | | |
| | | |
| | | |

**Action d'amélioration concrète :** _(une seule, mesurable)_

---

## 📊 Definition of Done — Checklist finale

| Dimension | Condition | ✅ |
|-----------|-----------|---|
| Fonctionnel | Critères d'acceptation MVP démontrables | ⬜ |
| Code | Versionné, relu, sans secret, erreurs gérées | ⬜ |
| Qualité | Tests essentiels exécutés avec succès en CI | ⬜ |
| DevOps | Pipeline reproductible, config externalisée | ⬜ |
| Données | 8+ bénévoles et 6+ missions en fixtures | ⬜ |
| Documentation | README : install, lancement, limites, comptes | ⬜ |
| Sécurité | Entrées validées, pas de données sensibles réelles | ⬜ |
| Produit | PO peut exécuter le parcours sans manipulation cachée | ⬜ |
