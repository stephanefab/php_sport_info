<?php
session_start();

require_once __DIR__ . '/config.php';

/**
 * Retourne la connexion PDO (singleton)
 */
function getConnection(): PDO
{
    static $conn = null;

    if ($conn === null) {
        global $dbdriver, $dbhost, $dbport, $dbname, $dbusername, $dbpassword;

        $dsn = "$dbdriver:host=$dbhost;port=$dbport;dbname=$dbname;charset=utf8";
        #echo $dsn;

        try {
            $conn = new PDO($dsn, $dbusername, $dbpassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                //PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    return $conn;
}

function query(string $sql, array $params = [])
{
    $stmt = getConnection()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Effectue une jointure entre deux tables avec options de filtre
 * Exemple
 * $results = queryJoint(
 * 'teams',
 * 'team_sports',
 * 'id',
 * 'team_id',
 * ['t2.sport_id' => 1],
 * 'LEFT',
 * ['t1.name AS team_name', 't2.sport_id']
 * );
 *
 * @param string $table1 Première table
 * @param string $table2 Deuxième table
 * @param string $joinColumn1 Colonne de la première table pour la jointure
 * @param string $joinColumn2 Colonne de la deuxième table pour la jointure
 * @param array $where Tableau associatif [colonne => valeur] pour le WHERE (facultatif)
 * @param string $joinType Type de jointure : INNER, LEFT, RIGHT (par défaut INNER)
 * @param array|string $select Colonnes à sélectionner ou '*' pour toutes
 *
 * @return array Résultat de la jointure
 */
function queryJoint(
    string $table1,
    string $table2,
    string $joinColumn1,
    string $joinColumn2,
    array $where = [],
    string $joinType = 'INNER',
    array|string $select = '*'
): array
{
    // Gestion des colonnes à sélectionner
    $selectClause = is_array($select) ? implode(', ', $select) : $select;

    // Construire la clause WHERE si nécessaire
    $whereClause = '';
    if (!empty($where)) {
        $conds = [];
        foreach ($where as $col => $val) {
            $conds[] = "$col = :$col";
        }
        $whereClause = 'WHERE ' . implode(' AND ', $conds);
    }

    // Construire la requête
    $sql = "
        SELECT $selectClause
        FROM `$table1` t1
        $joinType JOIN `$table2` t2
        ON t1.`$joinColumn1` = t2.`$joinColumn2`
        $whereClause
    ";

    $stmt = query($sql, $where);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * Récupère toutes les lignes d'une table avec pagination et filtre par intervalle
 *
 * @param string $table Nom de la table
 * @param int $page Page actuelle
 * @param int $limit Nombre d'éléments par page
 * @param string|null $interval 'today', 'week', 'month', 'year', ou null pour tout
 * @param string $dateColumn Nom de la colonne date à utiliser (par défaut 'created_at')
 *
 * @return array ['data' => [...], 'total' => int, 'totalPages' => int, 'currentPage' => int]
 */
function getAll(string $table, int $page = 1, int $limit = 10, ?string $interval = null, string $dateColumn = 'created_at'): array
{
    $offset = ($page - 1) * $limit;
    $where = '';

    if ($interval) {
        switch ($interval) {
            case 'today':
                $where = "WHERE DATE(`$dateColumn`) = CURDATE()";
                break;
            case 'week':
                $where = "WHERE YEARWEEK(`$dateColumn`, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $where = "WHERE YEAR(`$dateColumn`) = YEAR(CURDATE()) AND MONTH(`$dateColumn`) = MONTH(CURDATE())";
                break;
            case 'year':
                $where = "WHERE YEAR(`$dateColumn`) = YEAR(CURDATE())";
                break;
            default:
                $where = '';
                break;
        }
    }

    // Récupérer les résultats
    $stmt = getConnection()->prepare("SELECT * FROM `$table` $where ORDER BY name LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer le total
    $stmt = getConnection()->prepare("SELECT COUNT(*) AS total FROM `$table` $where");
    $stmt->execute();
    $total = (int) $stmt->fetch()['total'];

    return [
        'data'        => $data,
        'total'       => $total,
        'totalPages'  => ceil($total / $limit),
        'currentPage' => $page
    ];
}



/**
 * Compte toutes les lignes d'une table avec option de filtre par intervalle de temps
 *
 * @param string $table Nom de la table
 * @param string|null $interval 'today', 'week', 'month', 'year', ou null pour tout
 * @param string $dateColumn Nom de la colonne date à utiliser (par défaut 'created_at')
 *
 * @return int
 */
function getCountAll(string $table, ?string $interval = null, string $dateColumn = 'created_at'): int
{
    $sql = "SELECT COUNT(*) AS total FROM `$table`";
    $params = [];

    if ($interval) {
        switch ($interval) {
            case 'today':
                $sql .= " WHERE DATE(`$dateColumn`) = CURDATE()";
                break;
            case 'week':
                $sql .= " WHERE YEARWEEK(`$dateColumn`, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $sql .= " WHERE YEAR(`$dateColumn`) = YEAR(CURDATE()) AND MONTH(`$dateColumn`) = MONTH(CURDATE())";
                break;
            case 'year':
                $sql .= " WHERE YEAR(`$dateColumn`) = YEAR(CURDATE())";
                break;
            case 'all':
            default:
                // pas de filtre
                break;
        }
    }

    $stmt = getConnection()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();

    return (int) $result['total'];
}


/**
 * Récupère une seule ligne selon une colonne
 */
function getOneByColumn(string $table, string $columnName, mixed $value): ?array
{
    $stmt = getConnection()->prepare("SELECT * FROM $table WHERE $columnName = :val LIMIT 1");
    $stmt->bindValue(':val', $value);
    $stmt->execute();

    return $stmt->fetch() ?: null;
}

/**
 * Recherche un terme dans plusieurs colonnes d'une table avec LIKE et pagination complète
 *
 * @param string $table Nom de la table
 * @param array $columns Colonnes dans lesquelles effectuer la recherche
 * @param string $term Terme à rechercher
 * @param int $page Page actuelle
 * @param int $limit Nombre d'éléments par page
 *
 * @return array ['data' => [...], 'total' => int, 'totalPages' => int, 'currentPage' => int]
 */
function search(string $table, array $columns, string $term, int $page = 1, int $limit = 10): array
{
    if (empty($columns)) return [
        'data' => [],
        'total' => 0,
        'totalPages' => 0,
        'currentPage' => $page
    ];

    $offset = ($page - 1) * $limit;

    $where = implode(' OR ', array_map(fn($col) => "$col LIKE :term", $columns));

    // Récupérer les résultats
    $stmt = getConnection()->prepare("SELECT * FROM `$table` WHERE $where LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':term', "%$term%");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer le total
    $stmt = getConnection()->prepare("SELECT COUNT(*) AS total FROM `$table` WHERE $where");
    $stmt->bindValue(':term', "%$term%");
    $stmt->execute();
    $total = (int) $stmt->fetch()['total'];

    return [
        'data'       => $data,
        'total'      => $total,
        'totalPages' => ceil($total / $limit),
        'currentPage'=> $page
    ];
}



/**
 * Insère une nouvelle ligne dans une table de la base de données.
 *
 * Cette fonction prend le nom de la table et un tableau associatif des colonnes
 * et valeurs à insérer. Elle utilise des requêtes préparées PDO pour éviter
 * les injections SQL.
 *
 * Exemple :
 * ```php
 * $newUserId = insert('users', [
 *     'name'  => 'Fabien Brou',
 *     'email' => 'fabien@example.com'
 * ]);
 * echo "Nouvel utilisateur créé avec l'ID : $newUserId";
 * ```
 *
 * @param string $table Nom de la table dans laquelle insérer la ligne.
 * @param array $data Tableau associatif ['colonne' => 'valeur', ...] représentant les colonnes et leurs valeurs.
 *
 * @return int L'ID de la nouvelle ligne insérée (lastInsertId).
 *
 * @throws PDOException Si la requête échoue.
 */
function insert(string $table, array $data): int
{
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

    $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
    $stmt = getConnection()->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    $stmt->execute();
    return getConnection()->lastInsertId();
}


/**
 * Met à jour une ligne dans une table de la base de données.
 *
 * Cette fonction met à jour les colonnes spécifiées pour une ligne identifiée par son ID.
 * Elle utilise des requêtes préparées PDO pour éviter les injections SQL.
 *
 * Exemple :
 * ```php
 * $success = update('users', 5, [
 *     'name'  => 'Fabien B.',
 *     'email' => 'fabienb@example.com'
 * ]);
 * if ($success) {
 *     echo "Utilisateur mis à jour avec succès !";
 * } else {
 *     echo "Échec de la mise à jour.";
 * }
 * ```
 *
 * @param string $table Nom de la table à mettre à jour.
 * @param int $id ID de la ligne à modifier.
 * @param array $data Tableau associatif ['colonne' => 'valeur', ...] des colonnes à mettre à jour.
 * @param string $idColumn Nom de la colonne identifiant la ligne (par défaut 'id').
 *
 * @return bool TRUE si la mise à jour a réussi, FALSE sinon.
 *
 * @throws PDOException Si la requête échoue.
 */
function update(string $table, int $id, array $data, string $idColumn = 'id'): bool
{
    $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));

    $sql = "UPDATE `$table` SET $setClause WHERE $idColumn = :id";
    $stmt = getConnection()->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}



/**
 * Supprime une ligne d'une table de la base de données.
 *
 * Cette fonction supprime une ligne identifiée par son ID (ou une autre colonne spécifiée).
 * Elle utilise une requête préparée PDO pour éviter les injections SQL.
 *
 * Exemple :
 * ```php
 * $deleted = delete('users', 7);
 * if ($deleted) {
 *     echo "Utilisateur supprimé !";
 * } else {
 *     echo "Impossible de supprimer l'utilisateur.";
 * }
 * ```
 *
 * @param string $table Nom de la table dans laquelle supprimer la ligne.
 * @param int $id ID (ou valeur de la colonne identifiant) de la ligne à supprimer.
 * @param string $idColumn Nom de la colonne identifiant la ligne (par défaut 'id').
 *
 * @return bool TRUE si la suppression a réussi, FALSE sinon.
 *
 * @throws PDOException Si la requête échoue.
 */
function delete(string $table, int $id, string $idColumn = 'id'): bool
{
    $sql = "DELETE FROM `$table` WHERE $idColumn = :id";
    $stmt = getConnection()->prepare($sql);
    $stmt->bindValue(':id', $id);

    return $stmt->execute();
}

/**
 * Crée un administrateur par défaut si aucun utilisateur n'existe
 *
 * @param string $name
 * @param string $email
 * @param string $password
 * @return int ID de l'utilisateur créé ou existant
 */
function createDefaultAdmin(string $name = 'Admin', string $email = 'admin@example.com', string $password = 'admin123'): int
{
    // Vérifier s'il existe déjà un admin
    $existingAdmin = getOneByColumn('users', 'role', 'admin');
    if ($existingAdmin) {
        return (int) $existingAdmin['id'];
    }

    // Créer le compte admin
    return insert('users', [
        'name'     => $name,
        'email'    => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role'     => 'admin'
    ]);
}

/**
 * Met à jour le mot de passe d'un utilisateur
 *
 * @param int $userId ID de l'utilisateur
 * @param string $newPassword Nouveau mot de passe en clair
 * @return bool true si succès, false sinon
 */
function updatePassword(int $userId, string $newPassword): bool
{
    // Hasher le mot de passe
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Mettre à jour dans la table users
    return update('users', $userId, ['password' => $hashedPassword]);
}

/**
 * Retourne l'utilisateur actuellement connecté
 *
 * @return array|null Tableau associatif de l'utilisateur ou null si pas connecté
 */
function getCurrentUser(): ?array
{
    if (!isset($_SESSION['user'])) {
        return null;
    }

    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        return null;
    }

    return getOneByColumn('users', 'id', $userId);
}


/**
 * Crée un utilisateur normal
 *
 * @param string $name
 * @param string $email
 * @param string $password
 * @return int ID de l'utilisateur créé
 */
function createUser(string $name, string $email, string $password): int
{
    return insert('users', [
        'name'     => $name,
        'email'    => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role'     => 'user'
    ]);
}


/**
 * Authentifie un utilisateur
 *
 * @param string $email
 * @param string $password
 * @return bool TRUE si connexion réussie, FALSE sinon
 */
function login(string $email, string $password): bool
{
    // Récupérer l'utilisateur par email
    $user = getOneByColumn('users', 'email', $email);

    if (!$user) {
        return false; // utilisateur inexistant
    }

    // Vérifier le mot de passe
    if (!password_verify($password, $user['password'])) {
        return false; // mot de passe incorrect
    }

    // Stocker les informations de l'utilisateur dans la session
    $_SESSION['user'] = [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role']
    ];

    return true;
}

/**
 * Déconnecte l'utilisateur courant
 */
function logout(): void
{
    // Détruire la session
    if (isset($_SESSION['user'])) {
        unset($_SESSION['user']);
        session_destroy();
    }
    redirectToUrl("/index.php");
}

function isAdmin(): void {
    if(!isLogged() || $_SESSION['user']['role'] != 'admin'){
        redirectToLogin();
    }
}

function isUser(): void {
    if (!isLogged() || $_SESSION['user']['role'] !== 'user') {
        redirectToLogin();
    }
}

function isLogged(): bool {
   return (isset($_SESSION['user']) && getOneByColumn("users", "id", $_SESSION['user']['id']));
}

/**
 * Vérifie un fichier uploadé et génère un nom unique
 *
 * @param array $file Le fichier de $_FILES (ex: $_FILES['avatar'])
 * @param array $allowedExtensions Extensions autorisées
 * @param int $maxSize Taille max en octets
 *
 * @return array|false ['name' => string, 'timestamp' => int, 'extension' => string] ou false si erreur
 */
function checkUpload(array $file, array $allowedExtensions = ['jpg','png','jpeg','gif'], int $maxSize = 2097152)
{
    if ($file['error'] !== UPLOAD_ERR_OK) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) return false;

    if ($file['size'] > $maxSize) return false;

    $timestamp = time();
    $uniqueName = $timestamp . '_' . bin2hex(random_bytes(5)) . '.' . $ext;

    return [
        'name'      => $uniqueName,
        'timestamp' => $timestamp,
        'extension' => $ext
    ];
}

/**
 * Sauvegarde un fichier uploadé dans un dossier structuré par date
 *
 * @param array $file Le fichier de $_FILES
 * @param string $newName Nom du fichier généré par checkUpload()
 * @param string $baseDir Dossier de base (ex: 'uploads')
 *
 * @return string|false Chemin complet du fichier sauvegardé ou false si erreur
 */
function saveUploadWithFolders(array $file, string $newName, string $baseDir = 'uploads')
{
    // Sous-dossier par date : uploads/YYYY/MM/DD
    $subDir = date('Y/m/d');
    $fullDir = rtrim($baseDir, '/') . '/' . $subDir;

    if (!is_dir($fullDir)) mkdir($fullDir, 0755, true);

    $destination = $fullDir . '/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $destination; // chemin complet à enregistrer en base
    }

    return false;
}

/**
 * Retourne l'URL relative d'un fichier uploadé
 *
 * @param string $filePath Chemin complet renvoyé par saveUploadWithFolders()
 * @param string $baseDir Dossier de base upload (ex: 'uploads')
 *
 * @return string URL relative
 */
function getUploadUrl(string $filePath, string $baseDir = 'uploads'): string
{
    // Retire le dossier de base du chemin
    $relativePath = str_replace('\\', '/', $filePath); // Windows friendly
    $relativePath = str_replace(rtrim($baseDir, '/') . '/', '', $relativePath);

    return $baseDir . '/' . $relativePath;
}

function redirectToUrl(string $url): void
{
    if (!headers_sent()) { // Vérifie que les headers ne sont pas encore envoyés
        header("Location: $url");
        exit();
    } else {
        // Fallback si les headers sont déjà envoyés
        echo "<script>window.location.href='" . htmlspecialchars($url, ENT_QUOTES) . "';</script>";
        exit();
    }
}


function redirectToLogin(): void
{
    redirectToUrl("/login.php");
}

function redirectToHome(): void
{
    redirectToUrl("/index.php");
}

/**
 * Initialise le tableau de flash messages si nécessaire
 */
function initFlash(): void
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [
            'success' => [],
            'error'   => [],
            'info'    => []
        ];
    }
}

