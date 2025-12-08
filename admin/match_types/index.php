<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$pageTitle = "Types de Match";

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$match_types = getAll("match_types", $page, 20);
$count = getCountAll("match_types");
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Types de Match</h1>
        <p class="text-muted mb-0"><?= $count ?> types enregistres</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/matchs" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour aux matchs
        </a>
        <a href="/admin/match_types/create.php" class="btn btn-info">
            <i class="bi bi-plus-circle me-1"></i> Ajouter un type
        </a>
    </div>
</div>

<?php displayFlash(); ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($match_types['data'])): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-collection fs-1 mb-3 d-block"></i>
                <p class="mb-3">Aucun type de match enregistre</p>
                <a href="/admin/match_types/create.php" class="btn btn-info btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter le premier type
                </a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 60px;">Logo</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Date d'ajout</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($match_types['data'] as $mt): ?>
                    <tr id="match-type-<?= $mt['id'] ?>">
                        <td>
                            <?php if ($mt['path']): ?>
                                <img src="<?= htmlspecialchars($mt['path']) ?>" alt="<?= htmlspecialchars($mt['name']) ?>"
                                     class="rounded" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-collection text-info"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="fw-semibold"><?= htmlspecialchars($mt['name']) ?></span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= $mt['description'] ? htmlspecialchars(mb_substr($mt['description'], 0, 50)) . (strlen($mt['description']) > 50 ? '...' : '') : '-' ?>
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($mt['created_at'])) ?>
                            </small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="/admin/match_types/edit.php?id=<?= $mt['id'] ?>"
                                   class="btn btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/match_types/delete.php?id=<?= $mt['id'] ?>"
                                   class="btn btn-outline-danger" title="Supprimer"
                                   onclick="return confirm('Supprimer ce type de match ?');">
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

<?php if ($match_types['totalPages'] > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
        </li>
        <?php for ($i = 1; $i <= $match_types['totalPages']; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $match_types['totalPages'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
