<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$pageTitle = "Matchs";

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * 20;

// Requête avec jointures
$matchs = query("
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
    ORDER BY m.match_date DESC
    LIMIT 20 OFFSET $offset
")->fetchAll();

$count = getCountAll("matchs");
$totalPages = ceil($count / 20);

$statusColors = ['planned' => 'info', 'playing' => 'danger', 'finished' => 'success'];
$statusLabels = ['planned' => 'Planifie', 'playing' => 'En cours', 'finished' => 'Termine'];
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-1">Gestion des Matchs</h1>
        <p class="text-muted mb-0"><?= $count ?> matchs enregistres</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/match_types" class="btn btn-outline-secondary">
            <i class="bi bi-collection me-1"></i> Types de match
        </a>
        <a href="/admin/matchs/create.php" class="btn btn-danger">
            <i class="bi bi-plus-circle me-1"></i> Ajouter un match
        </a>
    </div>
</div>

<?php displayFlash(); ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($matchs)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-event fs-1 mb-3 d-block"></i>
                <p class="mb-3">Aucun match enregistre</p>
                <a href="/admin/matchs/create.php" class="btn btn-danger btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter le premier match
                </a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Match</th>
                        <th>Sport</th>
                        <th>Competition</th>
                        <th>Score</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matchs as $m): ?>
                    <tr id="match-<?= $m['id'] ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <!-- Equipe domicile -->
                                <div class="d-flex align-items-center me-2">
                                    <?php if ($m['team_home_path']): ?>
                                        <img src="<?= htmlspecialchars($m['team_home_path']) ?>" alt=""
                                             class="rounded me-1" style="width: 24px; height: 24px; object-fit: contain;">
                                    <?php endif; ?>
                                    <small class="fw-semibold"><?= htmlspecialchars($m['team_home_name'] ?? 'N/A') ?></small>
                                </div>
                                <span class="text-muted mx-1">vs</span>
                                <!-- Equipe extérieur -->
                                <div class="d-flex align-items-center">
                                    <?php if ($m['team_away_path']): ?>
                                        <img src="<?= htmlspecialchars($m['team_away_path']) ?>" alt=""
                                             class="rounded me-1" style="width: 24px; height: 24px; object-fit: contain;">
                                    <?php endif; ?>
                                    <small class="fw-semibold"><?= htmlspecialchars($m['team_away_name'] ?? 'N/A') ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($m['sport_path']): ?>
                                    <img src="<?= htmlspecialchars($m['sport_path']) ?>" alt=""
                                         class="me-1" style="width: 20px; height: 20px; object-fit: contain;">
                                <?php endif; ?>
                                <small><?= htmlspecialchars($m['sport_name'] ?? 'N/A') ?></small>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted"><?= htmlspecialchars($m['match_type_name'] ?? '-') ?></small>
                        </td>
                        <td>
                            <span class="fw-bold fs-6">
                                <?= (int)$m['score_home'] ?> - <?= (int)$m['score_away'] ?>
                            </span>
                        </td>
                        <td>
                            <small>
                                <?= date('d/m/Y', strtotime($m['match_date'])) ?><br>
                                <span class="text-muted"><?= $m['match_hour'] ?: date('H:i', strtotime($m['match_date'])) ?></span>
                            </small>
                        </td>
                        <td>
                            <?php $status = $m['status'] ?? 'planned'; ?>
                            <span class="badge bg-<?= $statusColors[$status] ?>">
                                <?php if ($status === 'playing'): ?>
                                    <i class="bi bi-circle-fill me-1 small"></i>
                                <?php endif; ?>
                                <?= $statusLabels[$status] ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="/admin/matchs/edit.php?id=<?= $m['id'] ?>"
                                   class="btn btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/matchs/delete.php?id=<?= $m['id'] ?>"
                                   class="btn btn-outline-danger" title="Supprimer"
                                   onclick="return confirm('Supprimer ce match ?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
