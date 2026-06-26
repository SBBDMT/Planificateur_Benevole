# Planificateur Benevoles

## Arborescence du projet

```text
planificateur-benevoles/
│
├── index.php                          # Routing central (redirige selon rôle)
├── login.php                          # Page de connexion
├── logout.php                         # Déconnexion + destroy session
│
├── config/
│   └── db.php                         # Connexion PDO MySQL via .env
│
├── includes/
│   ├── header.php                     # Nav adaptée au rôle + vérif session
│   ├── footer.php                     # Pied de page
│   └── functions.php                  # Toutes les règles métier
│
├── pages/
│   │
│   ├── — BÉNÉVOLE —
│   ├── planning.php                   # Mon planning perso (missions affectées)
│   ├── disponibilites.php             # Mes disponibilités (voir + ajouter)
│   │
│   ├── — COORDINATEUR —
│   ├── mes_missions.php               # Ses missions uniquement (liste + créer)
│   ├── creer_mission.php              # Formulaire création mission
│   ├── mes_affectations.php           # Ses affectations uniquement
│   ├── affecter.php                   # Formulaire affectation bénévole
│   │
│   └── — RESPONSABLE —
│       ├── dashboard.php              # Vue globale + missions sous-dotées
│       ├── toutes_missions.php        # Toutes les missions tous coordinateurs
│       └── toutes_affectations.php    # Toutes les affectations
│
├── actions/                           # POST uniquement → traitement → redirect
│   ├── login.php                      # Auth + création session
│   ├── create_mission.php             # US1-A/B : créer mission + compétence
│   ├── create_benevole.php            # US2-A : créer profil bénévole
│   ├── save_disponibilite.php         # US2-B/C : enregistrer dispo + validation
│   ├── affecter.php                   # US3-A/B/C : affecter + règles métier + log
│   └── desaffecter.php                # US3-D : désaffecter + log audit
│
├── css/
│   └── style.css
│
├── sql/
│   ├── schema.sql                     # ✅ Déjà fait
│   └── demo.sql                       # 8 bénévoles, 6 missions (Membre 3)
│
├── .env.example                       # ✅ À générer maintenant
├── .env                               # ⛔ dans .gitignore
└── .gitignore
```

## CI/CD

Le projet utilise GitHub Actions pour vérifier automatiquement la qualité technique.

À chaque push ou pull request, le pipeline :

1. récupère le code ;
2. installe PHP 8.2 avec l’extension `pdo_mysql` ;
3. vérifie que le fichier `.env` n’est pas versionné ;
4. génère un `.env` de test ;
5. vérifie la structure attendue du projet ;
6. vérifie la syntaxe de tous les fichiers PHP ;
7. lance une base MariaDB de test ;
8. importe `sql/schema.sql` ;
9. importe `sql/demo.sql` si le fichier existe ;
10. démarre le serveur PHP ;
11. vérifie que `login.php` répond correctement.

Cela garantit que le projet est relançable, testable et reproductible.

### Statut CI/CD

![CI/CD](https://github.com/SBBDMT/Planificateur_Benevole/actions/workflows/ci.yml/badge.svg)

[Voir le workflow CI/CD](https://github.com/SBBDMT/Planificateur_Benevole/actions/workflows/ci.yml)