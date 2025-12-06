Sports

Un léger projet en php pour les résultats sur les sports et les infos d'équipes avec statistiques

Structuration

CREATE TABLE pays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code_iso CHAR(3)
);

CREATE TABLE sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    type ENUM('national', 'club') NOT NULL,
    country_id INT NOT NULL,
    FOREIGN KEY (country_id) REFERENCES pays(id)
);

CREATE TABLE team_sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    sport_id INT NOT NULL,
    FOREIGN KEY (team_id) REFERENCES teams(id),
    FOREIGN KEY (sport_id) REFERENCES sports(id),
    UNIQUE (team_id, sport_id)
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sport_id INT NOT NULL,
    team_home_id INT NOT NULL,
    team_away_id INT NOT NULL,
    score_home INT DEFAULT 0,
    score_away INT DEFAULT 0,
    match_date DATETIME NOT NULL,
    status ENUM('planned', 'finished') DEFAULT 'planned',

    FOREIGN KEY (sport_id) REFERENCES sports(id),
    FOREIGN KEY (team_home_id) REFERENCES teams(id),
    FOREIGN KEY (team_away_id) REFERENCES teams(id)
);

CREATE TABLE `match_types` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `matches`
ADD COLUMN `match_type_id` INT UNSIGNED DEFAULT NULL AFTER `id`,
ADD CONSTRAINT `fk_matches_match_type`
    FOREIGN KEY (`match_type_id`) REFERENCES `match_types`(`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE `users`
ADD COLUMN `path` VARCHAR(255) DEFAULT NULL;

Utilisation de l'Upload de fichier

if (isset($_FILES['avatar'])) {
    $check = checkUpload($_FILES['avatar']);
    if ($check) {
        $path = saveUploadWithFolders($_FILES['avatar'], $check['name']);
        if ($path) {
            echo "Fichier uploadé avec succès : " . $path;
            // Ici tu peux enregistrer $path dans ta table users ou autre
        } else {
            echo "Erreur lors de la sauvegarde du fichier.";
        }
    } else {
        echo "Fichier invalide ou trop lourd.";
    }
}

schema

    ┌─────────────────────┐
                         │   getConnection()   │
                         │  (PDO singleton)   │
                         └─────────┬──────────┘
                                   │
          ┌────────────────────────┴─────────────────────────┐
          │                                                  │
┌───────────────────────┐                          ┌───────────────────────┐
│        CRUD / DB       │                          │        Auth           │
└─────────┬─────────────┘                          └─────────┬─────────────┘
          │                                                  │
 ┌────────┴────────┐                              ┌──────────┴───────────┐
 │  Basic Queries  │                              │  Session / User      │
 └────────┬────────┘                              └──────────┬───────────┘
          │                                                  │
 ┌────────┴────────┐                        ┌────────────────┴──────────────┐
 │  getAll()       │                        │ login()                       │
 │  getCountAll()  │                        │ logout()                      │
 │  getOneByColumn()│                       │ isLogged()                    │
 │  search()       │                        │ getCurrentUser()              │
 │  query()        │                        │ createDefaultAdmin()          │
 │  queryJoint()   │                        │ createUser()                  │
 │  insert()       │                        │ updatePassword()              │
 │  update()       │                        │ isAdmin()                     │
 │  delete()       │                        │ isUser()                      │
 └─────────────────┘                        └──────────────────────────────┘

    │
          │
 ┌────────┴─────────┐
 │   Upload / Files │
 └────────┬─────────┘
          │
 ┌────────┴────────────┐
 │ checkUpload()       │
 │ saveUploadWithFolders() │
 │ getUploadUrl()      │
 └─────────────────────┘

    │
          │
 ┌────────┴────────────┐
 │ Redirect Helpers    │
 └────────┬────────────┘
          │
 ┌────────┴───────────┐
 │ redirectToUrl()    │
 │ redirectToLogin()  │
 └────────────────────┘

### Explications

1. **Connexion à la DB**
   * `getConnection()` est la base : toutes les fonctions DB (CRUD, jointures, recherches) en dépendent.
2. **CRUD / DB**
   * Fonctions génériques : `getAll`, `search`, `insert`, `update`, `delete`.
   * `query()` et `queryJoint()` permettent des requêtes plus flexibles et des jointures.
3. **Auth / Session / Users**
   * Tout ce qui gère la connexion, déconnexion, création d’utilisateurs et vérifications de rôle.
4. **Upload / Fichiers**
   * Vérification, sauvegarde et génération d’URL pour les fichiers uploadés.
5. **Redirections**
   * Helpers simples pour rediriger l’utilisateur, utilisés par auth et upload si nécessaire.

https://flagpedia.net/cote-d-ivoire

https://commons.wikimedia.org/wiki/File:Flag_of_China.svg
