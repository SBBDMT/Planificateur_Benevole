# Documentation d'exploitation — Planificateur de bénévoles

## 1. Présentation

Le Planificateur de bénévoles est une application PHP / HTML / CSS avec base MariaDB permettant de gérer les missions d’un événement culturel.

Elle couvre le MVP suivant :

- création de missions ;
- gestion des bénévoles ;
- déclaration des disponibilités ;
- affectation contrôlée des bénévoles ;
- refus des conflits horaires ;
- refus du dépassement de capacité ;
- désaffectation ;
- planning bénévole ;
- planning par mission ;
- visualisation des missions sous-dotées ;
- vues globales responsable ;
- journal d’audit ;
- sauvegarde et restauration.

---

## 2. Stack technique

| Élément | Choix |
|---|---|
| Backend | PHP vanilla |
| Frontend | HTML + CSS |
| Base de données | MariaDB / MySQL |
| Accès base | PDO |
| Serveur web | Apache |
| CI/CD | GitHub Actions |
| Configuration | `.env` |

Le projet n’utilise pas Composer, Laravel, Symfony ou JavaScript.

---

## 3. Arborescence utile

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
│   ├── planning.php
│   ├── disponibilites.php
│   ├── mes_missions.php
│   ├── creer_mission.php
│   ├── mes_affectations.php
│   ├── dashboard.php
│   ├── missions_sous_dotees.php
│   ├── toutes_missions.php
│   └── toutes_affectations.php
├── actions/
│   ├── login.php
│   ├── create_mission.php
│   ├── create_benevole.php
│   ├── save_disponibilite.php
│   ├── affecter.php
│   └── desaffecter.php
├── sql/
│   ├── schema.sql
│   └── demo.sql
├── sauvegardes/
│   ├── BACKUP_RESTORE.md
│   └── scripts/
├── .github/workflows/
│   └── ci.yml
├── .env.example
├── README.md
├── TEST.md
└── rapport.md
```

---

## 4. Installation locale

### 4.1 Cloner le dépôt

```bash
git clone https://github.com/SBBDMT/Planificateur_Benevole.git
cd Planificateur_Benevole
```

### 4.2 Créer le fichier d’environnement

```bash
cp .env.example .env
```

Exemple de configuration :

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=planificateur_benevoles
DB_USER=planificateur_app
DB_PASS=mot_de_passe_local

APP_NAME="Planificateur de bénévoles"
APP_URL=http://localhost
APP_ENV=development
APP_TIMEZONE=Europe/Paris
```

Le fichier `.env` ne doit jamais être commité.

---

## 5. Base de données

### 5.1 Créer ou réinitialiser la base

Le script `sql/schema.sql` crée la base `planificateur_benevoles` et les tables nécessaires.

```bash
mariadb -u root -p < sql/schema.sql
```

### 5.2 Charger les données de démonstration

```bash
mariadb -u root -p planificateur_benevoles < sql/demo.sql
```

Le jeu de données contient :

- 12 utilisateurs ;
- 8 bénévoles ;
- 6 missions minimum ;
- des zones ;
- des compétences ;
- des disponibilités ;
- des affectations ;
- des missions sous-dotées ;
- des logs d’audit.

---

## 6. Lancement de l’application

### 6.1 Avec Apache

Le `DocumentRoot` Apache doit pointer vers le dossier du projet :

```apache
DocumentRoot /var/www/html/Planificateur_Benevole
```

Puis recharger Apache :

```bash
sudo systemctl reload apache2
```

URL locale :

```text
http://127.0.0.1/login.php
```

### 6.2 Avec le serveur PHP intégré

Pour un test rapide :

```bash
php -S 127.0.0.1:8000 -t .
```

Puis ouvrir :

```text
http://127.0.0.1:8000/login.php
```

---

## 7. Comptes fictifs

Tous les comptes de démonstration utilisent le mot de passe :

```text
password
```

