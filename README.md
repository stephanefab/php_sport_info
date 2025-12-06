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

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
