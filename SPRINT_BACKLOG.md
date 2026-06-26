# 📋 Sprint Backlog — Planificateur de bénévoles

**Sprint Goal :** Livrer en 15 heures un incrément utilisable permettant de créer des missions, déclarer les disponibilités, affecter sans conflit et afficher les besoins non couverts.

**Équipe :**
- 👤 Aurélien — Dev backend / métier
- 👤 Jonathan — Dev frontend / parcours utilisateur
- 👤 Sophian — DevOps / QA / Scrum Master tournant

**Durée totale :** 15H · **Date début :** ___/___/_____ · **Date fin :** ___/___/_____

**Légende statut :** ⬜ À faire · 🔄 En cours · ✅ Terminé · ❌ Bloqué

---

## 5.1 MVP — Socle métier

| # | ID | Story | Critères d'acceptation | Pts | Dépend. | Responsable | Statut | Notes |
|---|-----|-------|------------------------|-----|---------|-------------|--------|-------|
| 1 | T0 | Initialiser dépôt, conventions, CI minimale | Repo créé, pipeline lancé, structure projet disponible | 2 | — | Sophian | ✅ | |
| 2 | T1 | Créer le modèle de données minimal | Tables missions, bénévoles, disponibilités, affectations créées | 3 | T0 | Aurélien | ✅ | `schema.sql` généré |
| 3 | US1-A | Créer une mission simple | Titre, lieu, début, fin, capacité enregistrés | 2 | T1 | Aurélien | ✅ | |
| 4 | US1-B | Ajouter compétence éventuelle à une mission | Compétence facultative enregistrée et validée | 1 | US1-A | Aurélien | ✅ | |
| 5 | US2-A | Créer un bénévole | Nom, contact et profil créés | 1 | T1 | Aurélien | ✅ | |
| 6 | US2-B | Renseigner les disponibilités | Créneaux enregistrés pour un bénévole | 2 | US2-A | Jonathan | ✅ | |
| 7 | US2-C | Empêcher les disponibilités incohérentes | Fin > début ; chevauchements détectés | 2 | US2-B | Aurélien | ✅ | |
| 8 | US3-A | Affecter un bénévole à une mission | Affectation créée si disponibilité suffisante | 3 | US1, US2 | Aurélien | ✅ | |
| 9 | US3-B | Refuser une affectation en conflit | Conflit horaire détecté et message clair affiché | 3 | US3-A | Aurélien | ✅ | |
| 10 | US3-C | Refuser si capacité dépassée | Mission pleine : affectation impossible | 2 | US3-A | Aurélien | ✅ | |
| 11 | US3-D | Tracer affectation et désaffectation | Journal visible ou exploitable dans les logs | 2 | US3-A | Sophian | ✅ | |
| 12 | US4-A | Afficher le planning d'un bénévole | Missions triées chronologiquement | 2 | US3-A | Jonathan | ✅ | |
| 13 | US4-B | Afficher le planning par mission | Liste des bénévoles affectés visible | 2 | US3-A | Jonathan | ✅ | |
| 14 | US5-A | Calculer la couverture d'une mission | Nombre affecté / capacité affiché | 2 | US3-A | Aurélien | ⬜ | |
| 15 | US5-B | Identifier les missions sous-dotées | Missions incomplètes mises en évidence | 3 | US5-A | Jonathan | ✅ | |
| 16 | US5-C | Filtrer les missions sous-dotées | Filtre opérationnel par zone ou statut | 2 | US5-B | Jonathan | ✅ | |
| 17 | T2 | Jeu de données de démonstration | Au moins 8 bénévoles et 6 missions | 2 | T1 | Sophian | ✅ | |
| 18 | T3 | Tests essentiels | Tests affectation, conflit, capacité, disponibilité | 3 | US3, US5 | Tous | ⬜ | |
| 19 | T4 | Documentation d'exploitation | README, lancement, limites connues, comptes fictifs | 2 | T0 | Sophian | ⬜ | |
| 20 | T5 | Sauvegarde et restauration | Procédure testée avant revue | 2 | T1 | Sophian | ⬜ | |

---

## 5.2 Extensions — Après MVP accepté

| # | ID | Story | Critères d'acceptation | Pts | Dépend. | Responsable | Statut |
|---|-----|-------|------------------------|-----|---------|-------------|--------|
| 21 | EXT1 | Suggestion automatique d'affectation | Proposition non obligatoire, validée manuellement | 5 | MVP | Option | ⬜ |
| 22 | EXT2 | Export CSV/PDF | Export lisible du planning | 3 | US4, US5 | Option | ⬜ |
| 23 | EXT3 | Badge ou feuille de présence | Présence simulée enregistrée | 3 | US3 | Option | ⬜ |
| 24 | EXT4 | Notification simulée | Changement visible ou message simulé | 2 | US3 | Option | ⬜ |

---

## 📅 Suivi par phase

### Phase 1 — Setup & conception (H0 → H2h30)
> Responsable principal : Sophian + Aurélien (schéma BDD)

- [ ] T0 — Repo GitHub + CI + structure projet *(Sophian)*
- [x] T1 — Modèle de données `schema.sql` *(Aurélien)*

---

### Phase 2 — Construction verticale 1 (H2h30 → H5h30)
> Missions + bénévoles + disponibilités

