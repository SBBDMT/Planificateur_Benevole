# T5 — Procédure de sauvegarde et restauration

## Objectif

Garantir que le projet peut être sauvegardé et restauré avant la Sprint Review, sans dépendre d'une manipulation cachée.

Cette procédure couvre :

- la sauvegarde de la base MariaDB ;
- la restauration de la base MariaDB ;
- la vérification minimale après restauration ;
- les preuves à conserver pour la mission T5.

Les scripts fournis dans ce dossier sont prêts à intégrer, mais ne sont pas installés automatiquement.

---

## Périmètre

### À sauvegarder

- Base MariaDB configurée dans `.env`.
- Fichiers versionnés via GitHub.
- Documentation projet.

### À ne pas versionner

- `.env`.
- Dumps SQL de production ou de démo locale.
- Archives contenant des mots de passe ou données sensibles.

Le code est sauvegardé par Git. La sauvegarde critique à produire avant revue est donc le dump SQL.

---

## Préconditions

- `.env` présent et correctement configuré.
- Commande `mariadb` disponible.
- Commande `mariadb-dump` ou `mysqldump` disponible.
- Utilisateur SQL autorisé à lire et restaurer la base.
- CI/CD vert sur `main`.

Vérifier les outils :

```bash
mariadb --version
mariadb-dump --version || mysqldump --version
```

---

## Sauvegarde manuelle recommandée

Depuis la racine du projet :

```bash
bash sauvegardes/scripts/backup_db.sh
```

Le script :

- lit la configuration dans `.env` ;
- crée un dump SQL horodaté ;
- écrit un fichier `.sha256` associé ;
- affiche le chemin du backup.

Par défaut, le dump est écrit dans :

```text
sauvegardes/backups/database/
```

---

## Restauration manuelle recommandée

Restaurer un dump :

```bash
bash sauvegardes/scripts/restore_db.sh sauvegardes/backups/database/planificateur_benevoles_YYYYmmdd_HHMMSS.sql
```

Le script demande une confirmation explicite avant import.

Pour automatiser dans un contexte maîtrisé :

```bash
bash sauvegardes/scripts/restore_db.sh --yes sauvegardes/backups/database/planificateur_benevoles_YYYYmmdd_HHMMSS.sql
```

Attention : la restauration remplace l'état courant des tables si le dump contient des instructions `DROP TABLE`.

---

## Vérifications après restauration

Exécuter :

```bash
bash sauvegardes/scripts/check_restore.sh
```

Vérifications attendues :

- au moins 8 bénévoles ;
- au moins 6 missions ;
- au moins 1 mission sous-dotée ;
- comptes de démonstration présents ;
- tables principales lisibles.

---

## Scénario de preuve pour la Sprint Review

1. Montrer que `.env` n'est pas versionné :

```bash
git ls-files .env
```

Résultat attendu : aucune sortie.

2. Montrer un backup SQL horodaté.

3. Montrer le fichier `.sha256`.

4. Restaurer sur une base de test ou une base locale jetable.

5. Lancer `check_restore.sh`.

6. Ouvrir l'application et vérifier :

- connexion Claire responsable ;
- dashboard visible ;
- missions sous-dotées visibles ;
- toutes les missions visibles.

---

## Commandes utiles

Importer le schéma puis les données de démo :

```bash
mariadb -u USER -p DB_NAME < sql/schema.sql
mariadb -u USER -p DB_NAME < sql/demo.sql
```

Vérifier la syntaxe PHP :

```bash
find . -type f -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
```

Vérifier les tables principales :

```sql
SELECT COUNT(*) FROM user;
SELECT COUNT(*) FROM volunteer;
SELECT COUNT(*) FROM mission;
SELECT COUNT(*) FROM assignment;
```

---

## Critères d'acceptation T5

La mission T5 est validée si :

- une procédure de sauvegarde existe ;
- une procédure de restauration existe ;
- les scripts ne contiennent pas de secret en dur ;
- les scripts lisent `.env` ;
- un backup peut être produit ;
- une restauration peut être testée ;
- les données restaurées permettent de rejouer le scénario de démonstration ;
- les backups ne sont pas versionnés.
