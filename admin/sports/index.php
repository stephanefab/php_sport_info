<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$pageTitle = "Sports";

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$sports = getAll("sports", $page, 20);
$count = getCountAll("sports");
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Gestion des Sports</h1>
        <p class="text-muted mb-0"><?= $count ?> sports enregistres</p>
    </div>
    <a href="/admin/sports/create.php" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i> Ajouter un sport
    </a>
</div>

<?php displayFlash(); ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($sports['data'])): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-dribbble fs-1 mb-3 d-block"></i>
                <p class="mb-3">Aucun sport enregistre</p>
                <a href="/admin/sports/create.php" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter le premier sport
                </a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 60px;">Logo</th>
                        <th>Nom</th>
                        <th>Date d'ajout</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sports['data'] as $s): ?>
                    <tr id="sport-<?= $s['id'] ?>">
                        <td>
                            <?php if ($s['path']): ?>
                                <img src="<?= htmlspecialchars($s['path']) ?>" alt="<?= htmlspecialchars($s['name']) ?>"
                                     class="rounded" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-success bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-dribbble text-success"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="fw-semibold"><?= htmlspecialchars(ucfirst($s['name'])) ?></span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($s['created_at'])) ?>
                            </small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="/admin/sports/edit.php?id=<?= $s['id'] ?>"
                                   class="btn btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/sports/delete.php?id=<?= $s['id'] ?>"
                                   class="btn btn-outline-danger" title="Supprimer"
                                   onclick="return confirm('Supprimer le sport <?= htmlspecialchars(addslashes($s['name'])) ?> ?');">
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

<?php if ($sports['totalPages'] > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
        </li>
        <?php for ($i = 1; $i <= $sports['totalPages']; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $sports['totalPages'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