| Rôle | Nom | Email |
|---|---|---|
| Responsable | Claire Responsable | `claire.responsable@festival.test` |
| Coordinateur | Nora Coordinatrice | `nora.coordinateur@festival.test` |
| Coordinateur | Lucas Coordinateur | `lucas.coordinateur@festival.test` |
| Admin | Admin Festival | `admin@festival.test` |
| Bénévole | Emma Martin | `emma.martin@festival.test` |
| Bénévole | Hugo Bernard | `hugo.bernard@festival.test` |
| Bénévole | Lina Petit | `lina.petit@festival.test` |
| Bénévole | Karim Robert | `karim.robert@festival.test` |
| Bénévole | Zoé Durand | `zoe.durand@festival.test` |
| Bénévole | Maxime Moreau | `maxime.moreau@festival.test` |
| Bénévole | Inès Lefevre | `ines.lefevre@festival.test` |
| Bénévole | Thomas Leroy | `thomas.leroy@festival.test` |

---

## 8. Parcours utilisateur

### 8.1 Bénévole

Pages principales :

- `pages/planning.php`
- `pages/disponibilites.php`

Le bénévole peut :

- consulter son planning ;
- consulter ses disponibilités ;
- ajouter une disponibilité.

### 8.2 Coordinateur

Pages principales :

- `pages/mes_missions.php`
- `pages/creer_mission.php`
- `pages/mes_affectations.php`
- `pages/audit_log.php`
- `pages/missions_sous_dotees.php`

Le coordinateur peut :

- créer une mission ;
- consulter ses missions ;
- affecter un bénévole ;
- désaffecter un bénévole ;
- voir ses logs d’affectation ;
- voir ses missions sous-dotées.

### 8.3 Responsable

Pages principales :

- `pages/dashboard.php`
- `pages/toutes_missions.php`
- `pages/toutes_affectations.php`
- `pages/missions_sous_dotees.php`
- `pages/audit_log.php`

Le responsable peut :

- consulter le dashboard global ;
- voir toutes les missions ;
- voir toutes les affectations ;
- filtrer les missions sous-dotées ;
- consulter les logs.

---

## 9. Règles métier importantes

Une affectation est acceptée uniquement si :

- le bénévole existe ;
- la mission existe ;
- le bénévole est disponible sur toute la durée de la mission ;
- le bénévole n’est pas déjà affecté à une mission qui chevauche ce créneau ;
- la capacité de la mission n’est pas atteinte ;
- le bénévole n’est pas déjà affecté à cette mission.

Une mission est considérée sous-dotée si :

```text
nombre_affectations_confirmées < capacité_requise
```

---

## 10. Tests

Le fichier `TEST.md` contient la batterie de tests de recette.

Tests principaux :

- import du schéma ;
- import des données de démo ;
- connexion par rôle ;
- disponibilité valide ;
- disponibilité incohérente refusée ;
- affectation valide ;
- affectation refusée si indisponible ;
- affectation refusée en cas de conflit ;
- affectation refusée si capacité atteinte ;
- désaffectation ;
- audit ;
- planning bénévole ;
- planning mission ;
- missions sous-dotées ;
- filtres responsable ;
- CI/CD.

Commande de vérification syntaxe PHP :

```bash
find . -type f -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
```

---

## 11. CI/CD

Le workflow GitHub Actions est situé ici :

```text
.github/workflows/ci.yml
```

Il vérifie :

1. récupération du code ;
2. installation de PHP 8.2 ;
3. extension `pdo_mysql` ;
4. absence de `.env` versionné ;
5. création d’un `.env` de test ;
6. structure du projet ;
7. syntaxe PHP ;
8. démarrage MariaDB ;
9. import de `sql/schema.sql` ;
10. import de `sql/demo.sql` ;
11. démarrage du serveur PHP ;
12. réponse de `login.php`.

Badge :

```md
![CI/CD](https://github.com/SBBDMT/Planificateur_Benevole/actions/workflows/ci.yml/badge.svg)
```

---

## 12. Sauvegarde et restauration

La procédure complète est dans :

```text
sauvegardes/BACKUP_RESTORE.md
```

Scripts disponibles :

```text
sauvegardes/scripts/backup_db.sh
sauvegardes/scripts/restore_db.sh
sauvegardes/scripts/check_restore.sh
```

