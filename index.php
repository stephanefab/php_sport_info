<?php
require_once __DIR__ . '/functions.php';

$pageTitle = 'Accueil - Sports Comments';

// Récupérer les matchs en cours
$matchsEnCours = query("
    SELECT m.*,
           s.name AS sport_name, s.path AS sport_path,
           t1.name AS team_home_name, t1.path AS team_home_path,
           t2.name AS team_away_name, t2.path AS team_away_path,
           mt.name AS match_type_name
    FROM matchs m
    LEFT JOIN sports s ON m.sport_id = s.id
    LEFT JOIN teams t1 ON m.team_home_id = t1.id
    LEFT JOIN teams t2 ON m.team_away_id = t2.id
    LEFT JOIN match_types mt ON m.match_type_id = mt.id
    WHERE m.status = 'playing'
    ORDER BY m.match_date DESC
    LIMIT 6
")->fetchAll();

// Récupérer les prochains matchs (planifiés)
$matchsAVenir = query("
    SELECT m.*,
           s.name AS sport_name, s.path AS sport_path,
           t1.name AS team_home_name, t1.path AS team_home_path,
           t2.name AS team_away_name, t2.path AS team_away_path,
           mt.name AS match_type_name
    FROM matchs m
    LEFT JOIN sports s ON m.sport_id = s.id
    LEFT JOIN teams t1 ON m.team_home_id = t1.id
    LEFT JOIN teams t2 ON m.team_away_id = t2.id
    LEFT JOIN match_types mt ON m.match_type_id = mt.id
    WHERE m.status = 'planned' AND m.match_date >= NOW()
    ORDER BY m.match_date ASC
    LIMIT 6
")->fetchAll();

// Récupérer les derniers résultats (matchs terminés)
$derniersResultats = query("
    SELECT m.*,
           s.name AS sport_name, s.path AS sport_path,
           t1.name AS team_home_name, t1.path AS team_home_path,
           t2.name AS team_away_name, t2.path AS team_away_path,
           mt.name AS match_type_name
    FROM matchs m
    LEFT JOIN sports s ON m.sport_id = s.id
    LEFT JOIN teams t1 ON m.team_home_id = t1.id
    LEFT JOIN teams t2 ON m.team_away_id = t2.id
    LEFT JOIN match_types mt ON m.match_type_id = mt.id
    WHERE m.status = 'finished'
    ORDER BY m.match_date DESC
    LIMIT 6
")->fetchAll();

// Récupérer les sports
$sports = getAll('sports', 1, 8)['data'];

// Récupérer quelques équipes populaires
$teams = query("
    SELECT t.*, p.name AS country_name, p.path AS country_path
    FROM teams t
    LEFT JOIN pays p ON t.country_id = p.id
    ORDER BY t.created_at DESC
    LIMIT 8
")->fetchAll();

// Statistiques globales
$totalMatchs = getCountAll('matchs');
$totalTeams = getCountAll('teams');
$totalSports = getCountAll('sports');
?>

<?php include_once __DIR__ . '/include/header.php'; ?>

<!-- Hero Section -->
<section class="bg-dark text-white rounded-3 p-5 mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold mb-3">
                <i class="bi bi-trophy-fill text-warning"></i> Bienvenue sur Sports Comments
            </h1>
            <p class="lead mb-4">
                Suivez vos matchs favoris, commentez les performances et restez informé des derniers résultats sportifs en temps réel.
            </p>
            <div class="d-flex gap-3 flex-wrap">
                <?php if (!isLogged()): ?>
                    <a href="/register.php" class="btn btn-warning btn-lg">
                        <i class="bi bi-person-plus"></i> Rejoindre la communauté
                    </a>
                    <a href="/login.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </a>
                <?php else: ?>
                    <a href="/me" class="btn btn-warning btn-lg">
                        <i class="bi bi-person-circle"></i> Mon profil
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4 text-center mt-4 mt-lg-0">
            <div class="row text-center">
                <div class="col-4">
                    <div class="display-6 fw-bold text-warning"><?= $totalMatchs ?></div>
                    <small>Matchs</small>
                </div>
                <div class="col-4">
                    <div class="display-6 fw-bold text-warning"><?= $totalTeams ?></div>
                    <small>Équipes</small>
                </div>
                <div class="col-4">
                    <div class="display-6 fw-bold text-warning"><?= $totalSports ?></div>
                    <small>Sports</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Matchs en cours -->
<?php if (!empty($matchsEnCours)): ?>
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-broadcast text-danger"></i> Matchs en direct
            <span class="badge bg-danger ms-2 pulse-animation">LIVE</span>
        </h2>
    </div>
    <div class="row g-3">
        <?php foreach ($matchsEnCours as $match): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <span>
                        <?php if ($match['sport_path']): ?>
                            <img src="<?= htmlspecialchars($match['sport_path']) ?>" alt="" class="me-1" style="width: 20px; height: 20px; object-fit: contain;">
                        <?php endif; ?>
                        <?= htmlspecialchars($match['sport_name'] ?? 'Sport') ?>
                    </span>
                    <span class="badge bg-light text-danger">
                        <i class="bi bi-circle-fill small"></i> En direct
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($match['match_type_name']): ?>
                        <small class="text-muted d-block mb-2"><?= htmlspecialchars($match['match_type_name']) ?></small>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-center flex-fill">
                            <?php if ($match['team_home_path']): ?>
                                <img src="<?= htmlspecialchars($match['team_home_path']) ?>" alt="" class="mb-2" style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="small fw-bold"><?= htmlspecialchars($match['team_home_name'] ?? 'Équipe A') ?></div>
                        </div>
                        <div class="text-center px-3">
                            <div class="display-6 fw-bold">
                                <?= (int)$match['score_home'] ?> - <?= (int)$match['score_away'] ?>
                            </div>
                        </div>
                        <div class="text-center flex-fill">
                            <?php if ($match['team_away_path']): ?>
                                <img src="<?= htmlspecialchars($match['team_away_path']) ?>" alt="" class="mb-2" style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="small fw-bold"><?= htmlspecialchars($match['team_away_name'] ?? 'Équipe B') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Prochains matchs -->
<?php if (!empty($matchsAVenir)): ?>
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-calendar-event text-primary"></i> Prochains matchs
        </h2>
    </div>
    <div class="row g-3">
        <?php foreach ($matchsAVenir as $match): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>
                        <?php if ($match['sport_path']): ?>
                            <img src="<?= htmlspecialchars($match['sport_path']) ?>" alt="" class="me-1" style="width: 20px; height: 20px; object-fit: contain;">
                        <?php endif; ?>
                        <?= htmlspecialchars($match['sport_name'] ?? 'Sport') ?>
                    </span>
                    <span class="badge bg-light text-primary">
                        <?= date('d/m H:i', strtotime($match['match_date'])) ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($match['match_type_name']): ?>
                        <small class="text-muted d-block mb-2"><?= htmlspecialchars($match['match_type_name']) ?></small>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-center flex-fill">
                            <?php if ($match['team_home_path']): ?>
                                <img src="<?= htmlspecialchars($match['team_home_path']) ?>" alt="" class="mb-2" style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="small fw-bold"><?= htmlspecialchars($match['team_home_name'] ?? 'Équipe A') ?></div>
                        </div>
                        <div class="text-center px-3">
                            <div class="h4 text-muted">VS</div>
                            <small class="text-muted">
                                <?= $match['match_hour'] ? htmlspecialchars($match['match_hour']) : date('H:i', strtotime($match['match_date'])) ?>
                            </small>
                        </div>
                        <div class="text-center flex-fill">
                            <?php if ($match['team_away_path']): ?>
                                <img src="<?= htmlspecialchars($match['team_away_path']) ?>" alt="" class="mb-2" style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="small fw-bold"><?= htmlspecialchars($match['team_away_name'] ?? 'Équipe B') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Derniers résultats -->
<?php if (!empty($derniersResultats)): ?>
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-clock-history text-success"></i> Derniers résultats
        </h2>
    </div>
    <div class="row g-3">
        <?php foreach ($derniersResultats as $match): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span>
                        <?php if ($match['sport_path']): ?>
                            <img src="<?= htmlspecialchars($match['sport_path']) ?>" alt="" class="me-1" style="width: 20px; height: 20px; object-fit: contain;">
                        <?php endif; ?>
                        <?= htmlspecialchars($match['sport_name'] ?? 'Sport') ?>
                    </span>
                    <span class="badge bg-light text-success">Terminé</span>
                </div>
                <div class="card-body">
                    <?php if ($match['match_type_name']): ?>
                        <small class="text-muted d-block mb-2"><?= htmlspecialchars($match['match_type_name']) ?></small>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-center flex-fill">
                            <?php if ($match['team_home_path']): ?>
                                <img src="<?= htmlspecialchars($match['team_home_path']) ?>" alt="" class="mb-2" style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="small fw-bold <?= $match['score_home'] > $match['score_away'] ? 'text-success' : '' ?>">
                                <?= htmlspecialchars($match['team_home_name'] ?? 'Équipe A') ?>
                            </div>
                        </div>
                        <div class="text-center px-3">
                            <div class="display-6 fw-bold">
                                <?= (int)$match['score_home'] ?> - <?= (int)$match['score_away'] ?>
                            </div>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($match['match_date'])) ?></small>
                        </div>
                        <div class="text-center flex-fill">
                            <?php if ($match['team_away_path']): ?>
                                <img src="<?= htmlspecialchars($match['team_away_path']) ?>" alt="" class="mb-2" style="width: 50px; height: 50px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="small fw-bold <?= $match['score_away'] > $match['score_home'] ? 'text-success' : '' ?>">
                                <?= htmlspecialchars($match['team_away_name'] ?? 'Équipe B') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Message si aucun match -->
<?php if (empty($matchsEnCours) && empty($matchsAVenir) && empty($derniersResultats)): ?>
<section class="mb-5">
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle me-2"></i>
        Aucun match disponible pour le moment. Revenez bientôt !
    </div>
</section>
<?php endif; ?>

<!-- Sports disponibles -->
<?php if (!empty($sports)): ?>
<section class="mb-5">
    <h2 class="mb-3">
        <i class="bi bi-grid-fill text-warning"></i> Sports
    </h2>
    <div class="row g-3">
        <?php foreach ($sports as $sport): ?>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body">
                    <?php if ($sport['path']): ?>
                        <img src="<?= htmlspecialchars($sport['path']) ?>" alt="<?= htmlspecialchars($sport['name']) ?>" class="mb-2" style="width: 60px; height: 60px; object-fit: contain;">
                    <?php else: ?>
                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                            <i class="bi bi-trophy fs-4 text-white"></i>
                        </div>
                    <?php endif; ?>
                    <h6 class="card-title mb-0"><?= htmlspecialchars($sport['name']) ?></h6>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Équipes -->
<?php if (!empty($teams)): ?>
<section class="mb-5">
    <h2 class="mb-3">
        <i class="bi bi-people-fill text-info"></i> Équipes
    </h2>
    <div class="row g-3">
        <?php foreach ($teams as $team): ?>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body">
                    <?php if ($team['path']): ?>
                        <img src="<?= htmlspecialchars($team['path']) ?>" alt="<?= htmlspecialchars($team['name']) ?>" class="mb-2" style="width: 60px; height: 60px; object-fit: contain;">
                    <?php else: ?>
                        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                            <i class="bi bi-shield-fill fs-4 text-white"></i>
                        </div>
                    <?php endif; ?>
                    <h6 class="card-title mb-1"><?= htmlspecialchars($team['name']) ?></h6>
                    <?php if ($team['country_name']): ?>
                        <small class="text-muted">
                            <?php if ($team['country_path']): ?>
                                <img src="<?= htmlspecialchars($team['country_path']) ?>" alt="" style="width: 16px; height: 12px; object-fit: cover;">
                            <?php endif; ?>
                            <?= htmlspecialchars($team['country_name']) ?>
                        </small>
                    <?php endif; ?>
                    <div>
                        <span class="badge bg-<?= $team['type'] === 'national' ? 'primary' : 'secondary' ?> small">
                            <?= $team['type'] === 'national' ? 'National' : 'Club' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<style>
    .pulse-animation {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>

<?php include_once __DIR__ . '/include/footer.php'; ?>