/**
 * Ajoute un message flash
 *
 * @param string $message Le texte du message
 * @param string $type 'success', 'error', 'info'
 */
function setFlashMessage(string $message, string $type = 'info'): void
{
    $type = strtolower($type);
    if (!in_array($type, ['success', 'error', 'info'])) {
        $type = 'info';
    }

    initFlash();
    $_SESSION['flash'][$type][] = $message;
}

/**
 * Shortcut pour un message d'erreur
 */
function setErrorMessage(string $message): void
{
    setFlashMessage($message, 'error');
}

/**
 * Retourne vrai s'il y a au moins un message d'erreur
 */
function hasErrors(): bool
{
    initFlash();
    return !empty($_SESSION['flash']['error']);
}

/**
 * Affiche tous les messages flash et les supprime de la session
 */
function displayFlash(): void
{
    initFlash();

    foreach (['success', 'error', 'info'] as $type) {
        foreach ($_SESSION['flash'][$type] as $message) {
            $bsType = $type === 'error' ? 'danger' : $type; // Bootstrap alert-danger pour error
            echo '<div class="alert alert-' . $bsType . ' alert-dismissible fade show" role="alert">'
                . htmlspecialchars($message) .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
        // Vider les messages affichés
        $_SESSION['flash'][$type] = [];
    }
}


/**
 * Nettoie une donnée reçue de l'utilisateur
 *
 * - trim() : supprime les espaces en début et fin
 * - strip_tags() : supprime les balises HTML et PHP
 *
 * @param string $input Donnée à nettoyer
 * @return string Donnée nettoyée
 */
function sanitizeInput(string $input, bool $set_lower = false): string
{
    // Trim + supprime balises HTML
    $clean = trim(strip_tags($input));

    // Supprime les caractères de contrôle invisibles (sécurité + propreté)
    $clean = preg_replace('/[\x00-\x1F\x7F]/u', '', $clean);

    // Normalisation unicode (évite les caractères chelous)
    if (class_exists('Normalizer')) {
        $clean = Normalizer::normalize($clean, Normalizer::FORM_C);
    }

    // Force lowercase si demandé
    return $set_lower ? mb_strtolower($clean, 'UTF-8') : $clean;
}


createDefaultAdmin(email: "admin1@gmail.com", password: "admin01");