### 12.1 Sauvegarde manuelle

```bash
bash sauvegardes/scripts/backup_db.sh
```

Les dumps sont générés dans :

```text
sauvegardes/backups/database/
```

Ce dossier est ignoré par Git.

### 12.2 Restauration

```bash
bash sauvegardes/scripts/restore_db.sh sauvegardes/backups/database/nom_du_dump.sql
```

### 12.3 Vérification

```bash
bash sauvegardes/scripts/check_restore.sh
```

### 12.4 Sauvegarde automatique

Un cron serveur exécute une sauvegarde tous les jours à 17h15 UTC :

```cron
15 17 * * * root cd /var/www/html/Planificateur_Benevole && sauvegardes/scripts/backup_db.sh >> /var/log/planificateur_benevole_backup.log 2>&1
```

---

## 13. Sécurité exploitation

Mesures en place :

- `.env` ignoré par Git ;
- dumps SQL ignorés par Git ;
- dossier `sql/` interdit par Apache ;
- dossier `sauvegardes/` interdit par Apache ;
- fichiers `.md` interdits par Apache ;
- mots de passe de démo hashés ;
- requêtes SQL préparées ;
- sessions vérifiées sur les pages protégées ;
- rôles vérifiés selon les pages.

Tests HTTP attendus :

```text
/sql/schema.sql -> 403 Forbidden
/sql/demo.sql -> 403 Forbidden
/README.md -> 403 Forbidden
/TEST.md -> 403 Forbidden
/sauvegardes/BACKUP_RESTORE.md -> 403 Forbidden
/login.php -> 200 OK
```

---

## 14. Limites connues

Le MVP ne couvre pas :

- suggestion automatique d’affectation ;
- optimisation globale du planning ;
- export CSV/PDF ;
- notification automatique ;
- badge ou feuille de présence ;
- gestion avancée des fuseaux horaires ;
- interface d’administration complète ;
- modification détaillée des missions après création ;
- tests automatisés métier complets.

Certains messages d’erreur peuvent encore être améliorés pour être affichés de façon plus homogène dans l’interface.

---

## 15. Dépannage

### 15.1 Page blanche

Vérifier les logs Apache :

```bash
tail -n 100 /var/log/apache2/error.log
```

Vérifier la syntaxe PHP :

```bash
php -l chemin/du/fichier.php
```

### 15.2 Connexion impossible

Vérifier :

- `.env` ;
- utilisateur MariaDB ;
- mot de passe ;
- base `planificateur_benevoles` ;
- import de `sql/demo.sql`.

Tester la connexion :

```bash
mariadb -u USER -p planificateur_benevoles
```

### 15.3 Données absentes

Réimporter :

```bash
mariadb -u root -p < sql/schema.sql
mariadb -u root -p planificateur_benevoles < sql/demo.sql
```

### 15.4 CI/CD en échec

Vérifier dans l’ordre :

- syntaxe YAML ;
- présence des fichiers attendus ;
- syntaxe PHP ;
- cohérence entre `schema.sql` et la base utilisée par le workflow ;
- import de `demo.sql` ;
- réponse de `login.php`.

### 15.5 Sauvegarde en échec

Vérifier :

- présence de `.env` ;
- droits de l’utilisateur MariaDB ;
- disponibilité de `mariadb-dump` ou `mysqldump` ;
- droits d’écriture dans `sauvegardes/backups/database/`.

---

## 16. Commandes utiles

Voir l’état Git :

```bash
git status --short --branch
```

Vérifier que `.env` n’est pas versionné :

```bash
git ls-files .env
```

Lancer le serveur PHP intégré :

```bash
php -S 127.0.0.1:8000 -t .
```

Tester `login.php` :

```bash
curl --fail --silent --show-error http://127.0.0.1:8000/login.php > /dev/null
```

---

## 17. Contacts et responsabilités

| Sujet | Responsable |
|---|---|
| Modèle de données / règles métier | Aurélien |
| Pages et parcours utilisateur | Jonathan |
| CI/CD, sauvegarde, documentation | Sophian |

