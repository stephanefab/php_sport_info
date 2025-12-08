<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$pageTitle = "Pays";

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$countries = getAll("pays", $page, 20);
$count = getCountAll("pays");
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Gestion des Pays</h1>
        <p class="text-muted mb-0"><?= $count ?> pays enregistres</p>
    </div>
    <a href="/admin/countries/create.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Ajouter un pays
    </a>
</div>

<?php displayFlash(); ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($countries['data'])): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-globe-americas fs-1 mb-3 d-block"></i>
                <p class="mb-3">Aucun pays enregistre</p>
                <a href="/admin/countries/create.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter le premier pays
                </a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Nom</th>
                        <th>Code ISO</th>
                        <th>Date d'ajout</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($countries['data'] as $c): ?>
                    <tr id="country-<?= $c['id'] ?>">
                        <td>
                            <?php if ($c['path']): ?>
                                <img src="<?= htmlspecialchars($c['path']) ?>" alt="<?= htmlspecialchars($c['name']) ?>"
                                     class="rounded" style="width: 40px; height: 30px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 30px;">
                                    <i class="bi bi-flag text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="fw-semibold"><?= htmlspecialchars(ucfirst($c['name'])) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars(strtoupper($c['code_iso'])) ?></span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                            </small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="/admin/countries/edit.php?id=<?= $c['id'] ?>"
                                   class="btn btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/countries/delete.php?id=<?= $c['id'] ?>"
                                   class="btn btn-outline-danger" title="Supprimer"
                                   onclick="return confirm('Supprimer le pays <?= htmlspecialchars(addslashes($c['name'])) ?> ?');">
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

<?php if ($countries['totalPages'] > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        <?php for ($i = 1; $i <= $countries['totalPages']; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $countries['totalPages'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
