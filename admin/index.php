<?php
require_once __DIR__ . '/../functions.php';
isAdmin();
$user = getCurrentUser();
$pageTitle = "Dashboard";

// Statistiques
$stats = [
    [
        'title' => 'Pays',
        'count' => getCountAll('pays'),
        'icon' => 'bi-globe-americas',
        'color' => 'primary',
        'link' => '/admin/countries'
    ],
    [
        'title' => 'Sports',
        'count' => getCountAll('sports'),
        'icon' => 'bi-dribbble',
        'color' => 'success',
        'link' => '/admin/sports'
    ],
    [
        'title' => 'Equipes',
        'count' => getCountAll('teams'),
        'icon' => 'bi-people-fill',
        'color' => 'warning',
        'link' => '/admin/teams'
    ],
    [
        'title' => 'Matchs',
        'count' => getCountAll('matchs'),
        'icon' => 'bi-calendar-event',
        'color' => 'danger',
        'link' => '/admin/matchs'
    ],
    [
        'title' => 'Types de Match',
        'count' => getCountAll('match_types'),
        'icon' => 'bi-collection',
        'color' => 'info',
        'link' => '/admin/match_types'
    ],
    [
        'title' => 'Utilisateurs',
        'count' => getCountAll('users'),
        'icon' => 'bi-person-circle',
        'color' => 'secondary',
        'link' => '#'
    ],
];

// Derniers matchs
$recentMatchs = query("
    SELECT m.*,
           s.name AS sport_name,
           t1.name AS team_home_name,
           t2.name AS team_away_name
    FROM matchs m
    LEFT JOIN sports s ON m.sport_id = s.id
    LEFT JOIN teams t1 ON m.team_home_id = t1.id
    LEFT JOIN teams t2 ON m.team_away_id = t2.id
    ORDER BY m.created_at DESC
    LIMIT 5
")->fetchAll();

// Dernières équipes ajoutées
$recentTeams = query("
    SELECT t.*, p.name AS country_name
    FROM teams t
    LEFT JOIN pays p ON t.country_id = p.id
    ORDER BY t.created_at DESC
    LIMIT 5
")->fetchAll();
?>

<?php include_once __DIR__ . '/include/header.php'; ?>

<!-- Welcome Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Bienvenue, <?= htmlspecialchars($user['name']) ?> !</h1>
        <p class="text-muted mb-0">Voici un apercu de votre plateforme</p>
    </div>
    <div>
        <span class="badge bg-light text-dark">
            <i class="bi bi-calendar3 me-1"></i>
            <?= date('d/m/Y') ?>
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <?php foreach ($stats as $stat): ?>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stat-card bg-<?= $stat['color'] ?>">
            <i class="bi <?= $stat['icon'] ?> stat-icon"></i>
            <div class="stat-value"><?= $stat['count'] ?></div>
            <div class="stat-label"><?= $stat['title'] ?></div>
            <a href="<?= $stat['link'] ?>" class="stretched-link"></a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-lightning-fill text-warning me-2"></i>Actions rapides</span>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-auto">
                        <a href="/admin/matchs/create.php" class="btn btn-danger">
                            <i class="bi bi-plus-circle me-1"></i> Nouveau Match
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/teams/create.php" class="btn btn-warning">
                            <i class="bi bi-plus-circle me-1"></i> Nouvelle Equipe
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/countries/create.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Nouveau Pays
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/sports/create.php" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Nouveau Sport
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row g-4">
    <!-- Derniers matchs -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event me-2"></i>Derniers matchs</span>
                <a href="/admin/matchs" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentMatchs)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-1 mb-2 d-block"></i>
                        Aucun match enregistre
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Match</th>
                                <th>Sport</th>
                                <th>Score</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMatchs as $match): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($match['team_home_name'] ?? 'N/A') ?></strong>
                                    <span class="text-muted">vs</span>
                                    <strong><?= htmlspecialchars($match['team_away_name'] ?? 'N/A') ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars($match['sport_name'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold"><?= (int)$match['score_home'] ?> - <?= (int)$match['score_away'] ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = ['planned' => 'info', 'playing' => 'danger', 'finished' => 'success'];
                                    $statusLabels = ['planned' => 'Planifie', 'playing' => 'En cours', 'finished' => 'Termine'];
                                    $status = $match['status'] ?? 'planned';
                                    ?>
                                    <span class="badge bg-<?= $statusColors[$status] ?>">
                                        <?= $statusLabels[$status] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Dernières équipes -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people-fill me-2"></i>Dernieres equipes</span>
                <a href="/admin/teams" class="btn btn-sm btn-outline-warning">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentTeams)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-1 mb-2 d-block"></i>
                        Aucune equipe enregistree
                    </div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentTeams as $team): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <?php if ($team['path']): ?>
                                <img src="<?= htmlspecialchars($team['path']) ?>" alt=""
                                     class="rounded me-3" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-shield text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars($team['name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($team['country_name'] ?? 'N/A') ?></small>
                            </div>
                        </div>
                        <span class="badge bg-<?= $team['type'] === 'national' ? 'primary' : 'secondary' ?>">
                            <?= $team['type'] === 'national' ? 'National' : 'Club' ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/include/footer.php'; ?>