- [ ] US1-A — Créer une mission simple *(Aurélien)*
- [ ] US1-B — Compétence facultative sur mission *(Aurélien)*
- [ ] US2-A — Créer un bénévole *(Aurélien)*
- [ ] US2-B — Renseigner les disponibilités *(Jonathan)*
- [ ] US2-C — Empêcher disponibilités incohérentes *(Aurélien)*
- [ ] T2 — Jeu de données démo *(Sophian)*

---

### 🕐 Daily Scrum #1 (H5h30 → H5h45)

| | Aurélien | Jonathan | Sophian |
|--|----------|----------|----------|
| ✅ Fait | | | |
| 🔜 Prévu | | | |
| ❗ Blocage | | | |

**Adaptation :** *(à remplir)*

---

### Phase 3 — Construction verticale 2 (H5h45 → H8h45)
> Affectation contrôlée + règles métier

- [ ] US3-A — Affecter un bénévole à une mission *(Aurélien)*
- [ ] US3-B — Refuser si conflit horaire *(Aurélien)*
- [ ] US3-C — Refuser si capacité dépassée *(Aurélien)*
- [ ] US3-D — Tracer affectation/désaffectation *(Sophian)*
- [ ] US4-A — Planning d'un bénévole *(Jonathan)*
- [ ] US4-B — Planning par mission *(Jonathan)*

---

### 🔵 Revue intermédiaire PO (H8h45 → H9h15)

**Questions PO à préparer :**
- [ ] Quel utilisateur bénéficie réellement de la prochaine story ?
- [ ] Quelle preuve permettra d'accepter cette story ?
- [ ] Que retirez-vous du périmètre pour protéger le Sprint Goal ?
- [ ] Quel risque doit être testé avant d'ajouter une fonctionnalité ?
- [ ] Quel élément est réellement Done maintenant ?

**Décisions prises :** *(à remplir)*

---

### Phase 4 — Stabilisation MVP (H9h15 → H12h15)

- [ ] US5-A — Calculer couverture d'une mission *(Aurélien)*
- [ ] US5-B — Missions sous-dotées en évidence *(Jonathan)*
- [ ] US5-C — Filtre missions sous-dotées *(Jonathan)*
- [ ] T3 — Tests essentiels *(Tous)*
- [ ] T4 — README + documentation *(Sophian)*
- [ ] T5 — Sauvegarde et restauration *(Sophian)*

---

### 🕐 Daily Scrum #2 (H9h15)

| | Aurélien | Jonathan | Sophian |
|--|----------|----------|----------|
| ✅ Fait | | | |
| 🔜 Prévu | | | |
| ❗ Blocage | | | |

**Adaptation :** *(à remplir)*

---

### Phase 5 — Recette & préparation (H12h15 → H13h45)

- [ ] Tester le scénario de démo complet de bout en bout
- [ ] Vérifier seed : 8+ bénévoles, 6+ missions chargés
- [ ] Pipeline CI vert sur `main`
- [ ] Compléter `TRACABILITE_IA.md`
- [ ] Tag de version `v1.0-mvp`

---

### 🕐 Daily Scrum #3 (H12h15)

| | Aurélien | Jonathan | Sophian |
|--|----------|----------|----------|
| ✅ Fait | | | |
| 🔜 Prévu | | | |
| ❗ Blocage | | | |

---

### 🏁 Sprint Review (H13h45 → H14h30)

**Scénario de démo :**
- [ ] 1. Créer 6 missions avec horaires, lieux, capacités, compétences
- [ ] 2. Créer 8+ bénévoles
- [ ] 3. Déclarer les disponibilités
- [ ] 4. Affecter un bénévole disponible ✅ nominal
- [ ] 5. Tenter une affectation en conflit → refusée ❌ erreur
- [ ] 6. Afficher le planning d'un bénévole
- [ ] 7. Afficher la couverture des missions
- [ ] 8. Montrer les missions incomplètes
- [ ] 9. Montrer CI ✅ + README + restauration

**Retour critique :**
- Choix produit : *(ex. pourquoi affectation manuelle ?)*
- Choix technique : *(ex. pourquoi PHP vanilla + MySQL ?)*
- Usage IA : *(ce que l'IA a généré, ce qu'on a corrigé)*

---

### 🔄 Rétrospective (H14h30 → H15h00)

| ✅ Continue | ❌ Stop | 🚀 Start |
|------------|--------|---------|
| | | |
| | | |

**Action d'amélioration :** *(une seule, concrète et mesurable)*

---

## ✅ Definition of Done — Checklist finale

| Dimension | Condition minimale | Statut |
|-----------|-------------------|--------|
| Fonctionnel | Critères d'acceptation MVP démontrables | ⬜ |
| Code | Versionné, relu, sans secret, erreurs gérées | ⬜ |
| Qualité | Tests essentiels exécutés avec succès | ⬜ |
| DevOps | Pipeline reproductible, config externalisée | ⬜ |
| Données | 8+ bénévoles et 6+ missions en seed | ⬜ |
| Documentation | README : install, lancement, limites, comptes fictifs | ⬜ |
| Sécurité | Entrées validées, pas de données sensibles réelles | ⬜ |
| Produit | PO peut exécuter le parcours sans manipulation cachée | ⬜ |
