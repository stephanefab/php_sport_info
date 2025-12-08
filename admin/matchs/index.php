<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$matchs = getAll("matchs", $page, 50);
$count = getCountAll("matchs");
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des matchs (<?= $count; ?>)</h2>
        <a href="/admin/matchs/create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajouter un match
        </a>
        <a href="/admin/match_types" class="btn btn-dark">
            <i class="bi bi-plus-circle"></i> Type de match
        </a>
    </div>
    <?= displayFlash(); ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Sports</th>
                        <th>Match type</th>
                        <th>Status</th>
                        <th>Equipe Home</th>
                        <th>Equipe Away</th>
                        <th>Date</th>
                        <th>-</th>
                        <th>Date ajout</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($matchs['data'] as $c): ?>
                        <tr id="<?= $c['id'] ?>">
                            <td><?= $c['sport_id']; ?></td>
                            <td><?= $c['match_type_id']; ?></td>
                            <td><?= $c['status']; ?></td>
                            <td><?= $c['team_home_id']; ?></td>
                            <td><?= $c['team_away_id']; ?></td>
                            <td><?= $c['match_date']; ?></td>
                            <td><?= $c['created_at'] ?? '-' ?></td>
                            <td class="text-end">
                                <a href="/admin/matchs/edit.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/matchs/delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Supprimer cette match ?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <nav class="mt-4">
        <ul class="pagination justify-content-center">

            <?php for ($i = 1; $i <= $matchs['totalPages']; $i++): ?>
                <li class="page-item <?= ($i == $matchs['currentPage']) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

        </ul>
    </nav>

</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>